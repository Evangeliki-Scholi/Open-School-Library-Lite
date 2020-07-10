<?php
session_start();
include_once 'header.php';
require_once 'settings.php';
?>
            <center><h1><?php echo (isset($_SETTINGS['Welcome Message']) ? $_SETTINGS['Welcome Message'] : 'Hello and welcome'); ?></h1></center>
<?php
include_once 'footer.php';
?>