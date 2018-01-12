<?php

namespace YWH\Encryptable;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\LoadClassMetadataEventArgs;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\Mapping\ClassMetadata;
use Gedmo\Mapping\Event\AdapterInterface;
use Gedmo\Mapping\MappedEventSubscriber;

class EncryptableListener extends MappedEventSubscriber
{
    const ENCRYPT = 'encrypt';
    const DECRYPT = 'decrypt';

    /**
     * @var EncryptorInterface
     */
    protected $encryptor;

    /**
     * EncryptionListener constructor.
     *
     * @param EncryptorInterface $encryptor
     */
    public function __construct(EncryptorInterface $encryptor)
    {
        parent::__construct();

        $this->encryptor = $encryptor;
    }

    /**
     * @return array
     */
    public function getSubscribedEvents()
    {
        return array(
            'loadClassMetadata',
            'prePersist',
            'onFlush',
            'postPersist',
            'postLoad',
            'postUpdate',
        );
    }

    /**
     * Maps additional metadata
     *
     * @param LoadClassMetadataEventArgs $args
     */
    public function loadClassMetadata(LoadClassMetadataEventArgs $args)
    {
        $ea = $this->getEventAdapter($args);
        $meta = $args->getClassMetadata();
        $this->loadMetadataForObjectClass($ea->getObjectManager(), $meta);
    }

    /**
     * Generate encryption key if needed
     *
     * @param LifecycleEventArgs $args
     */
    public function prePersist(LifecycleEventArgs $args)
    {
        $ea = $this->getEventAdapter($args);
        $om = $ea->getObjectManager();
        $object = $ea->getObject();
        $meta = $om->getClassMetadata(get_class($object));

        if ($config = $this->getConfiguration($om, $meta->name)) {
            if ($config['usePassword'] && isset($config['keyField']) &&
                null === $meta->getReflectionProperty($config['keyField'])->getValue($object)
            ) {
                $key = $this->encryptor->generateKey(true);
                $meta->getReflectionProperty($config['keyField'])->setValue($object, $key);
            }
        }
    }

    /**
     * Looks for encryptable objects being inserted or updated
     * for further processing
     *
     * @param OnFlushEventArgs $args
     */
    public function onFlush(OnFlushEventArgs $args)
    {
        $ea  = $this->getEventAdapter($args);
        $om  = $ea->getObjectManager();
        $uow = $om->getUnitOfWork();

        foreach ($ea->getScheduledObjectUpdates($uow) as $object) {
            $meta = $om->getClassMetadata(get_class($object));
            if ($this->getConfiguration($om, $meta->name)) {
                $this->process($ea, $om, $object, self::ENCRYPT);
                $ea->recomputeSingleObjectChangeSet($uow, $meta, $object);
            }
        }

        foreach ($ea->getScheduledObjectInsertions($uow) as $object) {
            $meta = $om->getClassMetadata(get_class($object));
            if ($this->getConfiguration($om, $meta->name)) {
                $this->process($ea, $om, $object, self::ENCRYPT);
                $ea->recomputeSingleObjectChangeSet($uow, $meta, $object);
            }
        }
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function postPersist(LifecycleEventArgs $args)
    {
        $ea = $this->getEventAdapter($args);
        $om = $ea->getObjectManager();
        $object = $ea->getObject();
        $meta = $om->getClassMetadata(get_class($object));

        if ($this->getConfiguration($om, $meta->name)) {
            $this->process($ea, $om, $ea->getObject(), self::DECRYPT);
        }
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function postLoad(LifecycleEventArgs $args)
    {
        $ea = $this->getEventAdapter($args);
        $om = $ea->getObjectManager();
        $object = $ea->getObject();
        $meta = $om->getClassMetadata(get_class($object));

        if ($this->getConfiguration($om, $meta->name)) {
            $this->process($ea, $om, $ea->getObject(), self::DECRYPT);
        }
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function postUpdate(LifecycleEventArgs $args)
    {
        $ea = $this->getEventAdapter($args);
        $om = $ea->getObjectManager();
        $object = $ea->getObject();
        $meta = $om->getClassMetadata(get_class($object));

        if ($this->getConfiguration($om, $meta->name)) {
            $this->process($ea, $om, $ea->getObject(), self::DECRYPT);
        }
    }

    /**
     * Process field
     *
     * @param AdapterInterface $ea
     * @param ObjectManager    $om
     * @param mixed            $entity
     * @param string           $operation
     */
    protected function process(AdapterInterface $ea, ObjectManager $om, $entity, $operation)
    {
        $meta = $om->getClassMetadata(get_class($entity));
        $config = $this->getConfiguration($om, $meta->name);

        $key = $this->getKey($entity, $meta, $config);

        foreach ($config['fields'] as $fieldConfig) {
            $field = $fieldConfig['field'];
            /** @var Type $type */
            $type = $fieldConfig['type'];
            $reflectionProperty = $meta->getReflectionProperty($field);

            $value = $reflectionProperty->getValue($entity);

            if (self::ENCRYPT == $operation) {
                $value = $type->convertToDatabaseValue($value, $om->getConnection()->getDatabasePlatform());
            }

            $newValue = $this->encryptor->$operation($value, $key);

            if (self::DECRYPT === $operation) {
                $newValue = $type->convertToPHPValue($newValue, $om->getConnection()->getDatabasePlatform());
            }

            $reflectionProperty->setValue($entity, $newValue);
        }
    }

    /**
     * Get encryption key
     *
     * @param mixed         $entity
     * @param ClassMetadata $meta
     * @param array         $config
     *
     * @return mixed
     */
    protected function getKey($entity, $meta, $config)
    {
        $oid = spl_object_hash($entity);
        if (!isset($this->keys[$oid][$meta->name])) {
            if ($config['usePassword']) {
                $reflectionProperty = $meta->getReflectionProperty($config['keyField']);
                $this->keys[$oid][$meta->name] = $this->encryptor->getKeyProtectedWithPassword($reflectionProperty->getValue($entity));
            } else {
                $this->keys[$oid][$meta->name] = $this->encryptor->getKey();
            }
        }

        return $this->keys[$oid][$meta->name];
    }

    /**
     * {@inheritDoc}
     */
    protected function getNamespace()
    {
        return __NAMESPACE__;
    }
}