<?php

namespace Encryptable\Fixture;

use Doctrine\ORM\Mapping as ORM;
use YWH\Encryptable\Mapping\Annotation as YWH;

/**
 * @ORM\Entity
 *
 * @YWH\Encryptable()
 */
class Article
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(name="encryption_key", type="text")
     *
     * @YWH\EncryptionKey
     */
    private $encryptionKey;

    /**
     * @ORM\Column(name="title", type="text")
     *
     * @YWH\Encrypted(type="string")
     */
    private $title;

    public function getId()
    {
        return $this->id;
    }

    public function setEncryptionKey($key)
    {
        $this->encryptionKey = $key;
    }

    public function getEncryptionKey()
    {
        return $this->encryptionKey;
    }

    public function setTitle($title)
    {
        $this->title = $title;
    }

    public function getTitle()
    {
        return $this->title;
    }
}