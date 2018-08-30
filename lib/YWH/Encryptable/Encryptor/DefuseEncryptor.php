<?php

namespace YWH\Encryptable\Encryptor;

use Defuse\Crypto\Crypto;
use Defuse\Crypto\Exception\EnvironmentIsBrokenException;
use Defuse\Crypto\Exception\WrongKeyOrModifiedCiphertextException;
use Defuse\Crypto\Key;
use Defuse\Crypto\KeyProtectedByPassword;
use YWH\Encryptable\EncryptorInterface;

/**
 * Class DefuseEncryptor
 *
 * @author Romain Honel <r.honel@yeswehack.com>
 * @author Maxime Bouchard <m.bouchard@yeswehack.com>
 */
class DefuseEncryptor implements EncryptorInterface
{
    /**
     * @var string
     */
    protected $password;

    /**
     * @var string
     */
    protected $key;

    /**
     * DefuseEncryptor constructor.
     *
     * @param string $password
     * @param string $key
     */
    public function __construct($password, $key)
    {
        $this->password = $password;
        $this->key = $key;
    }

    /**
     * {@inheritdoc}
     */
    public function encrypt($data, Key $key)
    {
        if (null === $data) {
            return null;
        }

        try {
            return Crypto::encrypt($data, $key);
        } catch (EnvironmentIsBrokenException $e) {
            return (string) $data;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function decrypt($data, Key $key)
    {
        if (null === $data) {
            return null;
        }

        try {
            return Crypto::decrypt($data, $key);
        } catch (EnvironmentIsBrokenException $e) {
            return (string) 'Corrupted data';
        } catch (WrongKeyOrModifiedCiphertextException $e) {
            return (string) 'Corrupted data';
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getKey($safeString = null)
    {
        return Key::loadFromAsciiSafeString($safeString ? $safeString : $this->key);
    }

    /**
     * {@inheritdoc}
     */
    public function getKeyProtectedWithPassword($encodedKey)
    {
        try {
            $key = KeyProtectedByPassword::loadFromAsciiSafeString($encodedKey);

            return $key->unlockKey($this->password);
        } catch (WrongKeyOrModifiedCiphertextException $e) {
            return Key::createNewRandomKey();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function generateKey($withPasswordProtection = false)
    {
        if (true === $withPasswordProtection) {
            $key = KeyProtectedByPassword::createRandomPasswordProtectedKey($this->password);
        } else {
            $key = Key::createNewRandomKey();
        }

        return $key->saveToAsciiSafeString();
    }
}