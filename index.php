<?php

require_once dirname(__FILE__) . '/dist/prepend.php';

require_once 'limonade.php';

function configure()
{
    global $site, $application;
    option('base_uri',      $site->getPath() . '/' . $application->getPath() );
    option('session',       false);
    error_reporting(E_ALL);
    if (define('LD_DEBUG') && constant('LD_DEBUG')) {
        option('debug',         true);
        option('env',           ENV_DEVELOPMENT);
    }
}

function before()
{
    global $site, $application;
    set('site', $site);
    set('application', $application);
    layout('layouts/default.html.php');
}

function getConfig()
{
    global $application;
    $configuration = $application->getConfiguration();
    $config = array(
        'siteUrl' => 'http://twitter.com/oauth',
        'callbackUrl' => $application->getUrl() . 'callback',
        'consumerKey' => $configuration['consumerKey'],
        'consumerSecret' => $configuration['consumerSecret'],
    );
    return $config;
}

function getConsumer()
{
    $consumer = new Zend_Oauth_Consumer( getConfig() );
    return $consumer;
}

// function getTwitter()
// {
//     $config = getConfig();
//     $config['accessToken'] = getAccessToken();
//     return new Zend_Service_Twitter($config);
// }

function getAccessToken()
{
    global $application;
    $config = $application->getConfiguration();
    if (empty($config['access_token'])) {
        redirect_to('setup');
        return;
    }
    return unserialize($config['access_token']);
}

function getStatuses($timeline = 'friends_timeline', $params = array())
{
    global $cache;
    if ($cache) {
        $tweets = $cache->load($timeline);
    }
    if (empty($tweets)) {
        $token = getAccessToken();
        $client = $token->getHttpClient( getConfig() );
        $client->setUri("http://twitter.com/statuses/$timeline.json");
        foreach ($params as $key => $value) {
            $client->setParameterGet($key, $value);
        }
        $client->setMethod(Zend_Http_Client::GET);
        $response = $client->request();
        $tweets = Zend_Json::decode($response->getBody(), Zend_Json::TYPE_OBJECT);
        if ($cache) {
            $cache->save($tweets);
        }
    }
    return $tweets;
}

function is_admin()
{
    global $application;
    return Ld_Auth::isAuthenticated() && $application->getUserRole() == 'administrator';
}

function text($t)
{
    // http://www.barattalo.it/2010/03/10/php-parse-url-mailto-and-also-twitters-usernames-and-arguments/

    // link URLs
    $t = " ".preg_replace( "/(([[:alnum:]]+:\/\/)|www\.)([^[:space:]]*)".
        "([[:alnum:]#?\/&=])/i", "<a href=\"\\1\\3\\4\" target=\"_blank\">".
        "\\1\\3\\4</a>", $t);

    // link twitter users
    $t = preg_replace( "/ +@([a-z0-9_]*) ?/i", " <a href=\"http://twitter.com/\\1\">@\\1</a> ", $t);

    // $base_url = base_url();
    // $t = preg_replace( "/ +@([a-z0-9_]*) ?/i", " <a href=\"{$base_url}user/\\1\">@\\1</a> ", $t);

    // link twitter arguments
    $t = preg_replace( "/ +#([a-z0-9_]*) ?/i", " <a href=\"http://twitter.com/search?q=%23\\1\" target=\"_blank\">#\\1</a> ", $t);

    // truncates long urls that can cause display problems (optional)
    $t = preg_replace("/>(([[:alnum:]]+:\/\/)|www\.)([^[:space:]]".
        "{30,40})([^[:space:]]*)([^[:space:]]{10,20})([[:alnum:]#?\/&=])".
        "</", ">\\3...\\5\\6<", $t);
    return trim($t);
}

function render_template($controller = "posts", $action = "index")
{
    $format = isset($_GET['format']) ? $_GET['format'] : 'html';
    if ($format == 'html') {
        return html("$controller/$action.html.php");
    } elseif ($format == 'atom') {
        return xml("$controller/$action.atom.php", null);
    }
    return render("Unknown format");
}

dispatch('/', 'index');

function index()
{
    if (is_admin()) {
        return timeline();
    } else {
        return tweets();
    }
}

dispatch('/timeline', 'timeline');

function timeline()
{
    if (!is_admin()) {
        redirect_to('/');
    }
    set('tweets', getStatuses('friends_timeline'));
    set('isTimeline', true);
    if (is_admin()) {
        set('hasMenu', true);
    }
    return render_template("posts");
}

dispatch('/tweets', 'tweets');

dispatch('/your-tweets', 'tweets');

function tweets()
{
    set('tweets',  getStatuses('user_timeline'));
    set('isTweets', true);
    if (is_admin()) {
        set('hasMenu', true);
    }
    return render_template("posts");
}

dispatch('/setup', 'setup');

function setup()
{
    if (!is_admin()) {
        return render("Not authorized");
    }
    if (isset($_GET['reset'])) {
        $configuration = $application->getConfiguration();
        $configuration['access_token'] = null;
        $application->setConfiguration($configuration);
    }
    return html('setup.html.php');
}

dispatch('/authenticate', 'authenticate');

function authenticate()
{
    global $application;

    $consumer = getConsumer();
    $token = $consumer->getRequestToken();

    $config = $application->getConfiguration();
    $config['request_token'] = serialize($token);
    $application->setConfiguration($config);

    $consumer->redirect();
}

dispatch('/callback', 'callback');

function callback()
{
    global $application;

    $config = $application->getConfiguration();

    $consumer = getConsumer();
    $token = $consumer->getAccessToken($_GET, unserialize($config['request_token']));

    $config['access_token'] = serialize($token);
    $config['request_token'] = null;
    $application->setConfiguration($config);

    redirect_to('/');
}

run();
