<?php

namespace YWH\Encryptable\Traits;

/**
 * Trait EncryptableKey
 *
 * @author Romain Honel <r.honel@yeswehack.com>
 * @author Maxime Bouchard <m.bouchard@yeswehack.com>
 */
trait EncryptableKey
{
    /**
     * @var string
     */
    protected $encryptionKey;

    /**
     * Set encryption key
     *
     * @param string $key
     *
     * @return $this
     */
    public function setEncryptionKey($key)
    {
        $this->encryptionKey = $key;

        return $this;
    }

    /**
     * Get encryption key
     *
     * @return string
     */
    public function getEncryptionKey()
    {
        return $this->encryptionKey;
    }
}