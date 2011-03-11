<?php
require_once dirname(__FILE__) . '/config.php';

$site = Zend_Registry::get('site');

$application = $site->getInstance( dirname(__FILE__) . '/..' );
Zend_Registry::set('application', $application);

$configuration = $application->getConfiguration();

if (empty($configuration['endpoint'])) {
    $configuration['endpoint'] = 'http://twitter.com/oauth';
}

if (Zend_Registry::isRegistered('cache')) {
    $cache = Zend_Registry::get('cache');
}
