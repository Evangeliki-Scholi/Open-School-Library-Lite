<?php

define('CONF_FILE', 'conf.php');

if (!file_exists(CONF_FILE))
{
    if (!is_writable(CONF_FILE))
    {
        echo '<!DOCTYPE html><html><head><title>Can not write '.CONF_FILE.'</title></head><body><h1 style="margin: 0 auto;">Can not write '.CONF_FILE.'</h1></body></html>';
        exit();
    }
    header("Location: install.php");
    exit();
}

require_once 'header.php';
require_once 'body.php';
require_once 'footer.php';

require_once CONF_FILE;

StartHTML();
HEAD('Open School Library Lite');
StartBODY();

TopNavBar(true, 0);
SideNavBar(true, 'Richard Bariampa', 0);
MainPage(true);
Footer();

EndBODY();
EndHTML();

?>