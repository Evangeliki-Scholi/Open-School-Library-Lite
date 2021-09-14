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
	0 => array('AddCharge', 'ClearCharge', 'GetCharge', 'ListActiveCharges', 'ListCharges', 'SearchCharge'),
	1 => array('AddCharge', 'ClearCharge', 'GetCharge', 'ListActiveCharges', 'ListCharges', 'SearchCharge')
);


function AddCharge($PermissionLevels, $LogInLevel, $DatabaseConnection)
{
	if (!isset($_POST['BookIdentifier']) || $_POST['BookIdentifier'] == '')
		return array('response' => false, 'error' => 'BookIdentifier is not provided', 'errorCode' => 3321);
	if (!isset($_POST['UserIdentifier']) || $_POST['UserIdentifier'] == '')
		return array('response' => false, 'error' => 'UserIdentifier is not provided', 'errorCode' => 3321);

	$BookIdentifier = $_POST['BookIdentifier'];
	$UserIdentifier = $_POST['UserIdentifier'];

	$dateNow = date ('Y-m-d');
	$dateInTwoWeeks = date('Y-m-d', strtotime('+2 weeks'));

	$query = 'UPDATE `books` SET `QuantityBorrowed` = `QuantityBorrowed` + 1 WHERE `Identifier` = ?;';
	$statement = $DatabaseConnection->prepare($query);
	if (!$statement)
		return array('response' => false, 'error' => 'Could not prepare statement in BookV2::'.__FUNCTION__, 'errorCode' => 3101);
	$statement->bind_param('s', $BookIdentifier);
	if (!$statement->execute())
		return array('response' => false, 'error' => 'Could not execute statement in BookV2::'.__FUNCTION__, 'errorCode' => 3102);
	
	$query = 'INSERT INTO `charges` (`ID`, `BookIdentifier`, `UserIdentifier`, `BorrowDate`, `ReturnDate`, `Active`, `Metadata`) VALUES (NULL, ?, ?, ?, ?, 1, "{}");';
	$statement = $DatabaseConnection->prepare($query);
	if (!$statement)
		return array('response' => false, 'error' => 'Could not prepare statement in BookV2::'.__FUNCTION__, 'errorCode' => 3101);
	$statement->bind_param('ssss', $BookIdentifier, $UserIdentifier, $dateNow, $dateInTwoWeeks);
	if (!$statement->execute())
		return array('response' => false, 'error' => 'Could not execute statement in BookV2::'.__FUNCTION__, 'errorCode' => 3102);

	return array('response' => true);
}

function ClearCharge($PermissionLevels, $LogInLevel, $DatabaseConnection)
{
	if (!isset($_POST['Identifier']) || $_POST['Identifier'] == '')
		return array('response' => false, 'error' => 'Identifier is not provided', 'errorCode' => 3321);

	$Identifier = $_POST['Identifier'];
	$dateNow = date ('Y-m-d');

	$Charge = GetCharge($PermissionLevels, $LogInLevel, $DatabaseConnection);
	if (!$Charge['response'])
		return array('response' => false, 'error' => 'An unexpected error occured while retriving Charge Data', 'errorCode' => 3324);
	if ($Charge['data']['Active'] == false)
		return array('response' => false, 'error' => 'Charge is already cleared');

	$query = 'UPDATE `books` SET `QuantityBorrowed` = `QuantityBorrowed` - 1 WHERE `Identifier` = ?;';
	$statement = $DatabaseConnection->prepare($query);
	if (!$statement)
		return array('response' => false, 'error' => 'Could not prepare statement in BookV2::'.__FUNCTION__, 'errorCode' => 3101);
	$statement->bind_param('s', $Charge['data']['BookIdentifier']);
	if (!$statement->execute())
		return array('response' => false, 'error' => 'Could not execute statement in BookV2::'.__FUNCTION__, 'errorCode' => 3102);

	$query = 'UPDATE `charges` SET `Active` = 0, `ReturnDate` = ? WHERE `ID` = ?;';
	$statement = $DatabaseConnection->prepare($query);
	if (!$statement)
		return array('response' => false, 'error' => 'Could not prepare statement in BookV2::'.__FUNCTION__, 'errorCode' => 3101);
	$statement->bind_param('ss', $dateNow, $Charge['data']['ID']);
	if (!$statement->execute())
		return array('response' => false, 'error' => 'Could not execute statement in BookV2::'.__FUNCTION__, 'errorCode' => 3102);

	return array('response' => true);
}

function GetCharge($PermissionLevels, $LogInLevel, $DatabaseConnection)
{
	if (!isset($_POST['Identifier']) || $_POST['Identifier'] == '')
		return array('response' => false, 'error' => 'Identifier is not provided', 'errorCode' => 3321);

	$Identifier = $_POST['Identifier'];

	$query = 'SELECT `charges`.`ID`, `books`.`Title`, `charges`.`BookIdentifier`, `users`.`Name`, `charges`.`BorrowDate`, `charges`.`ReturnDate`, `charges`.`Active` FROM `charges` LEFT JOIN `users` ON `charges`.`UserIdentifier` = `users`.`Identifier` LEFT JOIN `books` ON `charges`.`BookIdentifier` = `books`.`Identifier` WHERE `charges`.`ID` = ? ORDER BY `charges`.`ID` LIMIT 0, 1;';
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

function ListActiveCharges($PermissionLevels, $LogInLevel, $DatabaseConnection)
{
	$Skip = (isset($_POST['Skip'])) ? $_POST['Skip'] : 0;
	$Limit = (isset($_POST['Limit']) && $_POST['Limit'] <= 100) ? $_POST['Limit'] : 20;

	$query = 'SELECT `charges`.`ID`, `charges`.`BookIdentifier`, `users`.`Name`, `charges`.`BorrowDate`, `charges`.`Active` FROM `charges` LEFT JOIN `users` ON `charges`.`UserIdentifier` = `users`.`Identifier` WHERE `charges`.`Active` = true ORDER BY `charges`.`ID` LIMIT '.$Skip.', '.$Limit.';';
	$statement = $DatabaseConnection->prepare($query);
	if (!$statement)
		return array('response' => false, 'error' => 'Could not prepare statement in BookV2::'.__FUNCTION__, 'errorCode' => 3101);
	if (!$statement->execute())
		return array('response' => false, 'error' => 'Could not execute statement in BookV2::'.__FUNCTION__, 'errorCode' => 3102);

	$result = $statement->get_result();
	$response = array('response' => true, 'data' => array());
	
	while ($row = $result->fetch_assoc())
		$response['data'][] = $row;

	return $response;
}

function ListCharges($PermissionLevels, $LogInLevel, $DatabaseConnection)
{
	$Skip = (isset($_POST['Skip'])) ? $_POST['Skip'] : 0;
	$Limit = (isset($_POST['Limit']) && $_POST['Limit'] <= 100) ? $_POST['Limit'] : 20;

	$query = 'SELECT `charges`.`ID`, `charges`.`BookIdentifier`, `users`.`Name`, `charges`.`BorrowDate`, `charges`.`ReturnDate`, `charges`.`Active` FROM `charges` LEFT JOIN `users` ON `charges`.`UserIdentifier` = `users`.`Identifier` ORDER BY `charges`.`ID` LIMIT '.$Skip.', '.$Limit.';';
	$statement = $DatabaseConnection->prepare($query);
	if (!$statement)
		return array('response' => false, 'error' => 'Could not prepare statement in BookV2::'.__FUNCTION__, 'errorCode' => 3101);
	if (!$statement->execute())
		return array('response' => false, 'error' => 'Could not execute statement in BookV2::'.__FUNCTION__, 'errorCode' => 3102);

	$result = $statement->get_result();
	$response = array('response' => true, 'data' => array());
	
	while ($row = $result->fetch_assoc())
		$response['data'][] = $row;

	return $response;
}

function SearchCharge($PermissionLevels, $LogInLevel, $DatabaseConnection)
{
	return array('response' => false, 'error' => 'SearchCharge is not yet activated');
}


require_once '../../sql_connection.php';

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

if (!isset($_POST['type']))
	die('{"response":false,"error":"Charge API `type` not provided"}');

if (!function_exists($_POST['type']))
	die('{"response":false,"error":"Charge API function `'.$_POST['type'].'` does not exist"}');

if (!array_key_exists($LogInLevel, $PermissionLevels) || !in_array($_POST['type'], $PermissionLevels[$LogInLevel]))
{
	header("HTTP/1.0 401 Unauthorized");
	die('{"response":"false","error":"You do not have the right permissions"}');
}

echo json_encode(($_POST['type']($PermissionLevels, $LogInLevel, GetDBConnection())), JSON_UNESCAPED_UNICODE);

?>