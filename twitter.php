<?php

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
    global $configuration;
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
        if ($timeline == 'search') {
            $client->setUri("http://search.twitter.com/search.json");
        } else {
            $client->setUri("http://twitter.com/statuses/$timeline.json");
        }
        foreach ($params as $key => $value) {
            $client->setParameterGet($key, $value);
        }
        $response = $client->request(Zend_Http_Client::GET);
        $tweets = Zend_Json::decode($response->getBody(), Zend_Json::TYPE_OBJECT);
        if ($cache) {
            $cache->save($tweets, /* id */ null, array('statuses'));
        }
    }
    return $tweets;
}

function postTweet($status = '', $in_reply_to = null)
{
    global $cache;
    $token = getAccessToken();
    $client = $token->getHttpClient( getConfig() );
    $client->setUri("http://twitter.com/statuses/update.json");
    $data = array('status' => $status);
    if (!empty($in_reply_to)) {
        $data['in_reply_to_status_id'] = $in_reply_to;
    }
    foreach ($data as $key => $value) {
        $client->setParameterGet($key, $value);
    }
    $response = $client->request(Zend_Http_Client::POST);
    if ($cache) {
        $cache->clean('matchingTag', array('statuses'));
    }
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
    $maxItems = isset($configuration['maxItems']) ? (int)$configuration['maxItems'] : 50;
    if ($maxItems <= 0 || $maxItems >= 200) {
        $maxItems = 50;
    }
    return $maxItems;
}

function text($text)
{
    // link it
    $text = preg_replace("/(^|[\n ])([\w]*?)((ht|f)tp(s)?:\/\/[\w]+[^ \,\"\n\r\t<]*)/is", "$1$2<a href=\"$3\" >$3</a>", $text);
    $text = preg_replace("/(^|[\n ])([\w]*?)((www|ftp)\.[^ \,\"\t\n\r<]*)/is", "$1$2<a href=\"http://$3\" >$3</a>", $text);
    // twitter it
    $text = preg_replace("/@(\w+)/", '<a href="http://twitter.com/$1" target="_blank">@$1</a>', $text);
    $text = preg_replace("/\#(\w+)/", '<a href="http://search.twitter.com/search?q=$1" target="_blank">#$1</a>', $text);
    return trim($text);
}
