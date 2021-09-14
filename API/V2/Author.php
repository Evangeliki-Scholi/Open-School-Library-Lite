<?php

$LogInLevel = 3;
session_start();
if (isset($_SESSION) && isset($_SESSION['Logged In']) && isset($_SESSION['Level']) && $_SESSION['Level'] <= 3 && $_SESSION['Level'] >= 0)
	$LogInLevel = $_SESSION['Level'];
else
{
	session_unset();
	session_destroy();
}

$PermissionLevels = array(
	0 => array('AddAuthor', 'CheckAuthorExists', 'EditAuthor', 'GetAuthor', 'RemoveAuthor', 'SearchAuthor'),
	1 => array('AddAuthor', 'CheckAuthorExists', 'EditAuthor', 'GetAuthor', 'RemoveAuthor', 'SearchAuthor'),
	2 => array(                                                'GetAuthor',                 'SearchAuthor'),
	3 => array(                                                'GetAuthor',                 'SearchAuthor')
);

function AddAuthor($PermissionLevels, $LogInLevel, $DatabaseConnection)
{
	return array('response' => false, 'error' => 'AddAuthor is not yet activated');
}

function CheckAuthorExists($PermissionLevels, $LogInLevel, $DatabaseConnection)
{
	if (!isset($_POST['Identifier']) || $_POST['Identifier'] == '')
		return array('response' => false, 'error' => 'Identifier is not provided', 'errorCode' => 3321);

	$Identifier = $_POST['Identifier'];

	$query = 'SELECT `ID` FROM `authors` WHERE `ID` = ?;';
	$statement = $DatabaseConnection->prepare($query);
	if (!$statement)
		return array('response' => false, 'error' => 'Could not prepare statement in BookV2::'.__FUNCTION__, 'errorCode' => 3101);
	$statement->bind_param('s', $Identifier);
	if (!$statement->execute())
		return array('response' => false, 'error' => 'Could not execute statement in BookV2::'.__FUNCTION__, 'errorCode' => 3102);

	$result = $statement->get_result();
	return array('response' => $result->num_rows != 0);
}

function EditAuthor($PermissionLevels, $LogInLevel, $DatabaseConnection)
{
	return array('response' => false, 'error' => 'EditAuthor is not yet activated');
}

function GetAuthor($PermissionLevels, $LogInLevel, $DatabaseConnection)
{
	if (!isset($_POST['Identifier']) || $_POST['Identifier'] == '')
		return array('response' => false, 'error' => 'Identifier is not provided', 'errorCode' => 3321);

	$Exists = CheckAuthorExists($PermissionLevels, $LogInLevel, $DatabaseConnection);
	if (!$Exists['response'])
		return array('response' => false, 'error' => 'An Author with that Identifier does not exist', 'errorCode' => 3323);
	else if (isset($Exists['error']))
		return $Exists;

	$Identifier = $_POST['Identifier'];

	$query = 'SELECT `ID`, `Name`, `PictureURL`, `Description`, `Metadata` FROM `authors` WHERE `ID` = ?;';
	$statement = $DatabaseConnection->prepare($query);
	if (!$statement)
		return array('response' => false, 'error' => 'Could not prepare statement in AuthorV2::'.__FUNCTION__, 'errorCode' => 3101);
	$statement->bind_param('s', $Identifier);
	if (!$statement->execute())
		return array('response' => false, 'error' => 'Could not execute statement in AuthorV2::'.__FUNCTION__, 'errorCode' => 3102);

	$result = $statement->get_result();
	if ($result->num_rows != 1)
		return array('response' => false, 'error' => 'An Author with that Identifier does not exist', 'errorCode' => 3323);
	
	return array('response' => true, 'data' => $result->fetch_assoc());
}

function RemoveAuthor($PermissionLevels, $LogInLevel, $DatabaseConnection)
{
	return array('response' => false, 'error' => 'RemoveAuthor is not yet activated');
}

function SearchAuthor($PermissionLevels, $LogInLevel, $DatabaseConnection)
{
	if (!isset($_POST['SearchTag']) || $_POST['SearchTag'] == '' || strlen($_POST['SearchTag']) < 2)
		return array('response' => false, 'error' => 'SearchTag is not provided', 'errorCode' => 3321);

	$SearchTag = '%'.$_POST['SearchTag'].'%';
	$Skip = (isset($_POST['Skip'])) ? $_POST['Skip'] : 0;
	$Limit = (isset($_POST['Limit']) && $_POST['Limit'] <= 100) ? $_POST['Limit'] : 20;

	$query = 'SELECT `ID`, `Name`, `PictureURL`, `Description`, `Metadata` FROM `authors` WHERE ( UPPER(`Name`) LIKE UPPER(?) OR `ID` = ?) ORDER BY `ID` LIMIT '.$Skip.', '.$Limit.';';
	$statement = $DatabaseConnection->prepare($query);
	if (!$statement)
		return array('response' => false, 'error' => 'Could not prepare statement in AuthorV2::'.__FUNCTION__, 'errorCode' => 3101);
	$statement->bind_param('ss', $SearchTag, $_POST['SearchTag']);
	if (!$statement->execute())
		return array('response' => false, 'error' => 'Could not execute statement in AuthorV2::'.__FUNCTION__, 'errorCode' => 3102);

	$result = $statement->get_result();
	$response = array('response' => true, 'data' => array());
	
	while ($row = $result->fetch_assoc())
		$response['data'][] = $row;

	return $response;
}


require_once '../../sql_connection.php';

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

if (!isset($_POST['type']))
	die('{"response":false,"error":"Book API `type` not provided"}');

if (!function_exists($_POST['type']))
	die('{"response":false,"error":"Book API function `'.$_POST['type'].'` does not exist"}');

if (!array_key_exists($LogInLevel, $PermissionLevels) || !in_array($_POST['type'], $PermissionLevels[$LogInLevel]))
{
	header("HTTP/1.0 401 Unauthorized");
	die('{"response":"false","error":"You do not have the right permissions"}');
}

echo json_encode(($_POST['type']($PermissionLevels, $LogInLevel, GetDBConnection())), JSON_UNESCAPED_UNICODE);

?>