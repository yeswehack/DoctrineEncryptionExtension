<?php

use Doctrine\Common\Annotations\AnnotationRegistry;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\CachedReader;
use Doctrine\Common\Cache\ArrayCache;
use Composer\Autoload\ClassLoader;

if (!class_exists('PHPUnit_Framework_TestCase') ||
    version_compare(PHPUnit_Runner_Version::id(), '3.5') < 0
) {
    die('PHPUnit framework is required, at least 3.5 version');
}

if (!class_exists('PHPUnit_Framework_MockObject_MockBuilder')) {
    die('PHPUnit MockObject plugin is required, at least 1.0.8 version');
}

/** @var $loader ClassLoader */
$loader = require __DIR__ . '/../vendor/autoload.php';

$loader->add('Tool', __DIR__ . '/../vendor/gedmo/doctrine-extensions/tests/Gedmo');
$loader->add('Encryptable\\Fixture', __DIR__ . '/YWH');

AnnotationRegistry::registerLoader([$loader, 'loadClass']);

$reader                    = new AnnotationReader();
$reader                    = new CachedReader($reader, new ArrayCache());

$_ENV['annotation_reader'] = $reader;