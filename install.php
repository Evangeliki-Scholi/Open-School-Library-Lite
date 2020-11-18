<?php

define('SQL_FILE', 'sql_connection.php');
define('CONF_FILE', 'conf.php');

function CreateSQLConnectionFile()
{
	if (!isset($_POST['ServerURL']) || !isset($_POST['ServerUsername']) || !isset($_POST['ServerPassword']) || !isset($_POST['ServerName']))
		return array('response' => false, 'error' => 'Not all database requirements were submited');
	
	if (file_exists(SQL_FILE))
		if (!unlink(SQL_FILE))
			return array('response' => false, 'error' => 'Can not remove previous sql connection script');

	if (!touch(SQL_FILE) || !is_writable(SQL_FILE))
		return array('response' => false, 'error' => 'Can not write sql connection script');

	file_put_contents(SQL_FILE, '<?php

function GetDBConnection()
{
	error_reporting(0);
	
	$serverUrl = "'.$_POST['ServerURL'].'";
	$username = "'.$_POST['ServerUsername'].'";
	$password = "'.$_POST['ServerPassword'].'";
	$database = "'.$_POST['ServerName'].'";

	$conn = new mysqli($serverUrl, $username, $password, $database);

	if ($conn->connect_error)
		return NULL;

	$conn->set_charset("utf8");
	return $conn;
}

?>');

	return array('response' => true);
}

function RemoveSQLConnectionFile()
{
	return array('response' => file_exists(SQL_FILE) ? unlink(SQL_FILE) : false);
}

function TestSQLConnectionFile()
{
	if (!file_exists(SQL_FILE))
		return array('response' => false, 'error' => 'Could not locate sql connection script');
	
	require_once SQL_FILE;

	$ConnectivityResponse = GetDBConnection();
	return array('response' => !is_null($ConnectivityResponse));
}

function CreateDatabaseTables()
{
	if (!file_exists(SQL_FILE))
		return array('response' => false, 'error' => 'Could not locate sql connection script');
	
	require SQL_FILE;

	$conn = GetDBConnection();
	return array('response' => $conn->multi_query(file_get_contents('database.sql')) === TRUE);
}

function SetUpConfFile()
{
	if (!isset($_POST['Title']) || !isset($_POST['Language']))
		return array('response' => false, 'error' => 'Not all configuration requirements were submited');

	if (file_exists(CONF_FILE))
		if (!unlink(CONF_FILE))
			return array('response' => false, 'error' => 'Can not remove previous configuration script');

	if (!touch(CONF_FILE) || !is_writable(CONF_FILE))
		return array('response' => false, 'error' => 'Can not write configuration script');

	file_put_contents(CONF_FILE, '<?php 

$Title = \''.$_POST['Title'].'\';
$Language = \''.$_POST['Language'].'\';
$Theme = \'Lumid\';

?>');

	return array('response' => true);
}

if (isset($_POST['type']))
{
	header('Access-Control-Allow-Origin: *');
	header('Content-Type: application/json');
	die(json_encode((function_exists($_POST['type'])) ? $_POST['type']() : array('response' => false, 'error' => 'Function "'.$_POST['type'].'" does not exist'), JSON_UNESCAPED_UNICODE));
}
?>

<!DOCTYPE html>
<html>
	<head>
		<meta http-equiv="content-type" content="text/html; charset=UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<meta name="contributors" content="Rikarnto Bariampa">
		<title>Open School Library Lite Installer</title>
		<link rel="icon" type="image/ico" href="favicon.ico">

		<!-- [Bootstrap] -->
		<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css" integrity="sha384-TX8t27EcRE3e/ihU7zmQxVncDAy5uIKz4rEkgIXeMed4M0jlfIDPvg6uqKI2xXr2" crossorigin="anonymous">
		<!-- [/Bootstrap] -->

		<!-- [Admin LTE] -->
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/admin-lte/3.0.5/css/adminlte.min.css">
		<!-- [/Admin LTE] -->
	</head>
	<body class="layout-top-nav">
		<div class="wrapper">
			<!-- [Top NavBar] -->
			<nav class="main-header navbar navbar-expand-md navbar-dark">
				<!-- [Top Left NavBar] -->
				<ul class="navbar-nav">
					<li class="nav-item">
						<a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
					</li>
					<li class="nav-item d-none d-sm-inline-block">
						<a href="#" class="nav-link">Installer</a>
					</li>
				</ul>
				<!-- [/Top Left NavBar] -->
			</nav>
			<!-- [/Top NavBar] -->

			<!-- [Main Page] -->
			<div class="content-wrapper">

				<!-- [New Notification Panel] -->
				<div aria-live="polite" aria-atomic="true" style="position: relative; height: 0px; z-index: 99 !important;">
					<!-- Position it -->
					<div style="position: absolute; top: 0; right: 0;" id="InstantNotification">
						<br />
					</div>
				</div>
				<!-- [/New Notification Panel] -->

				<!-- [Content Body] -->
				<section class="content">
					<div class="container-fluid">
						<br />
						<br />
						<div class="row">
							<div class="col-md-10" id="ContentBody" style="margin: 0 auto;">
							</div>
						</div>
					</div>
				</section>
				<!-- [/Content Body] -->
			</div>
			<!-- [/Main Page] -->
		</div>

		<!-- [Footer] -->
			<footer class="main-footer">
			<strong>Powered By <a href="https://github.com/Evangeliki-Scholi/Open-School-Library-Lite">Open School Library Lite</a> and a lot of ❤️ for <a href="https://en.wikipedia.org/wiki/Free_and_open-source_software">FLOSS</a></strong>
				<div class="float-right d-none d-sm-inline">
					Made with <a href="https://adminlte.io">AdminLTE 3</a>
				</div>
			</footer>

			<!-- [JQuery] -->
			<script src="https://code.jquery.com/jquery-3.5.1.min.js" crossorigin="anonymous"></script>
			<!-- [/JQuery] -->
	
			<!-- [Popper] -->
			<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
			<!-- [/Popper] -->
	
			<!-- [Bootstrap] -->
			<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.min.js" integrity="sha384-w1Q4orYjBQndcko6MimVbzY0tgp4pWB4lZ7lr30WKz0vr/aWKhXdBNmNb5D92v7s" crossorigin="anonymous"></script>
			<!-- [/Bootstrap] -->

			<!-- [OSLL] -->
			<script src="js/OSLL.card.js"></script>
			<!-- [/OSLL] -->

			<script>
				var ErrorID = 1;
				async function SetUpDB()
				{
					const DatabaseCreationJSON = {
						type: 'CreateSQLConnectionFile',
						ServerURL: document.getElementById('DatabaseURL').value,
						ServerUsername: document.getElementById('DatabaseUsername').value,
						ServerPassword: document.getElementById('DatabasePassword').value,
						ServerName: document.getElementById('DatabaseName').value
					};

					var Response = await $.ajax({
						url: 'install.php',
						type: 'POST',
						data: DatabaseCreationJSON
					});
					if (Response.hasOwnProperty('error'))
						AddNotification(CreateNotification('Error' + ErrorID++, '', 'exclamation', Response['error'], 'Error', true, false));

					Response = await $.ajax({
						url: 'install.php',
						type: 'POST',
						data: { type: 'TestSQLConnectionFile' }
					});
					if (Response.hasOwnProperty('error'))
						AddNotification(CreateNotification('Error' + ErrorID++, '', 'exclamation', Response['error'], 'Error', true, false));

					if (!Response['response'])
					{
						Response = await $.ajax({
							url: 'install.php',
							type: 'POST',
							data: { type: 'RemoveSQLConnectionFile' }
						});
						AddNotification(CreateNotification('Error' + ErrorID++, '', 'exclamation', 'Wrong SQL Credentials', 'Error', true, false));
						return;
					}

					Response = await $.ajax({
						url: 'install.php',
						type: 'POST',
						data: { type: 'CreateDatabaseTables' }
					});
					if (Response['response'])
					{
						document.getElementById('DBSetUpCard').style.display = 'none';
						document.getElementById('ConfSetUpCard').style.display = 'block';
					}
					else
						AddNotification(CreateNotification('Error' + ErrorID++, '', 'exclamation', Response['error'], 'Error', true, false));
				}

				async function SetUpConf()
				{
					const ConfJSON = {
						type: 'SetUpConfFile',
						Title: document.getElementById('Title').value,
						Language: document.getElementById('Language').value
					};

					var Response = await $.ajax({
						url: 'install.php',
						type: 'POST',
						data: ConfJSON
					});

					if (Response.hasOwnProperty('error') || !Response['response'])
						AddNotification(CreateNotification('Error' + ErrorID++, '', 'exclamation', Response['error'], 'Error', true, false));
					else
						window.location = 'index.php';
				}

				$(function()
				{
					AddCard(CreateCard('DBSetUpCard', 'DBSetUp', 'Database Connection', 'dark', '<div class="row"><div class="col-12"><input type="text" class="form-control" id="DatabaseURL" placeholder="Database URL" autocomplete="off" readonly onfocus="this.removeAttribute(\'readonly\');"></div></div><br /><div class="row"><div class="col-12"><input type="text" class="form-control" id="DatabaseUsername" placeholder="Database Username" autocomplete="off" readonly onfocus="this.removeAttribute(\'readonly\');"></div></div><br /><div class="row"><div class="col-12"><input type="password" class="form-control" id="DatabasePassword" placeholder="Database Password" autocomplete="off" readonly onfocus="this.removeAttribute(\'readonly\');"></div></div><br /><div class="row"><div class="col-12"><input type="text" class="form-control" id="DatabaseName" placeholder="Database Name" autocomplete="off" readonly onfocus="this.removeAttribute(\'readonly\');"></div></div>', '<button type="button" class="btn btn-block btn-primary" onclick="SetUpDB();">Set Up Database</button>'));
					AddCard(CreateCard('ConfSetUpCard', 'ConfSetUp', 'Configurations', 'dark', '<div class="row"><div class="col-12"><input type="text" class="form-control" id="Title" placeholder="Site Title" autocomplete="off" readonly onfocus="this.removeAttribute(\'readonly\');"></div></div><br /><div class="row"><div class="col-12"><div class="form-group"><label>Language</label><select class="form-control" id="Language"><option>English</option></select></div></div></div>', '<button type="button" class="btn btn-block btn-primary" onclick="SetUpConf();">Set Up Configurations</button>'));
					document.getElementById('DBSetUpCard').style.display = 'block';
					document.getElementById('ConfSetUpCard').style.display = 'none';
				});
			</script>
			<!-- [/Footer] -->
	</body>
</html>