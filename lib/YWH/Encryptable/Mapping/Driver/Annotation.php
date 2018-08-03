<?php

namespace YWH\Encryptable\Mapping\Driver;

use Doctrine\DBAL\Types\Type;
use Gedmo\Exception\InvalidMappingException;
use Gedmo\Mapping\Driver\AbstractAnnotationDriver;

/**
 * Class Annotation
 *
 * @author Romain Honel <r.honel@yeswehack.com>
 * @author Maxime Bouchard <m.bouchard@yeswehack.com>
 */
class Annotation extends AbstractAnnotationDriver
{
    const ENCRYPTABLE = 'YWH\Encryptable\Mapping\Annotation\Encryptable';
    const ENCRYPTED = 'YWH\Encryptable\Mapping\Annotation\Encrypted';
    const ENCRYPTION_KEY = 'YWH\Encryptable\Mapping\Annotation\EncryptionKey';

    protected $validFieldTypes = array(
        'text',
    );

    public function readExtendedMetadata($meta, array &$config)
    {
        $class = $this->getMetaReflectionClass($meta);

        if ($annotation = $this->reader->getClassAnnotation($class, self::ENCRYPTABLE)) {
            $config['usePassword'] = $annotation->usePassword;
            $config['encryptKey'] = $annotation->encryptKey;

            foreach ($class->getProperties() as $property) {
                if ($meta->isMappedSuperclass && !$property->isPrivate() ||
                    $meta->isInheritedField($property->name) ||
                    isset($meta->associationMappings[$property->name]['inherited'])
                ) {
                    continue;
                }

                // Encrypted fields
                if ($fieldAnnotation = $this->reader->getPropertyAnnotation($property, self::ENCRYPTED)) {
                    $field = $property->getName();
                    if (!$meta->hasField($field)) {
                        throw new InvalidMappingException("Unable to find 'encrypted field' - [{$field}] as mapped property in entity - {$meta->name}");
                    }
                    if (!$this->isValidFieldType($meta, $field)) {
                        throw new InvalidMappingException("Encrypted field - [{$field}] type is not valid and must be 'text' in entity - {$meta->name}");
                    }
                    $config['fields'][] = array(
                        'field' => $field,
                        'type' => Type::getType($fieldAnnotation->type),
                    );
                }

                // Encryption key
                if ($fieldAnnotation = $this->reader->getPropertyAnnotation($property, self::ENCRYPTION_KEY)) {
                    $field = $property->getName();
                    if (!$meta->hasField($field)) {
                        throw new InvalidMappingException("Unable to find 'encryption key field' - [{$field}] as mapped property in entity - {$meta->name}");
                    }
                    if (!$this->isValidFieldType($meta, $field)) {
                        throw new InvalidMappingException("Encryption key field - [{$field}] type is not valid and must be 'text' in entity - {$meta->name}");
                    }
                    $config['keyField'] = $field;
                }
            }

            if ($config['usePassword'] && $config['encryptKey']) {
                throw new InvalidMappingException("You cannot use both key protected by password and key encrypted in entity - {$meta->name}");
            }

            if ($config['encryptKey'] && !isset($config['keyField'])) {
                throw new InvalidMappingException("You need to map a key field as the encryption key to use a encrypted key in entity - {$meta->name}");
            }

            if ($config['usePassword'] && !isset($config['keyField'])) {
                throw new InvalidMappingException("You need to map a key field as the encryption key to use a key protected by password in entity - {$meta->name}");
            }
        }
    }

    private function isValidFieldType($meta, $field)
    {
        $mapping = $meta->getFieldMapping($field);

        return $mapping && in_array($mapping['type'], $this->validFieldTypes);
    }
}