<?php foreach ($tweets as $tweet) : ?>
<entry>
  <id></id>
  <title><?php echo htmlspecialchars($tweet->text) ?></title>
  <summary type="html"><![CDATA[<?php echo text($tweet->text) ?>]]></summary>
  <author>
    <name><?php echo htmlspecialchars($tweet->from_user) ?></name>
    <uri>http://twitter.com/<?php echo htmlspecialchars($tweet->from_user) ?></uri>
  </author>
  <ld:type>status</ld:type>
  <published><?php echo date("c", strtotime($tweet->created_at)) ?></published>
  <updated><?php echo date("c", strtotime($tweet->created_at)) ?></updated>
  <link rel="self" type="application/atom+xml" href=""/>
  <link rel="avatar" href="<?php echo htmlspecialchars($tweet->profile_image_url) ?>"/>
  <activity:verb>http://activitystrea.ms/schema/1.0/post</activity:verb>
  <activity:object-type>http://activitystrea.ms/schema/1.0/note</activity:object-type>
</entry>
<?php endforeach ?>