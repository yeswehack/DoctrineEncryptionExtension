<?php

namespace YWH;

use Doctrine\Common\Annotations\AnnotationRegistry;

/**
 * Class DoctrineExtensions
 *
 * @author Romain Honel <r.honel@yeswehack.com>
 * @author Maxime Bouchard <m.bouchard@yeswehack.com>
 */
final class DoctrineExtensions
{
    /**
     * Include all annotations
     */
    public static function registerAnnotations():void
    {
        AnnotationRegistry::registerFile(__DIR__.'/Encryptable/Mapping/Annotation/All.php');
    }
}
