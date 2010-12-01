<?php
if (isset($configuration['associatedUser'])) {
    $associatedUser = $site->getUser($configuration['associatedUser']);
    $accessToken = getAccessToken();
}
?>
<feed xmlns="http://www.w3.org/2005/Atom" xmlns:ld="http://ladistribution.net/#ns" xmlns:activity="http://activitystrea.ms/spec/1.0/">
<id></id>
<title><?php echo htmlspecialchars( $application->getName() ) ?></title>
<link rel="self" type="application/atom+xml" href="<?php echo $application->getUrl() ?>feed"/>
<updated><?php echo date("c") ?></updated>
<?php foreach ($tweets as $tweet) : ?>
<entry>
  <id></id>
  <title><?php echo htmlspecialchars($tweet->text) ?></title>
  <summary type="html"><![CDATA[<?php echo text($tweet->text) ?>]]></summary>
<?php if (isset($associatedUser, $accessToken) && $tweet->user->screen_name == $accessToken->screen_name) : ?>
  <ld:username><?php echo $associatedUser['username'] ?></ld:username>
<?php endif ?>
  <author>
    <name><?php echo htmlspecialchars($tweet->user->screen_name) ?></name>
    <uri>http://twitter.com/<?php echo htmlspecialchars($tweet->user->screen_name) ?></uri>
  </author>
  <ld:type>status</ld:type>
  <published><?php echo date("c", strtotime($tweet->created_at)) ?></published>
  <updated><?php echo date("c", strtotime($tweet->created_at)) ?></updated>
  <link rel="self" type="application/atom+xml" href=""/>
  <link rel="avatar" href="<?php echo htmlspecialchars($tweet->user->profile_image_url) ?>"/>
  <activity:verb>http://activitystrea.ms/schema/1.0/post</activity:verb>
  <activity:object>
    <activity:object-type>http://activitystrea.ms/schema/1.0/status</activity:object-type>
  </activity:object>
</entry>
<?php endforeach ?>
</feed>