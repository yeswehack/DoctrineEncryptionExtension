<?php
/**
 * This entity manager configuration works with doctrine 2.1.x and 2.2.x
 * versions. Regarding AnnotationDriver setup it most probably will be changed into
 * xml. Because annotation driver fails to read other classes in same namespace
 */
// connection args, modify at will
$connection = array(
    'host' => '127.0.0.1',
    'port' => 3306,
    'user' => 'root',
    'password' => 'root',
    'dbname' => 'test',
    'driver' => 'pdo_mysql',
);
$keyFile = __DIR__.'/encryptable.key';
if (!file_exists(__DIR__.'/../vendor/autoload.php')) {
    die('cannot find vendors, read README.md how to use composer');
}
if (!file_exists($keyFile)) {
    die("you need to create thie file $keyFile with a valid key (see bin/generate-defuse-key)");
}
// First of all autoloading of vendors
$loader = require __DIR__.'/../vendor/autoload.php';

// ywh extensions
$loader->add('YWH', __DIR__.'/../lib');

// autoloader for Entity namespace
$loader->add('Entity', __DIR__.'/app');
$loader->add('Yaml', __DIR__.'/app');

// ensure standard doctrine annotations are registered
Doctrine\Common\Annotations\AnnotationRegistry::registerFile(
    __DIR__.'/../vendor/doctrine/orm/lib/Doctrine/ORM/Mapping/Driver/DoctrineAnnotations.php'
);

// Second configure ORM
// globally used cache driver, in production use APC or memcached
$cache = new Doctrine\Common\Cache\ArrayCache();
// standard annotation reader
$annotationReader = new Doctrine\Common\Annotations\AnnotationReader();
$cachedAnnotationReader = new Doctrine\Common\Annotations\CachedReader(
    $annotationReader, // use reader
    $cache // and a cache driver
);
// create a driver chain for metadata reading
$driverChain = new Doctrine\Common\Persistence\Mapping\Driver\MappingDriverChain();
// load superclass metadata mapping only, into driver chain
// also registers Gedmo annotations.NOTE: you can personalize it
Gedmo\DoctrineExtensions::registerAbstractMappingIntoDriverChainORM(
    $driverChain, // our metadata driver chain, to hook into
    $cachedAnnotationReader // our cached annotation reader
);
YWH\DoctrineExtensions::registerAnnotations();

// now we want to register our application entities,
// for that we need another metadata driver used for Entity namespace
$annotationDriver = new Doctrine\ORM\Mapping\Driver\AnnotationDriver(
    $cachedAnnotationReader, // our cached annotation reader
    array(__DIR__.'/app/Entity') // paths to look in
);
$yamlDriver = new Doctrine\ORM\Mapping\Driver\YamlDriver(
    array(__DIR__.'/app/Yaml')
);
// NOTE: driver for application Entity can be different, Yaml, Xml or whatever
// register annotation driver for our application Entity fully qualified namespace
$driverChain->addDriver($annotationDriver, 'Entity');
$driverChain->addDriver($yamlDriver, 'Yaml');

// general ORM configuration
$config = new Doctrine\ORM\Configuration();
$config->setProxyDir(sys_get_temp_dir());
$config->setProxyNamespace('Proxy');
$config->setAutoGenerateProxyClasses(false); // this can be based on production config.
// register metadata driver
$config->setMetadataDriverImpl($driverChain);
// use our allready initialized cache driver
$config->setMetadataCacheImpl($cache);
$config->setQueryCacheImpl($cache);

// Third, create event manager and hook prefered extension listeners
$evm = new Doctrine\Common\EventManager();

// encryptable
$safeKey = file_get_contents($keyFile);

$encryptor = new \YWH\Encryptable\Encryptor\DefuseEncryptor('password', $safeKey);
$encryptableListener = new YWH\Encryptable\EncryptableListener($encryptor);
// you should set the used annotation reader to listener, to avoid creating new one for mapping drivers
$encryptableListener->setAnnotationReader($cachedAnnotationReader);
$evm->addEventSubscriber($encryptableListener);

// mysql set names UTF-8 if required
$evm->addEventSubscriber(new Doctrine\DBAL\Event\Listeners\MysqlSessionInit());
// Finally, create entity manager
return Doctrine\ORM\EntityManager::create($connection, $config, $evm);
