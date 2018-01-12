# Doctrine2 encryption extension

This extension allows to encrypt entites fields using [php-encryption](https://github.com/defuse/php-encryption).

This extension is based on and use **Doctrine2 behavioral extensions**.

## Setup and autoloading

Read the [documentation](http://github.com/Atlantic18/DoctrineExtensions/blob/master/doc/annotations.md#em-setup)
or check the [example code](http://github.com/Atlantic18/DoctrineExtensions/tree/master/example)
on how to setup and use the extensions in most optimized way.

## Encryptable Entity example:

### Encryptable annotations:
1. **@YWH\Encryptable\Mapping\Annotation\Encryptable** this class annotation tells if a class is Encryptable. Available configuration options:
    * **usePassword** does the key is protected with a password ? Default: true
2. **@YWH\Encryptable\Mapping\Annotation\EncryptionKey** this annotation tells that this column will contain the encryption key use to encrypt data. Only necessary when using a key protected by password
3. **@YWH\Encryptable\Mapping\Annotation\Encrypted** this annotation tells that this column will be encrypted. Available configuration options:
    * **type** the data type. Should one of the valid [doctrine types](http://docs.doctrine-project.org/projects/doctrine-dbal/en/latest/reference/types.html) Default: string

### Notes about setting the encryption key and encrypted data columns:

**EncryptionKey** and **Encrypted** columns must be set as **string** type

``` php
    /**
     * @var string
     *
     * @ORM\Column(name="encryption_key", type="text")
     * @YWH\EncryptionKey()
     */
    private $encryptionKey;

    /**
     * @ORM\Column(name="encrypted_data", type="string")
     * @YWH\Encrypted()
     */
    private $encryptedData;
```

``` php
<?php
namespace Entity;

use Doctrine\ORM\Mapping as ORM;
use YWH\Encryptable\Mapping\Annotation as YWH;

/**
 * @ORM\Entity
 * @YWH\Encryptable(usePassword=true)
 */
class Article
{
    /** @ORM\Id @ORM\GeneratedValue @ORM\Column(type="integer") */
    private $id;
    
    /**
     * @var string
     *
     * @ORM\Column(name="encryption_key", type="text")
     * @YWH\EncryptionKey()
     */
    private $encryptionKey;

    /**
     * @ORM\Column(type="string", length=128)
     * @YWH\Encrypted()
     */
    private $title;

    /**
     * @ORM\Column(name="date", type="string")
     * @YWH\Encrypted(type="datetime")
     */
    private $date;

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

    public function setDate(\DateTime $date)
    {
        $this->date = $date;
    }

    public function getDate()
    {
        return $this->date;
    }
}
```