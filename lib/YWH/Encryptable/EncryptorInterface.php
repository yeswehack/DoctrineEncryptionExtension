<?php

namespace YWH\Encryptable;

use Defuse\Crypto\Key;

/**
 * Interface EncryptorInterface
 *
 * @author Romain Honel <r.honel@yeswehack.com>
 * @author Maxime Bouchard <m.bouchard@yeswehack.com>
 */
interface EncryptorInterface
{
    /**
     * Encrypt data using an encryption key
     *
     * @param string $data
     * @param Key    $key
     *
     * @return mixed
     */
    public function encrypt($data, Key $key);

    /**
     * Decrypt data encrypted with an encryption key
     *
     * @param string $data
     * @param Key    $key
     *
     * @return mixed
     */
    public function decrypt($data, Key $key);

    /**
     * Load an encryption key from a safe string
     *
     * @return Key
     */
    public function getKey($safeString = null);

    /**
     * Load an encryption key protected with password from a safe string and unlock it
     *
     * @param string $encodedKey
     *
     * @return Key
     */
    public function getKeyProtectedWithPassword($encodedKey);

    /**
     * Generate a safe string key
     *
     * @param bool|null $withPasswordProtection
     *
     * @return string
     */
    public function generateKey($withPasswordProtection = false);
}