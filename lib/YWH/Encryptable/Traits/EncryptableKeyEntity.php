<?php

namespace YWH\Encryptable\Traits;

use Doctrine\ORM\Mapping as ORM;
use YWH\Encryptable\Mapping\Annotation as YWH;

/**
 * Trait EncryptableKeyEntity
 *
 * @author Romain Honel <r.honel@yeswehack.com>
 * @author Maxime Bouchard <m.bouchard@yeswehack.com>
 */
trait EncryptableKeyEntity
{
    /**
     * @var string
     *
     * @ORM\Column(name="encryption_key", type="text")
     *
     * @YWH\EncryptionKey
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