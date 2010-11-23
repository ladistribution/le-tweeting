<?php

class Ld_Installer_LeTweeting extends Ld_Installer
{

	/* Install */

	public function postInstall($preferences = array())
	{
		if (isset($preferences['administrator'])) {
			$username = $preferences['administrator']['username'];
			$this->setUserRoles(array($username => 'administrator'));
		}

		$this->writeHtaccess();
	}

	public function postMove()
	{
		$this->writeHtaccess();
	}

	/* Roles */

	public $roles = array('administrator', 'visitor');

	public $defaultRole = 'visitor';

	/* Colors */

	public $colorSchemes = array('base', 'bars', 'panels');

	/* App Management */

	public function setConfiguration($configuration)
	{
		$configuration = array_merge($this->getConfiguration(), $configuration);
		return parent::setConfiguration($configuration);
	}

	/* Install Utilities */

	public function writeHtaccess()
	{
		if (defined('LD_REWRITE') && constant('LD_REWRITE')) {
			$path = $this->getSite()->getBasePath() . '/' . $this->getPath() . '/';
			$htaccess  = "RewriteEngine on\n";
			$htaccess .= "RewriteBase $path\n";
			$htaccess .= "RewriteCond %{REQUEST_FILENAME} !-f\n";
			$htaccess .= "RewriteCond %{REQUEST_FILENAME} !-d\n";
			$htaccess .= "RewriteRule ^(.*)$ index.php?/$1 [QSA,L]\n";
			Ld_Files::put($this->getAbsolutePath() . "/.htaccess", $htaccess);
		}
	}

}
