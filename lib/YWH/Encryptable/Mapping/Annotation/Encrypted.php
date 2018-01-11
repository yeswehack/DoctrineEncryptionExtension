<?php

namespace YWH\Encryptable\Mapping\Annotation;

use Doctrine\Common\Annotations\Annotation;

/**
 * Class Encrypted
 *
 * @Annotation
 * @Target("PROPERTY")
 *
 * @author Romain Honel <r.honel@yeswehack.com>
 * @author Maxime Bouchard <m.bouchard@yeswehack.com>
 */
final class Encrypted
{
    /** @var string */
    public $type = 'string';
}