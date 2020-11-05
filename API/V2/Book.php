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
	0 => array('AddBook', 'CheckBookExists', 'EditBook', 'GetBook', 'RemoveBook', 'SearchBook'),
	1 => array('AddBook', 'CheckBookExists', 'EditBook', 'GetBook', 'RemoveBook', 'SearchBook'),
	2 => array(                                          'GetBook',               'SearchBook'),
	3 => array(                                          'GetBook',               'SearchBook')
);


function AddBook($PermissionLevels, $LogInLevel, $DatabaseConnection)
{
	if (!isset($_POST['Identifier']) || $_POST['Identifier'] == '')
		return array('response' => false, 'error' => 'Identifier is not provided', 'errorCode' => 3321);

	$Exists = CheckBookExists($PermissionLevels, $LogInLevel, $DatabaseConnection);
	if ($Exists['response'])
		return array('response' => false, 'error' => 'A book with that Identifier already exists', 'errorCode' => 3322);
	else if (isset($Exists['error']))
		return $Exists;

	$Identifier = $_POST['Identifier'];
	$Title = (isset($_POST['Title'])) ? $_POST['Title'] : '';
	$AuthorIDs = (isset($_POST['AuthorIDs']) && is_array(json_decode($_POST['AuthorIDs'], false))) ? $_POST['AuthorIDs'] : '[]';
	$Dewey = (isset($_POST['Dewey'])) ? $_POST['Dewey'] : '';
	$ISBN = (isset($_POST['ISBN'])) ? $_POST['ISBN'] : '';
	$Quantity = (isset($_POST['Quantity']) && is_int($_POST['Quantity'])) ? $_POST['Quantity'] : 1;
	$Metadata = '{}';

	$query = 'INSERT INTO `books` (`ID`, `Identifier`, `Title`, `AuthorIDs`, `Dewey`, `ISBN`, `Quantity`, `Metadata`) VALUES (NULL, ?, ?, ?, ?, ?, ?, ?);';
	$statement = $DatabaseConnection->prepare($query);
	if (!$statement)
		return array('response' => false, 'error' => 'Could not prepare statement in BookV2::'.__FUNCTION__, 'errorCode' => 3101);
	$statement->bind_param('sssssis', $Identifier, $Title, $AuthorIDs, $Dewey, $ISBN, $Quantity, $Metadata);
	if (!$statement->execute())
		return array('response' => false, 'error' => 'Could not execute statement in BookV2::'.__FUNCTION__, 'errorCode' => 3102);
	
	return array('response' => true);
}

function CheckBookExists($PermissionLevels, $LogInLevel, $DatabaseConnection)
{
	if (!isset($_POST['Identifier']) || $_POST['Identifier'] == '')
		return array('response' => false, 'error' => 'Identifier is not provided', 'errorCode' => 3321);

	$Identifier = $_POST['Identifier'];

	$query = 'SELECT `ID` FROM `books` WHERE `Identifier` = ?;';
	$statement = $DatabaseConnection->prepare($query);
	if (!$statement)
		return array('response' => false, 'error' => 'Could not prepare statement in BookV2::'.__FUNCTION__, 'errorCode' => 3101);
	$statement->bind_param('s', $Identifier);
	if (!$statement->execute())
		return array('response' => false, 'error' => 'Could not execute statement in BookV2::'.__FUNCTION__, 'errorCode' => 3102);

	$result = $statement->get_result();
	return array('response' => $result->num_rows != 0);
}

function EditBook($PermissionLevels, $LogInLevel, $DatabaseConnection)
{
	if (!isset($_POST['Identifier']) || $_POST['Identifier'] == '')
		return array('response' => false, 'error' => 'Identifier is not provided', 'errorCode' => 3321);
	
	$Exists = CheckBookExists($PermissionLevels, $LogInLevel, $DatabaseConnection);
	if (!$Exists['response'])
		return array('response' => false, 'error' => 'A book with that Identifier does not exist', 'errorCode' => 3323);
	else if (isset($Exists['error']))
		return $Exists;

	$OldBook = GetBook($PermissionLevels, $LogInLevel, $DatabaseConnection);
	if (!$OldBook['response'])
		return array('response' => false, 'error' => 'An unexpected error occured while retriving old Book Data', 'errorCode' => 3324);

	$Identifier = $_POST['Identifier'];
	$Title = (isset($_POST['Title'])) ? $_POST['Title'] : $OldBook['data']['Title'];
	$AuthorIDs = (isset($_POST['AuthorIDs']) && is_array(json_decode($_POST['AuthorIDs'], false))) ? $_POST['AuthorIDs'] : $OldBook['data']['AuthorIDs'];
	$Dewey = (isset($_POST['Dewey'])) ? $_POST['Dewey'] : $OldBook['data']['Dewey'];
	$ISBN = (isset($_POST['ISBN'])) ? $_POST['ISBN'] : $OldBook['data']['ISBN'];
	$Quantity = (isset($_POST['Quantity']) && is_int($_POST['Quantity'])) ? $_POST['Quantity'] : $OldBook['data']['Quantity'];
	$Metadata = $OldBook['data']['Metadata'];

	$query = 'UPDATE `books` SET `Title` = ?, `AuthorIDs` = ?, `Dewey` = ?, `ISBN` = ?, `Quantity` = ?, `Metadata` = ? WHERE `Identifier` = ?;';
	$statement = $DatabaseConnection->prepare($query);
	if (!$statement)
		return array('response' => false, 'error' => 'Could not prepare statement in BookV2::'.__FUNCTION__, 'errorCode' => 3101);
	$statement->bind_param('ssssiss', $Title, $AuthorIDs, $Dewey, $ISBN, $Quantity, $Metadata, $Identifier);
	if (!$statement->execute())
		return array('response' => false, 'error' => 'Could not execute statement in BookV2::'.__FUNCTION__, 'errorCode' => 3102);

	return array('response' => true);
}

function GetBook($PermissionLevels, $LogInLevel, $DatabaseConnection)
{
	if (!isset($_POST['Identifier']) || $_POST['Identifier'] == '')
		return array('response' => false, 'error' => 'Identifier is not provided', 'errorCode' => 3321);
	
	$Exists = CheckBookExists($PermissionLevels, $LogInLevel, $DatabaseConnection);
	if (!$Exists['response'])
		return array('response' => false, 'error' => 'A book with that Identifier does not exist', 'errorCode' => 3323);
	else if (isset($Exists['error']))
		return $Exists;

	$Identifier = $_POST['Identifier'];

	$query = 'SELECT `Identifier`, `Title`, `AuthorIDs`, `Dewey`, `ISBN`, `Quantity`, `QuantityBorrowed`, `Metadata` FROM `books` WHERE `Identifier` = ?;';
	$statement = $DatabaseConnection->prepare($query);
	if (!$statement)
		return array('response' => false, 'error' => 'Could not prepare statement in BookV2::'.__FUNCTION__, 'errorCode' => 3101);
	$statement->bind_param('s', $Identifier);
	if (!$statement->execute())
		return array('response' => false, 'error' => 'Could not execute statement in BookV2::'.__FUNCTION__, 'errorCode' => 3102);

	$result = $statement->get_result();
	if ($result->num_rows != 1)
		return array('response' => false, 'error' => 'A book with that Identifier does not exist', 'errorCode' => 3323);
	
	return array('response' => true, 'data' => $result->fetch_assoc());
}

function RemoveBook($PermissionLevels, $LogInLevel, $DatabaseConnection)
{
	if (!isset($_POST['Identifier']) || $_POST['Identifier'] == '')
		return array('response' => false, 'error' => 'Identifier is not provided', 'errorCode' => 3321);
	
	$Exists = CheckBookExists($PermissionLevels, $LogInLevel, $DatabaseConnection);
	if (!$Exists['response'])
		return array('response' => false, 'error' => 'A book with that Identifier does not exist', 'errorCode' => 3323);
	else if (isset($Exists['error']))
		return $Exists;

	$Identifier = $_POST['Identifier'];

	$query = 'DELETE FROM `books` WHERE `Identifier` = ?;';
	$statement = $DatabaseConnection->prepare($query);
	if (!$statement)
		return array('response' => false, 'error' => 'Could not prepare statement in BookV2::'.__FUNCTION__, 'errorCode' => 3101);
	$statement->bind_param('s', $Identifier);
	if (!$statement->execute())
		return array('response' => false, 'error' => 'Could not execute statement in BookV2::'.__FUNCTION__, 'errorCode' => 3102);
	
	$Exists = CheckBookExists($PermissionLevels, $LogInLevel, $DatabaseConnection);
	if ($Exists['response'])
		return array('response' => false, 'error' => 'An unexpected error occured while retriving old Book Data', 'errorCode' => 3325);
	return array('response' => true);
}

function SearchBook($PermissionLevels, $LogInLevel, $DatabaseConnection)
{
	if (!isset($_POST['SearchTag']) || $_POST['SearchTag'] == '' || strlen($_POST['SearchTag']) < 2)
		return array('response' => false, 'error' => 'SearchTag is not provided', 'errorCode' => 3321);

	$SearchTag = $_POST['SearchTag'].'%';
	$Skip = (isset($_POST['Skip'])) ? $_POST['Skip'] : 0;
	$Limit = (isset($_POST['Limit']) && $_POST['Limit'] <= 100) ? $_POST['Limit'] : 20;

	$query = 'SELECT `Identifier`, `Title`, `AuthorIDs`, `Dewey`, `ISBN`, `Quantity`, `QuantityBorrowed`, `Metadata` FROM `books` WHERE ( UPPER(`Title`) LIKE UPPER(?) OR UPPER(`Dewey`) LIKE UPPER(?) OR UPPER(`ISBN`) LIKE UPPER(?) OR `Identifier` = ?) ORDER BY `Identifier` LIMIT '.$Skip.', '.$Limit.';';
	$statement = $DatabaseConnection->prepare($query);
	if (!$statement)
		return array('response' => false, 'error' => 'Could not prepare statement in BookV2::'.__FUNCTION__, 'errorCode' => 3101);
	$statement->bind_param('ssss', $SearchTag, $SearchTag, $SearchTag, $SearchIdentifier);
	if (!$statement->execute())
		return array('response' => false, 'error' => 'Could not execute statement in BookV2::'.__FUNCTION__, 'errorCode' => 3102);

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