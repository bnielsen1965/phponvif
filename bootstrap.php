<?php

namespace NoCon\Framework;

include_once 'vendor/NoCon/Framework/Config.php';
Config::setPath(__DIR__ . '/config/');

// determine the autoloader to use
if ( file_exists(Config::get('framework', 'vendorPath') . '/autoload.php') ) {
    // using the auto loader from composer
    include_once(Config::get('framework', 'vendorPath') . '/autoload.php');
}
else
{
    // using the default NoCon auto loader
    include Config::get('framework', 'includePath') . 'autoload.php';
}

date_default_timezone_set(Config::get('application', 'timezone'));

// start user session
session_name(Config::get('application', 'sessionName'));
session_start();
