<?php

use Symfony\Component\ClassLoader\UniversalClassLoader;

$loader = new UniversalClassLoader();
$loader->registerNamespaces(array(
    'Symfony'          => array(__DIR__.'/../../../../../vendor/symfony/src', __DIR__.'/../../../../../vendor/bundles'),
    'Socloz'                   => __DIR__.'/../../../../',
));
$loader->register();

