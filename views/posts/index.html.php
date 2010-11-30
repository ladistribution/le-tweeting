<div class="ld-merger ld-feed">

<?php foreach ($tweets as $tweet) : ?>
    <div class="hentry tweet">
        <a class="avatar" href="http://twitter.com/<?php echo $tweet->user->screen_name ?>">
            <img src="<?php echo $tweet->user->profile_image_url ?>" width="32" height="32" alt="" class="avatar"/>
        </a>
        <div class="entry-inner">
            <a href="http://twitter.com/<?php echo $tweet->user->screen_name ?>"><?php echo $tweet->user->screen_name ?></a>
            <span class="user-name"><?php echo $tweet->user->name ?></span>
            <div class="text"><?php echo text($tweet->text) ?></div>
            <div class="h6e-post-info"><?php echo Ld_Ui::relativeTime(strtotime($tweet->created_at)) ?></div>
        </div>
    </div>
<?php endforeach ?>

</div>