<?php

$autoloader = require_once('vendor/autoload.php');

$autoloader->setPsr4(
    'bioengine\\',
    BIOENGINE_PATH
);
return $autoloader;