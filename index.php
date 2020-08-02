<?php
session_start();
include_once 'header.php';
require_once 'settings.php';
?>
            <center><h1><?php echo GetSetting('Welcome Message'); ?></h1></center>
<?php
include_once 'footer.php';
?>