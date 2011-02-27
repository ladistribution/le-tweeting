<?php
if (isset($configuration['associatedUser'])) {
    $associatedUser = $site->getUser($configuration['associatedUser']);
    $accessToken = getAccessToken();
}
foreach ($tweets as $entry) :
$tweet = isset($entry->retweeted_status) ? $entry->retweeted_status : $entry; ?>
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
  <published><?php echo date("c", strtotime($entry->created_at)) ?></published>
  <updated><?php echo date("c", strtotime($entry->created_at)) ?></updated>
  <link rel="self" type="application/atom+xml" href=""/>
  <link rel="avatar" href="<?php echo htmlspecialchars($tweet->user->profile_image_url) ?>"/>
  <activity:verb>http://activitystrea.ms/schema/1.0/post</activity:verb>
  <activity:object-type>http://activitystrea.ms/schema/1.0/note</activity:object-type>
</entry>
<?php endforeach ?>