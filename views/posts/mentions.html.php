<div class="ld-merger ld-feed">

<?php foreach ($tweets as $tweet) : ?>
    <div class="hentry tweet" id="tweet-<?php echo $tweet->id ?>">
        <a class="avatar" href="http://twitter.com/<?php echo $tweet->from_user ?>">
            <img src="<?php echo $tweet->profile_image_url ?>" width="32" height="32" alt="" class="avatar"/>
        </a>
        <div class="entry-inner">
            <a class="screen-name" href="http://twitter.com/<?php echo $tweet->from_user ?>"><?php echo $tweet->from_user ?></a>
            <div class="text"><?php echo text($tweet->text) ?></div>
            <div class="h6e-post-info">
                <?php echo Ld_Ui::relativeTime(strtotime($tweet->created_at)) ?>
                with <?php echo htmlspecialchars_decode($tweet->source) ?>
                |
                <a class="action reply" href="?reply=<?php echo $tweet->id ?>">Reply</a>
            </div>
        </div>
    </div>
<?php endforeach ?>

</div>