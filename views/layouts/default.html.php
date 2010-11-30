<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
  <title><?php echo $application->getName() ?></title>
  <meta charset="utf-8"/>
<?php if (defined('LD_COMPRESS_CSS') && constant('LD_COMPRESS_CSS')) : ?>
  <link href="<?php echo Ld_Ui::getCssUrl('/h6e-minimal/h6e-minimal.compressed.css', 'h6e-minimal') ?>" rel="stylesheet" type="text/css"/>
  <link href="<?php echo Ld_Ui::getCssUrl('/ld-ui/ld-ui.compressed.css', 'ld-ui') ?>" rel="stylesheet" type="text/css"/>
<?php else : ?>
    <link href="<?php echo Ld_Ui::getCssUrl('/h6e-minimal/h6e-minimal.css', 'h6e-minimal') ?>" rel="stylesheet" type="text/css"/>
    <link href="<?php echo Ld_Ui::getCssUrl('/ld-ui/ld-ui.css', 'ld-ui') ?>" rel="stylesheet" type="text/css"/>
<?php endif ?>
<?php if (defined('LD_APPEARANCE') && constant('LD_APPEARANCE')) : ?>
  <link href="<?php echo Ld_Ui::getApplicationStyleUrl() ?>" rel="stylesheet" type="text/css"/>
<?php endif ?>
  <style type="text/css">
  .h6e-block { padding:1em; }
  .user-name { opacity:0.5; }
  .ld-feed .hentry .entry-inner { width:auto; }
  .ld-feed .h6e-post-info { opacity:0.5; }
  .ld-merger .hentry.tweet { padding-bottom:5px; margin-bottom:5px; }
  .ld-merger .avatar { margin-top:2px; }
  </style>
</head>
<body class="ld-layout h6e-layout">

    <?php Ld_Ui::topBar(); ?>

    <div class="ld-main-content h6e-main-content">

        <h1 class="h6e-page-title"><?php echo $application->getName() ?></h1>

        <?php Ld_Ui::topNav(); ?>

        <?php if (isset($hasMenu)) : ?>
        <ul class="h6e-tabs">
            <li <?php if (isset($isTimeline)) echo 'class="active"' ?>><a href="<?php echo url_for('timeline') ?>">Home Timeline</a></li>
            <li <?php if (isset($isTweets)) echo 'class="active"' ?>><a href="<?php echo url_for(screenName()) ?>">@<?php echo screenName() ?> Timeline</a></li>
        </ul>
        <?php endif ?>

        <div class="h6e-page-content h6e-block<?php if (isset($hasMenu)) echo ' has-tab' ?>">
            <?php echo $content ?>
        </div>

        <div class="h6e-simple-footer">
            Powered by <strong>Le Tweeting</strong>
            with the help of <a href="http://www.limonade-php.net/">Limonade</a>
            via <a href="http://www.ladistribution.net/">La Distribution</a>
        </div>

    </div>

    <?php Ld_Ui::superBar(); ?>

</body>
</html>