<?php
if (!$loader = @include __DIR__.'/../vendor/autoload.php') {
    echo <<<EOM
You must set up the project dependencies by running the following commands:

    curl -s http://getcomposer.org/installer | php
    php composer.phar install

EOM;

    exit(1);
}

$loader->add('Testo\Tests', __DIR__);
$loader->add('Testo\Examples', __DIR__.'/../examples');

\Testo\Phpunit\ReGenerateDocsOnSuccessListener::setRootDir(realpath(__DIR__.'/../'));