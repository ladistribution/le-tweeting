<h3>Error</h3>

<?php if ($error instanceof Exception) : ?>
    <p><?php echo $error->getMessage() ?></p>
    <?php if (defined('LD_DEBUG') && constant('LD_DEBUG')) : ?>
    <pre><?php echo $error->getTraceAsString() ?></pre>
    <?php endif ?>
<?php else : ?>
    <p><?php echo $error ?></p>
<?php endif ?>

<p><a href="<?php echo url_for('setup') ?>?reset=1">Reset credentials ?</a></p>