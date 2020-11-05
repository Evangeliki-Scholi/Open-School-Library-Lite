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

require_once CONF_FILE;

$LogInLevel = 3;
session_start();
if (isset($_SESSION) && isset($_SESSION['Logged In']) && isset($_SESSION['Level']) && $_SESSION['Level'] <= 3 && $_SESSION['Level'] >= 0)
	$LogInLevel = $_SESSION['Level'];
else
{
	session_unset();
	session_destroy();
}

if (0 <= $LogInLevel && $LogInLevel <= 1)
{
	require_once 'header-admin.php';
	require_once 'body-admin.php';
	require_once 'footer-admin.php';

	StartHTML();
	HEAD('Open School Library Lite');
	StartBODY();

	TopNavBar(true, 0);
	SideNavBar(true, $_SESSION['Name'], md5(strtolower(trim($_SESSION['Email']))), 0);
	MainPage(true);
	Footer();

	EndBODY();
	EndHTML();
}
else
{
	require_once 'header.php';
	require_once 'body.php';
	require_once 'footer.php';

	StartHTML();
	HEAD('Open School Library Lite');
	StartBODY();

	TopNavBar();
	MainPage();
	Footer();
	
	EndBODY();
	EndHTML();
}

?>