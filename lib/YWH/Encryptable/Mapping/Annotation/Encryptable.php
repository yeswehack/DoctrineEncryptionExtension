<?php

namespace YWH\Encryptable\Mapping\Annotation;

use Doctrine\Common\Annotations\Annotation;

/**
 * Class Encryptable
 *
 * @Annotation
 * @Target("CLASS")
 *
 * @author Romain Honel <r.honel@yeswehack.com>
 * @author Maxime Bouchard <m.bouchard@yeswehack.com>
 */
final class Encryptable
{
    /** @var bool */
    public $usePassword = true;
}