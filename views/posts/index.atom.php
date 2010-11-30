<?php
global $configuration;
if (isset($configuration['associatedUser'])) {
    $associatedUser = $site->getUser($configuration['associatedUser']);
}
$accessToken = getAccessToken();
?>
<feed xmlns="http://www.w3.org/2005/Atom" xmlns:ld="http://ladistribution.net/#ns">
<id></id>
<title>Twtr Reader</title>
<link rel="self" type="application/atom+xml" href=""/>
<updated><?php echo date("c") ?></updated>
<?php foreach ($tweets as $tweet) : $item = $tweet; ?>
    <entry>
        <id></id>
        <title><?php echo htmlspecialchars($tweet->text) ?></title>
        <summary type="html"><![CDATA[<?php echo text($tweet->text) ?>]]></summary>
<?php if (isset($associatedUser) && $tweet->user->screen_name == $accessToken->screen_name) : ?>
        <ld:username><?php echo $associatedUser['username'] ?></ld:username>
<?php else : ?>
        <ld:avatar><?php echo $tweet->user->profile_image_url ?></ld:avatar>
        <ld:userurl>http://twitter.com/<?php echo $tweet->user->screen_name ?></ld:userurl>
<?php endif ?>
        <author>
            <name><?php echo $tweet->user->screen_name ?></name>
        </author>
        <ld:type>status</ld:type>
        <published><?php echo date("c", strtotime($tweet->created_at)) ?></published>
        <updated><?php echo date("c", strtotime($tweet->created_at)) ?></updated>
        <link rel="self" type="application/atom+xml" href=""/>
    </entry>
<?php endforeach ?>
</feed>