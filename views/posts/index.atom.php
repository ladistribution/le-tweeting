<?php
$configuration = $application->getConfiguration();

if (isset($configuration['associatedUser'])) {
    $associatedUser = $site->getUser($configuration['associatedUser']);
}
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
        <content type="html"><![CDATA[<?php echo text($tweet->text) ?>]]></content>
<?php if (isset($associatedUser)) : ?>
        <ld:username><?php echo $associatedUser['username'] ?></ld:username>
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