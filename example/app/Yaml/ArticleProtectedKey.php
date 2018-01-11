<?php

namespace Yaml;

/**
 * Class ArticleProtectedKey
 */
class ArticleProtectedKey
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $title;

    /**
     * @var string
     */
    private $encryptionKey;

    /**
     * @var string
     */
    private $encrypted;

    public function getId()
    {
        return $this->id;
    }

    public function setTitle($title)
    {
        $this->title = $title;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function setEncrypted($encrypted)
    {
        $this->encrypted = $encrypted;
    }

    public function getEncrypted()
    {
        return $this->encrypted;
    }

    public function setEncryptionKey($encryptionKey)
    {
        $this->encryptionKey = $encryptionKey;
    }

    public function getEncryptionKey()
    {
        return $this->encryptionKey;
    }
}
