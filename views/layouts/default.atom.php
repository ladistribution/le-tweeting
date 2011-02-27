<feed xmlns="http://www.w3.org/2005/Atom" xmlns:ld="http://ladistribution.net/#ns" xmlns:activity="http://activitystrea.ms/spec/1.0/">
<id></id>
<title><?php echo htmlspecialchars( $application->getName() ) ?></title>
<link rel="self" type="application/atom+xml" href="<?php echo $application->getUrl() ?>feed"/>
<updated><?php echo date("c") ?></updated>
<?php echo $content ?>
</feed>