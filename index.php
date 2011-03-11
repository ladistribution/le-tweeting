<?php

require_once dirname(__FILE__) . '/dist/prepend.php';

require_once 'limonade.php';

require_once 'twitter.php';

function configure()
{
    global $site, $application;
    option('base_uri',      $site->getPath() . '/' . $application->getPath() );
    option('session',       false);
}

function before()
{
    global $site, $application, $configuration;
    set('site', $site);
    set('application', $application);
    set('configuration', $configuration);
    set('hasMenu', false);
    set('hasForm', false);
    set('screenName', getScreenName());
    layout('layouts/default.html.php');
}

function send_error($error)
{
    set('error', $error);
    return html("error.html.php");
}

$screenName = getScreenName();

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
    return render("<h2>Le Tweeting</h2><p>Simple Twitter client for La Distribution. Version $version</p>");
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
    try {
        $params = array('count' => maxItems());
        $tweets = getStatuses('home_timeline', $params);
        set('tweets', $tweets);
        set('isTimeline', true);
        set('hasMenu', true);
        set('hasForm', true);
        return html("posts/index.html.php");
    } catch (Exception $e) {
        return send_error($e);
    }
}

dispatch('/tweets', 'tweets');

if ($screenName) {
    dispatch('/' . $screenName, 'tweets');
}

function tweets()
{
    if (!getAccessToken()) {
        return redirect_to('/setup');
    }
    try {
        $params = array('count' => maxItems());
        $tweets = getStatuses('user_timeline', $params);
        set('tweets', $tweets);
        set('isTweets', true);
        if (isAdmin()) {
            set('hasMenu', true);
            set('hasForm', true);
        }
        return html("posts/index.html.php");
    } catch (Exception $e) {
        return send_error($e);
    }
}

dispatch('/mentions', 'mentions');

if ($screenName) {
    dispatch('/' . $screenName . '/mentions', 'mentions');
}

function mentions()
{
    if (!getAccessToken()) {
        return redirect_to('/setup');
    }
    try {
        $params = array('q' => '@' . getScreenName() , 'count' => maxItems(), 'result_type' => 'recent');
        $query = getStatuses('search', $params);
        set('tweets', $query->results);
        set('isMentions', true);
        if (isAdmin()) {
            set('hasMenu', true);
            set('hasForm', true);
        }
        return html("posts/mentions.html.php");
    } catch (Exception $e) {
        return send_error($e);
    }
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
        return send_error($e);
    }

    redirect_to('/');
}

/* Feeds */

if ($screenName) {
    dispatch('/feed/' . $screenName, 'user_feed');
    dispatch('/feed/' . $screenName . '/mentions', 'mentions_feed');
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
    $params = array('q' => '@' . getScreenName() , 'count' => maxItems(), 'result_type' => 'recent');
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
        return send_error($e);
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
