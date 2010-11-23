<?php
global $site, $application;
$configuration = $application->getConfiguration();
?>

<?php if (empty($configuration['consumerKey']) || empty($configuration['consumerSecret'])) : ?>

    <h3>Register application</h3>

    <p>First, register a new application at <a href="http://dev.twitter.com/apps/new">Twitter</a> with the following informations.</p>

    <table class="h6e-data">
        <tr>
            <th>Application Name</th>
            <td>Le Tweeting @<?php echo $site->getConfig('host') ?><?php echo $site->getConfig('path') ?></td>
        </tr>
        <tr>
            <th>Description</th>
            <td>The official Twitter client for La Distribution</td>
        </tr>
        <tr>
            <th>Application Website</th>
            <td><?php echo $application->getUrl() . 'about' ?></td>
        </tr>
        <tr>
            <th>Organization</th>
            <td>h6e.net</td>
        </tr>
        <tr>
            <th>Application Type</th>
            <td>browser</td>
        </tr>
        <tr>
            <th>Callback URL</th>
            <td><?php echo $application->getUrl() . 'callback' ?></td>
        </tr>
        <tr>
            <th>Default Access type</th>
            <td>Read &amp; Write</td>
        </tr>
        <tr>
            <th>Application Icon</th>
            <td><em>Not necessary</em></td>
        </tr>
    </table>

    <p>Once done, edit the <a href="<?php echo Ld_Ui::getInstanceSettingsUrl($application, 'configure') ?>">application settings</a>
        and update the consumerKey and consumerSecret.</p>

<?php elseif (empty($configuration['access_token'])) : ?>

    <h3>Link application</h3>

    <p>You should now link the application with a Twitter account.</p>

    <form method="get" action="<?php echo url_for('authenticate') ?>">
        <input type="submit" class="submit button ld-button" value="Authenticate on Twitter"/>
    </form>

<?php else : ?>

    <h3>Setup</h3>

    <p>The application is configured.</p>

    <p><a href="<?php echo url_for('setup') ?>?reset=1">Reset credentials.</a></p>

<?php endif ?>
