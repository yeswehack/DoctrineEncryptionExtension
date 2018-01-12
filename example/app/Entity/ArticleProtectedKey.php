<?php

namespace Entity;

use Doctrine\ORM\Mapping as ORM;
use YWH\Encryptable\Mapping\Annotation as YWH;

/**
 * @ORM\Table(name="article_protected_key")
 * @ORM\Entity()
 *
 * @YWH\Encryptable(usePassword=true)
 */
class ArticleProtectedKey
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue
     */
    private $id;

    /**
     * @ORM\Column(name="title", type="string", length=255)
     */
    private $title;

    /**
     * @ORM\Column(name="encryption_key", type="text")
     *
     * @YWH\EncryptionKey
     */
    private $encryptionKey;

    /**
     * @ORM\Column(name="encrypted", type="text")
     *
     * @YWH\Encrypted(type="string")
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
