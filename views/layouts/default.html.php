<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
  <meta charset="utf-8"/>
  <title><?php echo $application->getName() ?></title>
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
  <link href="<?php echo $application->getAbsoluteUrl('/css/le-tweeting.css') ?>" rel="stylesheet" type="text/css"/>
  <script type="text/javascript" src="<?php echo Ld_Ui::getJsUrl('/jquery/jquery.js', 'js-jquery') ?>"></script>
  <script type="text/javascript" src="<?php echo $application->getAbsoluteUrl('/js/le-tweeting.js') ?>"></script>
</head>
<body class="ld-layout h6e-layout">

    <?php Ld_Ui::topBar(); ?>

    <div class="ld-main-content h6e-main-content">

        <h1 class="h6e-page-title"><a href="<?php echo $application->getUrl() ?>"><?php echo $application->getName() ?></a></h1>

        <?php Ld_Ui::topNav(); ?>

        <?php if ($hasForm) : ?>
        <form id="tweet-form" class="tweet-form" method="post" action="<?php echo url_for('tweet') ?>">
        <h3><label for="tweet-status">What's happening?</label></h3>
        <textarea cols="50" rows="3" id="tweet-status" class="tweet-status" name="status"></textarea>
        <input type="hidden" name="in_reply_to_status_id" value=""/>
        <input type="submit" class="submit button ld-button" value="Tweet"/>
        </form>
        <?php endif ?>

        <?php if ($hasMenu) : ?>
        <ul class="h6e-tabs">
            <?php if (isAdmin()) : ?>
            <li <?php if (isset($isTimeline)) echo 'class="active"' ?>><a href="<?php echo url_for('timeline') ?>">
                Home Timeline</a></li>
            <?php endif ?>
            <li <?php if (isset($isTweets)) echo 'class="active"' ?>><a href="<?php echo url_for(screenName()) ?>">
                @<?php echo screenName() ?> Timeline</a></li>
            <li <?php if (isset($isMentions)) echo 'class="active"' ?>><a href="<?php echo url_for(screenName() . '/mentions') ?>">
                @<?php echo screenName() ?> Mentions</a></li>
        </ul>
        <?php endif ?>

        <div class="h6e-page-content h6e-block <?php if ($hasMenu) echo ' has-tab' ?>">
            <?php echo $content ?>
        </div>

        <div class="h6e-simple-footer">
            Powered by <strong><?php echo $application->getPackage()->getName() ?></strong>
            with the help of <a href="http://www.limonade-php.net/">Limonade</a>
            via <a href="http://ladistribution.net/">La Distribution</a>
        </div>

    </div>

    <?php Ld_Ui::superBar(); ?>

</body>
</html>