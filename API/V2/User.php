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
    0 => array('AddUser', 'CheckUserExists', 'EditUser',            'GetUser',          'LogOut', 'RemoveUser', 'SearchUser'),
    1 => array('AddUser', 'CheckUserExists', 'EditUser',            'GetUser',          'LogOut', 'RemoveUser', 'SearchUser'),
    2 => array(                              'EditUser',            'GetUser',          'LogOut', 'SearchUser'),
    3 => array(                                          'GetAlgo',            'LogIn')
);


function AddUser($PermissionLevels, $LogInLevel, $DatabaseConnection)
{
    if (!isset($_POST['Identifier']) || $_POST['Identifier'] == '')
        return array('response' => false, 'error' => 'Identifier is not provided', 'errorCode' => 3421);
    if (!isset($_POST['Name']) || $_POST['Name'] == '')
        return array('response' => false, 'error' => 'Name is not provided', 'errorCode' => 3421);
    if (!isset($_POST['Username']) || $_POST['Username'] == '')
        return array('response' => false, 'error' => 'Username is not provided', 'errorCode' => 3421);
    if (!isset($_POST['Grade']) || $_POST['Grade'] == '')
        return array('response' => false, 'error' => 'Grade is not provided', 'errorCode' => 3421);

    $Exists = CheckUserExists($PermissionLevels, $LogInLevel, $DatabaseConnection);
    if ($Exists['response'])
        return array('response' => false, 'error' => 'A user with that Identifier already exists', 'errorCode' => 3422);
    else if (isset($Exists['error']))
        return $Exists;

    $Identifier = $_POST['Identifier'];
    $Name = $_POST['Name'];
    $Username = $_POST['Username'];
    $Email = (isset($_POST['Email'])) ? $_POST['Email'] : '';
    $Password = (isset($_POST['Password'])) ? password_hash($_POST['Password'], PASSWORD_DEFAULT) : '';
    $Algo = (isset($_POST['Algo'])) ? $_POST['Algo'] : '';
    $Level = (isset($_POST['Level']) && 0 == $LogInLevel) ? $_POST['Level'] : 2;
    $Grade = $_POST['Grade'];

    $query = 'INSERT INTO `users` (`ID`, `Identifier`, `Name`, `Username`, `Email`, `Password`, `Algo`, `Level`, `Grade`, `Metadata`) VALUES (NULL, ?, ?, ?, ?, ?, ?, ?, ?, "{}");';
    $statement = $DatabaseConnection->prepare($query);
    if (!$statement)
        return array('response' => false, 'error' => 'Could not prepare statement in UserV2::'.__FUNCTION__, 'errorCode' => 3101);
    $statement->bind_param('ssssssis', $Identifier, $Name, $Username, $Email, $Password, $Algo, $Level, $Grade);
    if (!$statement->execute())
        return array('response' => false, 'error' => 'Could not execute statement in UserV2::'.__FUNCTION__, 'errorCode' => 3102);
    
    return array('response' => true);
}

function CheckUserExists($PermissionLevels, $LogInLevel, $DatabaseConnection)
{
    if (!isset($_POST['Identifier']) || $_POST['Identifier'] == '')
        return array('response' => false, 'error' => 'Identifier is not provided', 'errorCode' => 3421);

    $Identifier = $_POST['Identifier'];

    $query = 'SELECT `ID` FROM `users` WHERE `Identifier` = ?;';
    $statement = $DatabaseConnection->prepare($query);
    if (!$statement)
        return array('response' => false, 'error' => 'Could not prepare statement in UserV2::'.__FUNCTION__, 'errorCode' => 3101);
    $statement->bind_param('s', $Identifier);
    if (!$statement->execute())
        return array('response' => false, 'error' => 'Could not execute statement in UserV2::'.__FUNCTION__, 'errorCode' => 3102);

    $result = $statement->get_result();
    return array('response' => $result->num_rows != 0);
}

function EditUser($PermissionLevels, $LogInLevel, $DatabaseConnection)
{
    $Identifier = '';
    if (0 <= $LogInLevel && $LogInLevel <= 1 && isset($_POST['Identifier']) && $_POST['Identifier'] != '')
        $Identifier = $_POST['Identifier'];
    else
        $Identifier = $_SESSION['Identifier'];

    $Exists = CheckUserExists($PermissionLevels, $LogInLevel, $DatabaseConnection);
    if (!$Exists['response'])
        return array('response' => false, 'error' => 'A user with that Identifier does not exists', 'errorCode' => 3422);
    else if (isset($Exists['error']))
        return $Exists;

    $OldUser = GetUser($PermissionLevels, $LogInLevel, $DatabaseConnection);
    if (!$OldUser['response'])
        return array('response' => false, 'error' => 'An unexpected error occured while retriving old User Data', 'errorCode' => 3424);

    $Name = $_POST['Name'];
    $Username = (isset($_POST['Username'])) ? $_POST['Username'] : $OldUser['data']['Username'];
    $Email = (isset($_POST['Email'])) ? $_POST['Email'] : $OldUser['data']['Email'];
    $Level = (isset($_POST['Level']) && 0 == $LogInLevel) ? $_POST['Level'] : $OldUser['data']['Level'];
    $Grade = (isset($_POST['Grade'])) ? $_POST['Grade'] : $OldUser['data']['Grade'];
    $Metadata = $OldUser['data']['Metadata'];

    $query = 'UPDATE `users` SET `Name` = ?, `Username` = ?, `Email` = ?, `Level` = ?, `Grade` = ?, `Metadata` = ? WHERE `Identifier` = ?;';
    $statement = $DatabaseConnection->prepare($query);
    if (!$statement)
        return array('response' => false, 'error' => 'Could not prepare statement in BookV2::'.__FUNCTION__, 'errorCode' => 3101);
    $statement->bind_param('sssisss', $Name, $Username, $Email, $Level, $Grade, $Metadata, $Identifier);
    if (!$statement->execute())
        return array('response' => false, 'error' => 'Could not execute statement in BookV2::'.__FUNCTION__, 'errorCode' => 3102);

    return array('response' => true);
}

function GetAlgo($PermissionLevels, $LogInLevel, $DatabaseConnection)
{
    if (!isset($_POST['Username']) || $_POST['Username'] == '')
        return array('response' => false, 'error' => 'Username is not provided', 'errorCode' => 3421);
    

    $Username = $_POST['Username'];

    $query = 'SELECT `Algo` FROM `users` WHERE `Username` = ?;';
    $statement = $DatabaseConnection->prepare($query);
    if (!$statement)
        return array('response' => false, 'error' => 'Could not prepare statement in UserV2::'.__FUNCTION__, 'errorCode' => 3101);
    $statement->bind_param('s', $Username);
    if (!$statement->execute())
        return array('response' => false, 'error' => 'Could not execute statement in UserV2::'.__FUNCTION__, 'errorCode' => 3102);

    $result = $statement->get_result();
    if ($result->num_rows != 1)
        return array('response' => false, 'error' => 'A user with that Username does not exists', 'errorCode' => 3422);
    $row = $result->fetch_assoc();

    return array('response' => true, 'data' => $row);
}

function GetUser($PermissionLevels, $LogInLevel, $DatabaseConnection)
{
    $Identifier = '';
    if (0 <= $LogInLevel && $LogInLevel <= 1 && isset($_POST['Identifier']) && $_POST['Identifier'] != '')
        $Identifier = $_POST['Identifier'];
    else
        $Identifier = $_SESSION['Identifier'];

    $query = 'SELECT `Name`, `Username`, `Email`, `Level`, `Metadata`, `Grade`, `Metadata` FROM `users` WHERE `Identifier` = ?;';
    $statement = $DatabaseConnection->prepare($query);
    if (!$statement)
        return array('response' => false, 'error' => 'Could not prepare statement in UserV2::'.__FUNCTION__, 'errorCode' => 3101);
    $statement->bind_param('s', $Identifier);
    if (!$statement->execute())
        return array('response' => false, 'error' => 'Could not execute statement in UserV2::'.__FUNCTION__, 'errorCode' => 3102);
    
    $result = $statement->get_result();
    if ($result->num_rows != 1)
        return array('response' => false, 'error' => 'A user with that Identifier does not exist', 'errorCode' => 3423);
    
    return array('response' => true, 'data' => $result->fetch_assoc());
}

function LogIn($PermissionLevels, $LogInLevel, $DatabaseConnection)
{
    if (!isset($_POST['Username']) || $_POST['Username'] == '')
        return array('response' => false, 'error' => 'Username is not provided', 'errorCode' => 3421);
    if (!isset($_POST['Password']) || $_POST['Password'] == '')
        return array('response' => false, 'error' => 'Password is not provided', 'errorCode' => 3421);

    $Username = $_POST['Username'];
    $Password = $_POST['Password'];

    $query = 'SELECT `Identifier`, `Name`, `Username`, `Email`, `Password`, `Level` FROM `users` WHERE `Username` = ?;';
    $statement = $DatabaseConnection->prepare($query);
    if (!$statement)
        return array('response' => false, 'error' => 'Could not prepare statement in UserV2::'.__FUNCTION__, 'errorCode' => 3101);
    $statement->bind_param('s', $Username);
    if (!$statement->execute())
        return array('response' => false, 'error' => 'Could not execute statement in UserV2::'.__FUNCTION__, 'errorCode' => 3102);

    $result = $statement->get_result();
    if ($result->num_rows != 1)
        return array('response' => false, 'error' => 'A user with that Username does not exists', 'errorCode' => 3422);
    $row = $result->fetch_assoc();
    if (password_verify($Password, $row['Password']))
    {
        if (!isset($_SESSION))
            session_start();
        $_SESSION['Logged In'] = true;
        $_SESSION['Identifier'] = $row['Identifier'];
        $_SESSION['Name'] = $row['Name'];
        $_SESSION['Username'] = $row['Username'];
        $_SESSION['Email'] = $row['Email'];
        $_SESSION['Level'] = $row['Level'];
        return array('response' => true);
    }

    return array('response' => false);
}

function LogOut($PermissionLevels, $LogInLevel, $DatabaseConnection)
{
    if (!isset($_SESSION))
        session_start();
    $_SESSION['Logged In'] = false;
    $_SESSION['Level'] = 3;
    $_SESSION['Name'] = $_SESSION['Username'] = $_SESSION['Email'] = '';
    session_unset();
    session_destroy();
    return array('response' => true);
}

function RemoveUser($PermissionLevels, $LogInLevel, $DatabaseConnection)
{
    $Identifier = '';
    if (0 <= $LogInLevel && $LogInLevel <= 1 && isset($_POST['Identifier']) && $_POST['Identifier'] != '')
        $Identifier = $_POST['Identifier'];
    else
        $Identifier = $_SESSION['Identifier'];

    $query = 'DELETE FROM `users` WHERE `Identifier` = ?;';
    $statement = $DatabaseConnection->prepare($query);
    if (!$statement)
        return array('response' => false, 'error' => 'Could not prepare statement in UserV2::'.__FUNCTION__, 'errorCode' => 3101);
    $statement->bind_param('s', $Identifier);
    if (!$statement->execute())
        return array('response' => false, 'error' => 'Could not execute statement in UserV2::'.__FUNCTION__, 'errorCode' => 3102);

    return array('response' => true);
}

function SearchUser($PermissionLevels, $LogInLevel, $DatabaseConnection)
{
    if (!isset($_POST['SearchTag']) || $_POST['SearchTag'] == '' || strlen($_POST['SearchTag']) < 2)
        return array('response' => false, 'error' => 'SearchTag is not provided', 'errorCode' => 3321);

    $SearchTag = $_POST['SearchTag'].'%';
    $Skip = (isset($_POST['Skip'])) ? $_POST['Skip'] : 0;
    $Limit = (isset($_POST['Limit']) && $_POST['Limit'] <= 100) ? $_POST['Limit'] : 20;

    $query = 'SELECT `Identifier`, `Name`, `Username`, `Email`, `Level`, `Grade`, `Metadata` WHERE ( UPPER(`Name`) LIKE UPPER(?) OR UPPER(`Username`) LIKE UPPER(?) OR UPPER(`Email`) LIKE UPPER(?) OR `Identifier` = ?) ORDER BY `Identifier` LIMIT '.$Skip.', '.$Limit.';';
    $statement = $DatabaseConnection->prepare($query);
    if (!$statement)
        return array('response' => false, 'error' => 'Could not prepare statement in UserV2::'.__FUNCTION__, 'errorCode' => 3101);
    $statement->bind_param('ssss', $SearchTag, $SearchTag, $SearchTag, $SearchIdentifier);
    if (!$statement->execute())
        return array('response' => false, 'error' => 'Could not execute statement in UserV2::'.__FUNCTION__, 'errorCode' => 3102);

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
    die('{ "response" : false, "error" : "User API `type` not provided" }');

if (!function_exists($_POST['type']))
    die('{ "response" : false, "error" : "User API function `'.$_POST['type'].'` does not exist" }');

if (!array_key_exists($LogInLevel, $PermissionLevels) || !in_array($_POST['type'], $PermissionLevels[$LogInLevel]))
{
    header("HTTP/1.0 401 Unauthorized");
    die('{ "response" : "false", "error" : "You do not have the right permissions" }');
}

echo json_encode(($_POST['type']($PermissionLevels, $LogInLevel, GetDBConnection())), JSON_UNESCAPED_UNICODE);

?>