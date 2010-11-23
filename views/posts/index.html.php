<div class="ld-merger">

<?php foreach ($tweets as $tweet) : ?>
    <div class="hentry tweet">
        <a class="avatar" href="http://twitter.com/<?php echo $tweet->user->screen_name ?>">
            <img src="<?php echo $tweet->user->profile_image_url ?>" width="32" height="32" alt="" class="avatar"/>
        </a>
        <a href="http://twitter.com/<?php echo $tweet->user->screen_name ?>"><?php echo $tweet->user->screen_name ?></a>
        <span class="user-name"><?php echo $tweet->user->name ?></span>
        <br/>
        <?php echo text($tweet->text) ?>
    </div>
<?php endforeach ?>

</div>