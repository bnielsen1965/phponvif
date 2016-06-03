<?php
/**
 * Framework configuration file.
 * 
 * These settings normally do not need to be changed unless you have modified
 * the framework or the auto magic location finder is not working.
 */

// auto magically figure out application location
$sitePath   = dirname($_SERVER['SCRIPT_FILENAME']) . '/';
$urlDomain  = $_SERVER['HTTP_HOST'];
$urlPath    = '/' . trim(str_replace('\\', '/', pathinfo($_SERVER['PHP_SELF'], PATHINFO_DIRNAME)), '/') . '/';
$siteURL    = 'http' . (!empty($_SERVER['HTTPS']) ? 's' : '') . '://' . $urlDomain . $urlPath;


$settings = array(
    'sitePath'          => $sitePath,
    'preprocessPath'    => $sitePath . 'preprocess/',
    'layoutPath'        => $sitePath . 'layout/',
    'viewPath'          => $sitePath . 'view/',
    'includePath'       => $sitePath . 'include/',
    'vendorPath'        => $sitePath . 'vendor/',
    'cachePath'         => $sitePath . 'cache/',
    
    'urlDomain'         => $urlDomain,
    'urlPath'           => $urlPath,
    'siteURL'           => $siteURL,
    'cssURL'            => $siteURL . 'css/',
    'jsURL'             => $siteURL . 'js/',
    'imageURL'          => $siteURL . 'image/',
);
