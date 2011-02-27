<div class="ld-merger ld-feed">

<?php foreach ($tweets as $entry) :
    $tweet = isset($entry->retweeted_status) ? $entry->retweeted_status : $entry;
    $reply_to = $tweet->in_reply_to_status_id;
    ?>
    <div class="hentry tweet" id="tweet-<?php echo $tweet->id ?>">
        <a class="avatar" href="http://twitter.com/<?php echo $tweet->user->screen_name ?>">
            <img src="<?php echo $tweet->user->profile_image_url ?>" width="32" height="32" alt="" class="avatar"/>
        </a>
        <div class="entry-inner">
            <a class="screen-name" href="http://twitter.com/<?php echo $tweet->user->screen_name ?>"><?php echo $tweet->user->screen_name ?></a>
            <span class="user-name"><?php echo $tweet->user->name ?></span>
            <?php if (isset($entry->retweeted_status)) : ?>
                <span class="retweeted-by">retweeted by
                    <a href="http://twitter.com/<?php echo $entry->user->screen_name ?>"><?php echo $entry->user->name ?></a></span>
            <?php endif ?>
            <div class="text"><?php echo text($tweet->text) ?></div>
            <div class="h6e-post-info">
                <?php echo Ld_Ui::relativeTime(strtotime($tweet->created_at)) ?>
                with <?php echo $tweet->source ?>
                |
                <a class="action reply" href="?reply=<?php echo $tweet->id ?>">Reply</a>
            </div>
        </div>
    </div>
<?php endforeach ?>

</div>