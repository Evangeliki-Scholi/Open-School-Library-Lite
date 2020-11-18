<?php

define('CONF_FILE', 'conf.php');

if (!file_exists(CONF_FILE))
{
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

$LoggedIn = $LogInLevel != 3;

require CONF_FILE;

require_once 'Themes/'.$Theme.'/Head.php';
require_once 'Themes/'.$Theme.'/Body.php';
require_once 'Themes/'.$Theme.'/Footer.php';

StartHTML($Title);
Head($Title, $LoggedIn, $LogInLevel);
StartBody($Title, $LoggedIn, $LogInLevel, ($LoggedIn) ? $_SESSION['Name'] : '', ($LoggedIn) ? $_SESSION['Email'] : '00000000000000000000000000000000');
MainPageLoad($Title, $LoggedIn, $LogInLevel, ($LoggedIn) ? $_SESSION['Name'] : '', ($LoggedIn) ? $_SESSION['Email'] : '00000000000000000000000000000000');
Footer($Title, $LoggedIn, $LogInLevel, ($LoggedIn) ? $_SESSION['Name'] : '');
EndBody($Title, $LoggedIn, $LogInLevel, ($LoggedIn) ? $_SESSION['Name'] : '');
EndHTML($Title);

?>