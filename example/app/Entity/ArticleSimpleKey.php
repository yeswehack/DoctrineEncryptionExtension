<?php

namespace Entity;

use Doctrine\ORM\Mapping as ORM;
use YWH\Encryptable\Mapping\Annotation as YWH;

/**
 * @ORM\Table(name="article_simple_key")
 * @ORM\Entity()
 *
 * @YWH\Encryptable(usePassword=false)
 */
class ArticleSimpleKey
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
     * @ORM\Column(name="encrypted", type="text")
     *
     * @YWH\Encrypted(type="string", type="string")
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
}
