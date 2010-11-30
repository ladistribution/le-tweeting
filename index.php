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
    global $application, $configuration;
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

function getAccessToken()
{
    global $application, $configuration;
    if (empty($configuration['access_token'])) {
        return null;
    }
    return unserialize($configuration['access_token']);
}

function getStatuses($timeline = 'friends_timeline', $params = array())
{
    global $application, $cache;
    if ($cache) {
        $cacheKey = $application->getId() . '_' . $timeline;
        $tweets = $cache->load($cacheKey);
    }
    if (empty($tweets)) {
        $token = getAccessToken();
        $client = $token->getHttpClient( getConfig() );
        $client->setUri("http://twitter.com/statuses/$timeline.json");
        foreach ($params as $key => $value) {
            $client->setParameterGet($key, $value);
        }
        $response = $client->request(Zend_Http_Client::GET);
        $tweets = Zend_Json::decode($response->getBody(), Zend_Json::TYPE_OBJECT);
        if ($cache) {
            $cache->save($tweets);
        }
    }
    return $tweets;
}

function isAdmin()
{
    global $application;
    return Ld_Auth::isAuthenticated() && $application->getUserRole() == 'administrator';
}

function screenName()
{
    if ($accessToken = getAccessToken()) {
        return $accessToken->screen_name;
    }
}

function maxItems()
{
    global $application, $configuration;
    $maxItems = isset($configuration['maxItems']) ? (int)$configuration['maxItems'] : 25;
    if ($maxItems <= 0 || $maxItems >= 200) {
        $maxItems = 25;
    }
    return $maxItems;
}

function text($text)
{
    // link it
    $text = preg_replace("/(^|[\n ])([\w]*?)((ht|f)tp(s)?:\/\/[\w]+[^ \,\"\n\r\t<]*)/is", "$1$2<a href=\"$3\" >$3</a>", $text);
    $text = preg_replace("/(^|[\n ])([\w]*?)((www|ftp)\.[^ \,\"\t\n\r<]*)/is", "$1$2<a href=\"http://$3\" >$3</a>", $text);
    // twitter it
    $text = preg_replace("/@(\w+)/", '<a href="http://www.twitter.com/$1" target="_blank">@$1</a>', $text);
    $text = preg_replace("/\#(\w+)/", '<a href="http://search.twitter.com/search?q=$1" target="_blank">#$1</a>', $text);
    return trim($text);
}

function render_template($controller = "posts", $action = "index")
{
    return html("$controller/$action.html.php");
}

function send_error($error)
{
    set('error', $error);
    return html("error.html.php");
}

dispatch('/', 'index');

function index()
{
    if (isAdmin()) {
        return redirect_to('/timeline');
    }
    return tweets();
}

dispatch('/timeline', 'timeline');

function timeline()
{
    if (!getAccessToken()) {
        return redirect_to('/setup');
    }
    if (!isAdmin()) {
        redirect_to('/');
    }
    $params = array('count' => maxItems());
    $tweets = getStatuses('home_timeline', $params);
    if (isset($tweets->error)) {
        return send_error($tweets->error);
    }
    set('tweets', $tweets);
    set('isTimeline', true);
    set('hasMenu', true);
    return render_template("posts");
}

dispatch('/tweets', 'tweets');

if ($accessToken = getAccessToken()) {
    dispatch('/' . $accessToken->screen_name, 'tweets');
}

function tweets()
{
    if (!getAccessToken()) {
        return redirect_to('/setup');
    }
    $params = array('count' => maxItems());
    $tweets = getStatuses('user_timeline', $params);
    set('tweets', $tweets);
    set('isTweets', true);
    if (isAdmin()) {
        set('hasMenu', true);
    }
    return render_template("posts");
}

dispatch('/feed', 'feed');

function feed()
{
    $timeline = isAdmin() && getAccessToken() ? 'home_timeline' : 'user_timeline';
    $params = array('count' => maxItems());
    $tweets = getStatuses($timeline, $params);
    set('tweets', $tweets);
    return xml("posts/index.atom.php", null);
}

dispatch('/setup', 'setup');

function setup()
{
    global $application, $configuration;
    if (!isAdmin()) {
        return render("Not authorized");
    }
    if (isset($_GET['reset'])) {
        $configuration['access_token'] = null;
        $application->setConfiguration($configuration);
        redirect_to('setup');
    }
    return html('setup.html.php');
}

dispatch('/authenticate', 'authenticate');

function authenticate()
{
    global $application, $configuration;

    $consumer = getConsumer();
    $token = $consumer->getRequestToken();

    $configuration['request_token'] = serialize($token);
    $application->setConfiguration($configuration);

    $consumer->redirect();
}

dispatch('/callback', 'callback');

function callback()
{
    global $application, $configuration;

    $consumer = getConsumer();
    $token = $consumer->getAccessToken($_GET, unserialize($configuration['request_token']));

    $configuration['access_token'] = serialize($token);
    $configuration['request_token'] = null;
    $application->setConfiguration($configuration);

    redirect_to('/');
}

run();
