<?php

namespace YWH\Encryptable;

use Defuse\Crypto\Key;
use Doctrine\Common\EventManager;
use Encryptable\Fixture\Article;
use Tool\BaseTestCaseORM;
use YWH\Encryptable\Encryptor\DefuseEncryptor;

class EncryptableTest extends BaseTestCaseORM
{
    const ARTICLE = "Encryptable\\Fixture\\Article";

    const PASSWORD = 'password';

    private $key;

    /**
     * @var DefuseEncryptor
     */
    private $encryptor;

    protected function setUp()
    {
        parent::setUp();

        $this->key = Key::createNewRandomKey();

        $this->encryptor = new DefuseEncryptor(self::PASSWORD, $this->key);

        $evm = new EventManager();
        $evm->addEventSubscriber(new EncryptableListener($this->encryptor));

        $this->getMockSqliteEntityManager($evm);
    }

    public function testGenerateKey()
    {
        $article = new Article();
        $article->setTitle('Test');

        $this->assertNull($article->getEncryptionKey());

        $this->em->persist($article);
        $this->em->flush();
        $this->em->clear();

        $insertedId = $article->getId();

        $article = $this->em->getRepository(self::ARTICLE)->find($insertedId);

        $this->assertNotNull($article->getEncryptionKey());
    }

    public function testProcess()
    {
        $article = new Article();
        $article->setTitle('Test');

        $this->em->persist($article);
        $this->em->flush();
        $this->em->clear();

        $insertedId = $article->getId();

        $article = $this->em->getRepository(self::ARTICLE)->find($insertedId);

        $this->assertSame($article->getTitle(), 'Test');
    }

    public function testProcessOnNullField()
    {
        $article = new Article();
        $article->setTitle(null);

        $this->em->persist($article);
        $this->em->flush();
        $this->em->clear();

        $insertedId = $article->getId();

        $article = $this->em->getRepository(self::ARTICLE)->find($insertedId);

        $this->assertNull($article->getTitle());
    }

    public function testEncryption()
    {
        $article = new Article();
        $article->setTitle('Test');

        $this->em->persist($article);
        $this->em->flush();
        $this->em->clear();

        $insertedId = $article->getId();

        $query = $this->em
            ->createQuery('SELECT a.title, a.encryptionKey FROM '.self::ARTICLE.' a WHERE a.id = :id')
            ->setParameter('id', $insertedId)
        ;
        $result = $query->getScalarResult()[0];

        $this->assertNotSame($result['title'], 'Test');

        $key = $this->encryptor->getKeyProtectedWithPassword($result['encryptionKey']);
        $this->assertSame($this->encryptor->decrypt($result['title'], $key), 'Test');
    }

    protected function getUsedEntityFixtures()
    {
        return array(
            self::ARTICLE,
        );
    }
}