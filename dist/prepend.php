<?php
require_once dirname(__FILE__) . '/config.php';

$site = Zend_Registry::get('site');

$application = $site->getInstance( dirname(__FILE__) . '/..' );
Zend_Registry::set('application', $application);

$configuration = $application->getConfiguration();

$cacheDirectory = LD_TMP_DIR . '/cache/';
Ld_Files::createDirIfNotExists($cacheDirectory);
if (file_exists($cacheDirectory) && is_writable($cacheDirectory)) {
    $frontendOptions = array('lifetime' => 60, 'automatic_serialization' => true);
    $backendOptions = array('cache_dir' => $cacheDirectory);
    $cache = Zend_Cache::factory('Core', 'File', $frontendOptions, $backendOptions);
    Zend_Registry::set('cache', $cache);
}