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

		$this->handleRewrite();

		Ld_Files::denyAccess($this->getAbsolutePath() . '/views', true);
	}

	/* Move */

	public function postMove()
	{
		$this->handleRewrite();
	}

	/* Update */

	public function postUpdate()
	{
		$this->handleRewrite();

		Ld_Files::denyAccess($this->getAbsolutePath() . '/views', true);
	}

	/* Uninstall */

	public function postUninstall()
	{
		if (defined('LD_NGINX') && constant('LD_NGINX')) {
			$nginxDir = $this->getSite()->getDirectory('dist') . '/nginx';
			Ld_Files::rm($nginxDir . "/" . $this->getInstance()->getId() . ".conf");
		}
	}

	/* Roles */

	public $roles = array('administrator', 'visitor');

	public $defaultRole = 'visitor';

	/* App Management */

	public function setConfiguration($configuration)
	{
		$configuration = array_merge($this->getConfiguration(), $configuration);
		return parent::setConfiguration($configuration);
	}

	/* Install Utilities */

	public function handleRewrite()
	{
		if (defined('LD_REWRITE') && constant('LD_REWRITE')) {
			$path = $this->getSite()->getPath() . '/' . $this->getPath() . '/';
			$htaccess  = "RewriteEngine on\n";
			$htaccess .= "RewriteBase $path\n";
			$htaccess .= "RewriteCond %{REQUEST_FILENAME} !-f\n";
			$htaccess .= "RewriteCond %{REQUEST_FILENAME} !-d\n";
			$htaccess .= "RewriteRule ^(.*)$ index.php?/$1 [QSA,L]\n";
			Ld_Files::put($this->getAbsolutePath() . "/.htaccess", $htaccess);
		}
		if (defined('LD_NGINX') && constant('LD_NGINX')) {
			// Generate configuration
			$path = $this->getSite()->getPath() . '/' . $this->getPath() . '/';
			$nginxConf  = 'location {PATH} {' . "\n";
			$nginxConf .= '  if (!-e $request_filename) {' . "\n";
			$nginxConf .= '   rewrite ^{PATH}(.*)$  {PATH}index.php?uri=$1 last;' . "\n";
			$nginxConf .= '  }' . "\n";
			$nginxConf .= '}' . "\n";
			$nginxConf = str_replace('{PATH}', $path, $nginxConf);
			// Write configuration
			$nginxDir = $this->getSite()->getDirectory('dist') . '/nginx';
			Ld_Files::ensureDirExists($nginxDir);
			Ld_Files::put($nginxDir . "/" . $this->getInstance()->getId() . ".conf", $nginxConf);
		}
	}

	/* Links */

	public function getTwitterUsername()
	{
		$configuration = $this->getInstance()->getConfiguration();
		if (empty($configuration['access_token'])) {
			return null;
		}
		$accessToken = unserialize($configuration['access_token']);
		return $accessToken->screen_name;
	}

	public function getLinks()
	{
		$links = parent::getLinks();
		if ($username = $this->getTwitterUsername()) {
			if (Ld_Auth::isAuthenticated()) {
				$links[] = array(
					'id'  => 'home_timeline',
					'title' => 'Home timeline',
					'rel'   => 'personal-feed',
					'href'  => $this->getInstance()->getAbsoluteUrl("/feed/timeline"),
					'type'  => 'application/atom+xml'
				);
			}
			$links[] = array(
				'id'  => 'user_timeline',
				'title' => "@$username timeline",
				'rel'   => 'public-feed',
				'href'  => $this->getInstance()->getAbsoluteUrl("/feed/$username"),
				'type'  => 'application/atom+xml'
			);
			$links[] = array(
				'id'  => 'user_mentions',
				'title' => "@$username mentions",
				'rel'   => 'feed',
				'href'  => $this->getInstance()->getAbsoluteUrl("/feed/$username/mentions"),
				'type'  => 'application/atom+xml'
			);
		}
		return $links;
	}

}
