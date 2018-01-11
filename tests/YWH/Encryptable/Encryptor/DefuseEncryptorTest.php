<?php

namespace YWH\Encryptable\Encryptor;

use Defuse\Crypto\Key;
use Defuse\Crypto\KeyProtectedByPassword;

class DefuseEncryptorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var string
     */
    private $password;

    /**
     * @var string
     */
    private $key;

    /**
     * @var string
     */
    private $keyProtected;

    /**
     * @var DefuseEncryptor
     */
    private $encryptor;

    public function setUp()
    {
        parent::setUp();

        $this->password = bin2hex(random_bytes(32));
        $this->key = Key::createNewRandomKey()->saveToAsciiSafeString();
        $this->keyProtected = KeyProtectedByPassword::createRandomPasswordProtectedKey($this->password)->saveToAsciiSafeString();
        $this->encryptor = new DefuseEncryptor($this->password, $this->key);
    }

    /**
     * @expectedException \ArgumentCountError
     */
    public function testMissingConstructorParams()
    {
        new DefuseEncryptor();
    }

    public function testSimpleEncryption()
    {
        $data = 'Encrypted data';
        $key = $this->encryptor->getKey();

        $encrypted = $this->encryptor->encrypt($data, $key);

        $this->assertNotSame($data, $encrypted);

        $decrypted = $this->encryptor->decrypt($encrypted, $key);

        $this->assertSame($data, $decrypted);
    }

    public function testPasswordProtectedEncryption()
    {
        $data = 'Encrypted data';
        $key = $this->encryptor->getKeyProtectedWithPassword($this->keyProtected);

        $encrypted = $this->encryptor->encrypt($data, $key);

        $this->assertNotSame($data, $encrypted);

        $decrypted = $this->encryptor->decrypt($encrypted, $key);

        $this->assertSame($data, $decrypted);
    }
}