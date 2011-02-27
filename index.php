<?php

require_once dirname(__FILE__) . '/dist/prepend.php';

require_once 'limonade.php';

require_once 'twitter.php';

function configure()
{
    global $site, $application;
    option('base_uri',      $site->getPath() . '/' . $application->getPath() );
    option('session',       false);
    if (define('LD_DEBUG') && constant('LD_DEBUG')) {
        option('debug',         true);
        option('env',           ENV_DEVELOPMENT);
    }
}

function before()
{
    global $site, $application, $configuration;
    set('site', $site);
    set('application', $application);
    set('configuration', $configuration);
    layout('layouts/default.html.php');
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

dispatch('/about', 'about');

function about()
{
    global $application;
    $version = $application->getVersion();
    if (isAdmin()) {
        set('hasMenu', true);
    }
    return render("<h2>Le Tweeting</h2><p>La Distribution Twitter client. Version $version</p>");
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
    return html("posts/index.html.php");
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
    return html("posts/index.html.php");
}

dispatch('/mentions', 'mentions');

if ($accessToken = getAccessToken()) {
    dispatch('/' . $accessToken->screen_name . '/mentions', 'mentions');
}

function mentions()
{
    if (!getAccessToken()) {
        return redirect_to('/setup');
    }
    $params = array('q' => '@' . screenName() , 'count' => maxItems(), 'result_type' => 'recent');
    $query = getStatuses('search', $params);
    set('tweets', $query->results);
    set('isMentions', true);
    if (isAdmin()) {
        set('hasMenu', true);
    }
    return html("posts/mentions.html.php");
}

dispatch_post('/tweet', 'tweet');

function tweet()
{
    if (!getAccessToken()) {
        return redirect_to('/setup');
    }

    try {
        postTweet($_POST['status'], $_POST['in_reply_to_status_id']);
    } catch (Exception $e) {
        send_error($e);
    }

    redirect_to('/');
}

/* Feeds */

if ($accessToken = getAccessToken()) {
    dispatch('/feed/' . $accessToken->screen_name, 'user_feed');
    dispatch('/feed/' . $accessToken->screen_name . '/mentions', 'mentions_feed');
}

dispatch('/feed', 'user_feed');
dispatch('/feed/timeline', 'home_feed');
dispatch('/feed/mentions', 'mentions_feed');

function user_feed()
{
    $params = array('count' => maxItems());
    $tweets = getStatuses('user_timeline', $params);
    set('tweets', $tweets);
    return xml("posts/index.atom.php", "layouts/default.atom.php");
}

function home_feed()
{
    $params = array('count' => maxItems());
    $tweets = getStatuses('home_timeline', $params);
    set('tweets', $tweets);
    return xml("posts/index.atom.php", "layouts/default.atom.php");
}

function mentions_feed()
{
    $params = array('q' => '@' . screenName() , 'count' => maxItems(), 'result_type' => 'recent');
    $query = getStatuses('search', $params);
    set('tweets', $query->results);
    return xml("posts/mentions.atom.php", "layouts/default.atom.php");
}

/* Setup */

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

    if (!isAdmin()) {
        return render("Not authorized");
    }

    try {
        $consumer = getConsumer();
        $token = $consumer->getRequestToken();
    } catch (Exception $e) {
        send_error($e);
    }

    $configuration['request_token'] = serialize($token);
    $application->setConfiguration($configuration);

    $consumer->redirect();
}

dispatch('/callback', 'callback');

function callback()
{
    global $application, $configuration;

    if (!isAdmin()) {
        return render("Not authorized");
    }

    try {
        $consumer = getConsumer();
        $token = $consumer->getAccessToken($_GET, unserialize($configuration['request_token']));
    } catch (Exception $e) {
        send_error($e);
    }

    $configuration['access_token'] = serialize($token);
    $configuration['request_token'] = null;
    $application->setConfiguration($configuration);

    redirect_to('/');
}

run();
