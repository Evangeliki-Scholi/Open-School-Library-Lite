<?php

/*
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

header('access-control-allow-origin *');
chdir(dirname(__FILE__));

session_start();
$elevated = (isset($_SESSION['Logged in']) && isset($_SESSION['Level'])) ? $_SESSION['Level'] : 3;

$permissionLevels = array(
    0 => array('AddAdmin', 'AddUser', 'CheckUserExists', 'EditSelf', 'EditUser',            'GetSelf', 'GetUser',         'LogOut',                  'RemoveUser', 'SearchUsers'),
    1 => array(            'AddUser', 'CheckUserExists', 'EditSelf', 'EditUser',            'GetSelf', 'GetUser',         'LogOut',                  'RemoveUser', 'SearchUsers'),
    2 => array(                       'CheckUserExists', 'EditSelf',                        'GetSelf',                    'LogOut'),
    3 => array(                                                                  'GetAlgo',                      'LogIn',           'PasswordReset')
);

function AddAdmin($permissionLevels, $elevated)
{
    if (!array_key_exists($elevated, $permissionLevels) || !in_array(__FUNCTION__, $permissionLevels[$elevated]))
        return array('response' => false, 'error' => 'You do not have the right permissions');

    if (!isset($_POST['Identifier']) || $_POST['Identifier'] == '')
        return array('response' => false, 'error' => 'Identifier can not be empty');
    if (!isset($_POST['Name']) || $_POST['Name'] == '')
        return array('response' => false, 'error' => 'Name can not be empty');
    if (!isset($_POST['Username']) || $_POST['Username'] == '')
        return array('response' => false, 'error' => 'Username can not be empty');
    if (!isset($_POST['Email']) || $_POST['Email'] == '')
        return array('response' => false, 'error' => 'Email can not be empty');
    if (!isset($_POST['Password']) || $_POST['Password'] == '')
        return array('response' => false, 'error' => 'Password can not be empty');
    if (!isset($_POST['Algo']) || $_POST['Algo'] == '')
        return array('response' => false, 'error' => 'Algo can not be empty');
    if (!isset($_POST['Level']) || $_POST['Level'] == '')
        return array('response' => false, 'error' => 'Level can not be empty');
    if (!is_numeric($_POST['Level']) || (int)$_POST['Level'] < 0 || 1 < (int)$_POST['Level'])
        return array('response' => false, 'error' => 'Wrong level parameter');

    $exists = CheckUserExists($permissionLevels, $elevated);
    if (isset($exists['error']))
        return array('response' => false, 'error' => $exists['error']);
    if (isset($exists['response']) && $exists['reponse'] == true)
        return array('response' => false, 'error' => 'User already exists');

    $Identifier = $_POST['Identifier'];
    $Name = $_POST['Name'];
    $Username = $_POST['Username'];
    $Email = $_POST['Email'];
    $Password = password_hash($_POST['Password'], PASSWORD_DEFAULT);
    $Algo = $_POST['Algo'];
    $Level = (int)$_POST['Level'];
    $Grade = (isset($_POST['Grade'])) ? $_POST['Grade'] : NULL;

    require_once '../../sql_connection.php';
    $conn = GetDBConnection();

    $query = 'INSERT INTO `users` (`ID`, `Identifier`, `Name`, `Username`, `Email`, `Password`, `Algo`, `Level`, `Grade`, `Metadata`) VALUES (NULL, ?, ?, ?, ?, ?, ?, ?, ?, "{}");';
    $statement = $conn->prepare($query);
    if (!$statement)
    {
        $conn->close();
        return array('response' => false, 'error' => 'Could not prepare statement in '.__FILE__.':'.__FUNCTION__.':'.__LINE__);
    }
    $statement->bind_param('ssssssis', $Identifier, $Name, $Username, $Email, $Password, $Algo, $Level, $Grade);
    if (!$statement->execute())
    {
        $statement->close();
        $conn->close();
        return array('response' => false, 'error' => 'Could not execute statement in '.__FILE__.':'.__FUNCTION__.':'.__LINE__);
    }

    $statement->close();
    $conn->close();
    
    $exists = CheckUserExists($permissionLevels, $elevated);
    if (isset($exists['error']))
        return array('response' => false, 'error' => $exists['error']);
    if (isset($exists['response']) && $exists['reponse'] == false)
        return array('response' => false, 'error' => 'User could not be added succesfully');
    
    return array('response' => true);
}

function AddUser($permissionLevels, $elevated)
{
    if (!array_key_exists($elevated, $permissionLevels) || !in_array(__FUNCTION__, $permissionLevels[$elevated]))
        return array('response' => false, 'error' => 'You do not have the right permissions');

    if (!isset($_POST['Identifier']) || $_POST['Identifier'] == '')
        return array('response' => false, 'error' => 'Identifier can not be empty');
    if (!isset($_POST['Name']) || $_POST['Name'] == '')
        return array('response' => false, 'error' => 'Name can not be empty');
    if (!isset($_POST['Username']) || $_POST['Username'] == '')
        return array('response' => false, 'error' => 'Username can not be empty');
    if (!isset($_POST['Password']) || $_POST['Password'] == '')
        return array('response' => false, 'error' => 'Password can not be empty');
    if (!isset($_POST['Algo']) || $_POST['Algo'] == '')
        return array('response' => false, 'error' => 'Algo can not be empty');

    $exists = CheckUserExists($permissionLevels, $elevated);
    if (isset($exists['error']))
        return array('response' => false, 'error' => $exists['error']);
    if (isset($exists['response']) && $exists['response'] == true)
        return array('response' => false, 'error' => 'User already exists');

    $Identifier = $_POST['Identifier'];
    $Name = $_POST['Name'];
    $Username = $_POST['Username'];
    $Email = (isset($_POST['Email'])) ? $_POST['Email'] : NULL;
    $Password = password_hash($_POST['Password'], PASSWORD_DEFAULT);
    $Algo = $_POST['Algo'];
    $Grade = $_POST['Grade'];

    require_once '../../sql_connection.php';
    $conn = GetDBConnection();

    $query = 'INSERT INTO `users` (`ID`, `Identifier`, `Name`, `Username`, `Email`, `Password`, `Algo`, `Level`, `Grade`, `Metadata`) VALUES (NULL, ?, ?, ?, ?, ?, ?, 2, ?, "{}");';
    $statement = $conn->prepare($query);
    if (!$statement)
    {
        $conn->close();
        return array('response' => false, 'error' => 'Could not prepare statement in '.__FILE__.':'.__FUNCTION__.':'.__LINE__);
    }
    $statement->bind_param('sssssss', $Identifier, $Name, $Username, $Email, $Password, $Algo, $Grade);
    if (!$statement->execute())
    {
        $statement->close();
        $conn->close();
        return array('response' => false, 'error' => 'Could not execute statement in '.__FILE__.':'.__FUNCTION__.':'.__LINE__);
    }

    $statement->close();
    $conn->close();
    
    $exists = CheckUserExists($permissionLevels, $elevated);
    if (isset($exists['error']))
        return array('response' => false, 'error' => $exists['error']);
    if (isset($exists['response']) && $exists['response'] == false)
        return array('response' => false, 'error' => 'User could not be added succesfully');
    
    return array('response' => true);
}

function CheckUserExists($permissionLevels, $elevated)
{
    if (!array_key_exists($elevated, $permissionLevels) || !in_array(__FUNCTION__, $permissionLevels[$elevated]))
        return array('response' => false, 'error' => 'You do not have the right permissions');

    if (!isset($_POST['Identifier']) || $_POST['Identifier'] == '')
        return array('response' => false, 'error' => 'Identifier can not be empty');
    
    $Identifier = $_POST['Identifier'];

    require_once '../../sql_connection.php';
    $conn = GetDBConnection();
    
    $query = 'SELECT `ID` FROM `users` WHERE `users`.`Identifier` = ?'.(isset($_POST['Username']) ? ' OR `users`.`Username` = ?' : '').(isset($_POST['Email']) ? ' OR `users`.`Email` = ?' : '').';';
    $statement = $conn->prepare($query);
    if (!$statement)
    {
        $conn->close();
        return array('response' => false, 'error' => 'Could not prepare statement in '.__FILE__.':'.__FUNCTION__.':'.__LINE__);
    }

    if (isset($_POST['Email']))
    {
        if (isset($_POST['Username']))
            $statement->bind_param('sss', $Identifier, $_POST['Username'], $_POST['Email']);
        else
        $statement->bind_param('ss', $Identifier, $_POST['Email']);
    }
    else if (isset($_POST['Username']))
        $statement->bind_param('ss', $Identifier, $_POST['Username']);
    else
        $statement->bind_param('s', $Identifier);
    if (!$statement->execute())
    {
        $statement->close();
        $conn->close();
        return array('response' => false, 'error' => 'Could not execute statement in '.__FILE__.':'.__FUNCTION__.':'.__LINE__);
    }

    $result = $statement->get_result();
    $statement->close();
    $conn->close();
    return array('response' => $result->num_rows != 0);
}

function EditSelf($permissionLevels, $elevated)
{
    if (!array_key_exists($elevated, $permissionLevels) || !in_array(__FUNCTION__, $permissionLevels[$elevated]))
        return array('response' => false, 'error' => 'You do not have the right permissions');
    
    if (!isset($_SESSION['Identifier']))
        return array('response' => false, 'error' => 'No Identifier was stored on SESSION. Try logging out and back in');

    $Identifier = $_SESSION['Identifier'];
    $Name = (isset($_POST['Name'])) ? $_POST['Name'] : NULL;
    $Username = (isset($_POST['Username'])) ? $_POST['Username'] : NULL;
    $Email = (isset($_POST['Email'])) ? $_POST['Email'] : NULL;
    $Password = (isset($_POST['Password'])) ? password_hash($_POST['Password'],  PASSWORD_DEFAULT) : NULL;
    $Algo = (isset($_POST['Algo'])) ? $_POST['Algo'] : NULL;

    $user = GetSelf($permissionLevels, $elevated);
    if (isset($user['error']))
        return array('response' => false, 'error' => $user['error']);
    if ($user['response'] == false)
        return array('response' => false, 'error' => 'Unexpected error');
    
    $NeedLogOut = false;

    if ($Name == NULL)
        $Name = $user['data']['Name'];
    if ($Username == NULL)
        $Username = $user['data']['Username'];
    if ($Email == NULL)
        $Email = $user['data']['Email'];
    if ($Password != NULL && $Algo != NULL)
        $NeedLogOut = true;

    require_once '../../sql_connection.php';
    $conn = GetDBConnection();
    
    if ($Password != NULL && $Algo != NULL)
    {
        $query = 'UPDATE `users` SET `users`.`Name` = ?, `users`.`Username` = ?, `users`.`Email` = ?, `users`.`Password` = ?, `users`.`Algo` = ? WHERE `users`.`Identifier` = ?;';
        $statement = $conn->prepare($query);
        if (!$statement)
        {
            $conn->close();
            return array('response' => false, 'error' => 'Could not prepare statement in '.__FILE__.':'.__FUNCTION__.':'.__LINE__);
        }

        $statement->bind_param('ssssss', $Name, $Username, $Email, $Password, $Algo, $Identifier);
        if (!$statement->execute())
        {
            $statement->close();
            $conn->close();
            return array('response' => false, 'error' => 'Could not execute statement in '.__FILE__.':'.__FUNCTION__.':'.__LINE__);
        }
    }
    else
    {
        $query = 'UPDATE `users` SET `users`.`Name` = ?, `users`.`Username` = ?, `users`.`Email` = ? WHERE `users`.`Identifier` = ?;';
        $statement = $conn->prepare($query);
        if (!$statement)
        {
            $conn->close();
            return array('response' => false, 'error' => 'Could not prepare statement in '.__FILE__.':'.__FUNCTION__.':'.__LINE__);
        }

        $statement->bind_param('ssss', $Name, $Username, $Email, $Identifier);
        if (!$statement->execute())
        {
            $statement->close();
            $conn->close();
            return array('response' => false, 'error' => 'Could not execute statement in '.__FILE__.':'.__FUNCTION__.':'.__LINE__);
        }
    }

    $user = GetSelf($permissionLevels, $elevated);
    if (isset($user['error']))
        return array('response' => false, 'error' => $user['error']);
    if ($user['response'] == false)
        return array('response' => false, 'error' => 'Unexpected error');

    if ($user['data']['Name'] != $Name || $user['data']['Username'] != $Username || $user['data']['Email'] != $Email)
    {
        $statement->close();
        $conn->close();
        return array('response' => false, 'error' => 'Could not validate SelfEdit in users API V1');
    }

    $_SESSION['Name'] = $Name;
    $_SESSION['Username'] = $Username;
    $_SESSION['Email'] = $Email;

    $statement->close();
    $conn->close();

    if ($NeedLogOut)
        LogOut($permissionLevels, $elevated);
    return array('response' => true);
}

function EditUser($permissionLevels, $elevated)
{
    if (!array_key_exists($elevated, $permissionLevels) || !in_array(__FUNCTION__, $permissionLevels[$elevated]))
        return array('response' => false, 'error' => 'You do not have the right permissions');
    
    if (!isset($_POST['Identifier']))
        return array('response' => false, 'error' => 'No Identifier was stored on SESSION. Try logging out and back in');

    $Identifier = $_POST['Identifier'];
    $Name = (isset($_POST['Name'])) ? $_POST['Name'] : NULL;
    $Username = (isset($_POST['Username'])) ? $_POST['Username'] : NULL;
    $Email = (isset($_POST['Email'])) ? $_POST['Email'] : NULL;
    $Grade = (isset($_POST['Grade'])) ? $_POST['Grade'] : NULL;
    $Metadata = NULL;
    if (isset($_POST['Metadata']))
    {
        $Metadata = json_decode($_POST['Metadata']);
        if (json_last_error() != JSON_ERROR_NONE)
            return array('response' => false, 'error' => 'Metadata have to be in JSON formatting in User API V1');
    }
    $Metadata = (isset($_POST['Metadata']) ? $_POST['Metadata'] : NULL);

    $user = GetUser($permissionLevels, $elevated);
    if (isset($user['error']))
        return array('response' => false, 'error' => $user['error']);
    if ($user['response'] == false)
        return array('response' => false, 'error' => 'Unexpected error');
    
    if ($Name == NULL)
        $Name = $user['data']['Name'];
    if ($Username == NULL)
        $Username = $user['data']['Username'];
    if ($Email == NULL)
        $Email = $user['data']['Email'];
    if ($Grade == NULL)
        $Grade = $user['data']['Grade'];
    if ($Metadata == NULL)
        $Metadata = $user['data']['Email'];

    require_once '../../sql_connection.php';
    $conn = GetDBConnection();
    
    $query = 'UPDATE `users` SET `users`.`Name` = ?, `users`.`Username` = ?, `users`.`Email` = ?, `users`.`Grade` = ?, `users`.`Metadata` = ? WHERE `users`.`Identifier` = ?;';
    $statement = $conn->prepare($query);
    if (!$statement)
    {
        $conn->close();
        return array('response' => false, 'error' => 'Could not prepare statement in '.__FILE__.':'.__FUNCTION__.':'.__LINE__);
    }

    $statement->bind_param('ssssss', $Name, $Username, $Email, $Grade, $Metadata, $Identifier);
    if (!$statement->execute())
    {
        $statement->close();
        $conn->close();
        return array('response' => false, 'error' => 'Could not execute statement in '.__FILE__.':'.__FUNCTION__.':'.__LINE__);
    }

    $user = GetUser($permissionLevels, $elevated);
    if (isset($user['error']))
        return array('response' => false, 'error' => $user['error']);
    if ($user['response'] == false)
        return array('response' => false, 'error' => 'Unexpected error');

    if ($user['data']['Name'] != $Name || $user['data']['Username'] != $Username || $user['data']['Email'] != $Email || $user['data']['Grade'] != $Grade)
    {
        $statement->close();
        $conn->close();
        return array('response' => false, 'error' => 'Could not validate SelfEdit in users API V1');
    }

    $statement->close();
    $conn->close();
    return array('response' => true);
}

function GetAlgo($permissionLevels, $elevated)
{
    if (!array_key_exists($elevated, $permissionLevels) || !in_array(__FUNCTION__, $permissionLevels[$elevated]))
        return array('response' => false, 'error' => 'You do not have the right permissions');
    
    if ((!isset($_POST['Username']) || $_POST['Username'] == '') && (!isset($_POST['Email']) || $_POST['Email'] == ''))
        return array('response' => false, 'error' => 'You need to specify a username or an email');

    $LogInID = isset($_POST['Username']) ? $_POST['Username'] : $_POST['Email'];

    require_once '../../sql_connection.php';
    $conn = GetDBConnection();

    $query = 'SELECT `Algo` FROM `users` WHERE `'.(isset($_POST['Email']) ? 'Email' : 'Username').'` = ?;';
    $statement = $conn->prepare($query);
    if (!$statement)
    {
        $conn->close();
        return array('response' => false, 'error' => 'Could not prepare statement in '.__FILE__.':'.__FUNCTION__.':'.__LINE__);
    }

    $statement->bind_param('s', $LogInID);
    if (!$statement->execute())
    {
        $statement->close();
        $conn->close();
        return array('response' => false, 'error' => 'Could not execute statement in '.__FILE__.':'.__FUNCTION__.':'.__LINE__);
    }

    $result = $statement->get_result();
    $row = $result->fetch_assoc();
    
    return array('response' => true, 'data' => $row['Algo']);
}

function GetSelf($permissionLevels, $elevated)
{
    if (!array_key_exists($elevated, $permissionLevels) || !in_array(__FUNCTION__, $permissionLevels[$elevated]))
        return array('response' => false, 'error' => 'You do not have the right permissions');
    
    if (!isset($_SESSION['Identifier']))
        return array('response' => false, 'error' => 'No Identifier was stored on SESSION. Try logging out and back in');

    $Identifier = $_SESSION['Identifier'];
    
    require_once '../../sql_connection.php';
    $conn = GetDBConnection();
    
    $query = 'SELECT `Name`, `Username`, `Email`, `Level`, `Metadata`, `Grade`  FROM `users` WHERE `users`.`Identifier` = ?;';
    $statement = $conn->prepare($query);
    if (!$statement)
    {
        $conn->close();
        return array('response' => false, 'error' => 'Could not prepare statement in '.__FILE__.':'.__FUNCTION__.':'.__LINE__);
    }

    $statement->bind_param('s', $Identifier);
    if (!$statement->execute())
    {
        $statement->close();
        $conn->close();
        return array('response' => false, 'error' => 'Could not execute statement in '.__FILE__.':'.__FUNCTION__.':'.__LINE__);
    }

    $result = $statement->get_result();
    if ($result->num_rows == 0)
        return array('response' => false);
    $row = $result->fetch_assoc();
    $statement->close();
    $conn->close();
    return array('response' => true, 'data' => $row);
}

function GetUser($permissionLevels, $elevated)
{
    if (!array_key_exists($elevated, $permissionLevels) || !in_array(__FUNCTION__, $permissionLevels[$elevated]))
        return array('response' => false, 'error' => 'You do not have the right permissions');
    
    if (!isset($_POST['Identifier']))
        return array('response' => false, 'error' => 'No Identifier was passed in user API V1');

    $Identifier = $_POST['Identifier'];
    
    require_once '../../sql_connection.php';
    $conn = GetDBConnection();
    
    $query = 'SELECT `Identifier`, `Name`, `Username`, `Email`, `Level`, `Metadata`, `Grade`  FROM `users` WHERE `users`.`Identifier` = ?;';
    $statement = $conn->prepare($query);
    if (!$statement)
    {
        $conn->close();
        return array('response' => false, 'error' => 'Could not prepare statement in '.__FILE__.':'.__FUNCTION__.':'.__LINE__);
    }

    $statement->bind_param('s', $Identifier);
    if (!$statement->execute())
    {
        $statement->close();
        $conn->close();
        return array('response' => false, 'error' => 'Could not execute statement in '.__FILE__.':'.__FUNCTION__.':'.__LINE__);
    }

    $result = $statement->get_result();
    if ($result->num_rows == 0)
        return array('response' => false);
    $row = $result->fetch_assoc();
    $statement->close();
    $conn->close();
    return array('response' => true, 'data' => $row);
}

function LogIn($permissionLevels, $elevated)
{
    if (!array_key_exists($elevated, $permissionLevels) || !in_array(__FUNCTION__, $permissionLevels[$elevated]))
        return array('response' => false, 'error' => 'You do not have the right permissions');
    
    if ((!isset($_POST['Username']) || $_POST['Username'] == '') && (!isset($_POST['Email']) || $_POST['Email'] == ''))
        return array('response' => false, 'error' => 'You need to specify a username or an email');
    if (!isset($_POST['Password']) || $_POST['Password'] == '')
        return array('response' => false, 'error' => 'You need to specify a password');

    $LogInID = isset($_POST['Username']) ? $_POST['Username'] : $_POST['Email'];
    $Password = $_POST['Password'];

    require_once '../../sql_connection.php';
    $conn = GetDBConnection();

    $query = 'SELECT `Identifier`, `Name`, `Username`, `Email`, `Password`, `Level`, `Grade` FROM `users` WHERE `'.(isset($_POST['Email']) ? 'Email' : 'Username').'` = ?;';
    $statement = $conn->prepare($query);
    if (!$statement)
    {
        $conn->close();
        return array('response' => false, 'error' => 'Could not prepare statement in '.__FILE__.':'.__FUNCTION__.':'.__LINE__);
    }

    $statement->bind_param('s', $LogInID);
    if (!$statement->execute())
    {
        $statement->close();
        $conn->close();
        return array('response' => false, 'error' => 'Could not execute statement in '.__FILE__.':'.__FUNCTION__.':'.__LINE__);
    }

    $result = $statement->get_result();
    $numOfRows = $result->num_rows;
    if ($numOfRows == 0)
	    return array('response' => false, 'error' => 'No users found');
    for ($i = 0; $i < $numOfRows; $i++)
    {
        $row = $result->fetch_assoc();
        if (password_verify($Password, $row['Password']))
        {
            $_SESSION['Logged in'] = true;
            $_SESSION['Name'] = $row['Name'];
            $_SESSION['Username'] = $row['Username'];
            $_SESSION['Email'] = $row['Email'];
            $_SESSION['Identifier'] = $row['Identifier'];
            $_SESSION['Level'] = $row['Level'];
            $_SESSION['Grade'] = $row['Grade'];
            return array('response' => true);
        }
    }
    
    return array('response' => false);
}

function LogOut($permissionLevels, $elevated)
{
    $_SESSION['Logged in'] = false;
    $_SESSION['Level'] = 3;
    $_SESSION['Name'] = $_SESSION['Username'] = $_SESSION['Email'] = $_SESSION['Grade'] = '';

    return array('response' => true);
}

function PasswordReset($permissionLevels, $elevated)
{
    if (!array_key_exists($elevated, $permissionLevels) || !in_array(__FUNCTION__, $permissionLevels[$elevated]))
        return array('response' => false, 'error' => 'You do not have the right permissions');
    
    if (!isset($_POST['Username']))
        return array('response' => false, 'error' => 'No Username was passed in user API V1');
    
    require_once '../../sql_connection.php';
    $conn = GetDBConnection();

    $Username = $_POST['Username'];


    $query = 'SELECT `Email`, `Metadata` FROM `users` WHERE `users`.`Username` = ?;';
    $statement = $conn->prepare($query);
    if (!$statement)
    {
        $conn->close();
        return array('response' => false, 'error' => 'Could not prepare statement in '.__FILE__.':'.__FUNCTION__.':'.__LINE__);
    }

    $statement->bind_param('s', $Username);
    if (!$statement->execute())
    {
        $statement->close();
        $conn->close();
        return array('response' => false, 'error' => 'Could not execute statement in '.__FILE__.':'.__FUNCTION__.':'.__LINE__);
    }

    $result = $statement->get_result();
    if ($result->num_rows == 0)
        return array('response' => false, 'error' => 'No users were found with this Username');
    $row = $result->fetch_assoc();

    if ($row['Email'] == '')
        return array('response' => false, 'error' => 'This username is not connected to an email');

    $Metadata = json_decode($row['Metadata'], true);
    if (json_last_error() != JSON_ERROR_NONE)
        return array('response' => false, 'error' => 'Corrupted user data. Please ask a Administator to remove user metadata');


    if (isset($_POST['Code']) && isset($_POST['Password']) && isset($_POST['Algo']))// Reset Password
    {   
        if (isset($Metadata['PasswordReset']) && isset($Metadata['PasswordReset']['Code']) && password_verify($_POST['Code'], $Metadata['PasswordReset']['Code']))
        {
            unset($Metadata['PasswordReset']);
            $Metadata = json_encode($Metadata);
            $query = 'UPDATE `users` SET `users`.`Password` = ?, `users`.`Algo` = ?, `users`.`Metadata` = ? WHERE `users`.`Username` = ?;';
            $statement = $conn->prepare($query);
            if (!$statement)
            {
                $conn->close();
                return array('response' => false, 'error' => 'Could not prepare statement in '.__FILE__.':'.__FUNCTION__.':'.__LINE__);
            }

            $Password = password_hash($_POST['Password'], PASSWORD_DEFAULT);
            $statement->bind_param('ssss', $Password, $_POST['Algo'], $Metadata, $Username);
            if (!$statement->execute())
            {
                $statement->close();
                $conn->close();
                return array('response' => false, 'error' => 'Could not execute statement in '.__FILE__.':'.__FUNCTION__.':'.__LINE__);
            }
            return array('response' => true);
        }
        else
            return array('response' => false, 'error' => 'Unexpected error');
    }
    else                                                                            // Send Password Reset Code
    {
        $Code = 'Test';
        $Code = password_hash($Code, PASSWORD_DEFAULT);
        
        $Metadata['PasswordReset'] = array('Code' => $Code);
        
        require 'phpmailer/class.smtp.php';
        require 'phpmailer/class.phpmailer.php';
        $mail = new PHPMailer();
        $mail->IsSMTP();
        $mail->SMTPAuth = true;
        $mail->Host = 'websitemail.sch.gr';
        $mail->Username = 'lykevsch';
        $mail->Password = '5ek4re13';
        $mail->From = 'mail@lyk-evsch-n-smyrn.att.sch.gr';
        $mail->FromName = 'Open School Library Lite';
        $mail->AddAddress($row['Email']);
        $mail->IsHTML(true);
        $mail->CharSet = 'UTF-8';
        $mail->Subject = 'Password Reset';
        $mail->Body = 'This is an automated email response from the Open School Library Lite.<br />You have recently requested a password reset code. Your password reset code is "'.$Code.'"<br /><br />Sent by Open School Library Lite. <3 from the FLOSS Community.';
        $mail->Send();

        $Metadata = json_encode($Metadata);
        $query = 'UPDATE `users` SET `users`.`Metadata` = ? WHERE `users`.`Username` = ?;';
        $statement = $conn->prepare($query);
        if (!$statement)
        {
            $conn->close();
            return array('response' => false, 'error' => 'Could not prepare statement in '.__FILE__.':'.__FUNCTION__.':'.__LINE__);
        }

        $statement->bind_param('ss', $Metadata, $Username);
        if (!$statement->execute())
        {
            $statement->close();
            $conn->close();
            return array('response' => false, 'error' => 'Could not execute statement in '.__FILE__.':'.__FUNCTION__.':'.__LINE__);
        }
        return array('response' => true);
    }
}

function RemoveUser($permissionLevels, $elevated)
{
    if (!array_key_exists($elevated, $permissionLevels) || !in_array(__FUNCTION__, $permissionLevels[$elevated]))
        return array('response' => false, 'error' => 'You do not have the right permissions');
    
    if (!isset($_POST['Identifier']))
        return array('response' => false, 'error' => 'No Identifier was passed in user API V1');

    $user = GetUser($permissionLevels, $elevated);
    if (isset($user['error']))
        return array('response' => false, 'error' => $user['error']);
    if ($user['response'] == false)
        return array('response' => false, 'error' => 'A user with that Identifier does not exist');

    $Identifier = $_POST['Identifier'];
    
    require_once '../../sql_connection.php';
    $conn = GetDBConnection();

    $query = 'DELETE FROM `users` WHERE `Identifier` = ?;';
    $statement = $conn->prepare($query);
    if (!$statement)
    {
        $conn->close();
        return array('response' => false, 'error' => 'Could not prepare statement in '.__FILE__.':'.__FUNCTION__.':'.__LINE__);
    }

    $statement->bind_param('s', $Identifier);
    if (!$statement->execute())
    {
        $statement->close();
        $conn->close();
        return array('response' => false, 'error' => 'Could not execute statement in '.__FILE__.':'.__FUNCTION__.':'.__LINE__);
    }

    $user = GetUser($permissionLevels, $elevated);
    if (isset($user['error']))
        return array('response' => false, 'error' => $user['error']);
    if ($user['response'] == true)
        return array('response' => false, 'error' => 'Could not remove User');

    $statement->close();
    $conn->close();
    return array('response' => true);
}

function SearchUsers($permissionLevels, $elevated)
{
    if (!array_key_exists($elevated, $permissionLevels) || !in_array(__FUNCTION__, $permissionLevels[$elevated]))
        return array('response' => false, 'error' => 'You do not have the right permissions');
    
    if (!isset($_POST['SearchTag']) || strlen($_POST['SearchTag']) < 2)
        return array('response' => false, 'error' => 'No SearchTag was passed in user API V1');

    $SearchTag = '%'.$_POST['SearchTag'].'%';
    
    require_once '../../sql_connection.php';
    $conn = GetDBConnection();
    
    $query = 'SELECT `Identifier`, `Name`, `Grade`  FROM `users` WHERE ( UPPER(`users`.`Name`) LIKE UPPER(?) OR UPPER(`users`.`Username`) LIKE UPPER(?) OR UPPER(`users`.`Email`) LIKE UPPER(?) OR UPPER(`users`.`Identifier`) LIKE UPPER(?) ) LIMIT 10;';
    $statement = $conn->prepare($query);
    if (!$statement)
    {
        $conn->close();
        return array('response' => false, 'error' => 'Could not prepare statement in '.__FILE__.':'.__FUNCTION__.':'.__LINE__);
    }

    $statement->bind_param('ssss', $SearchTag, $SearchTag, $SearchTag, $SearchTag);
    if (!$statement->execute())
    {
        $statement->close();
        $conn->close();
        return array('response' => false, 'error' => 'Could not execute statement in '.__FILE__.':'.__FUNCTION__.':'.__LINE__);
    }

    $result = $statement->get_result();
    $results = array('response' => true, 'data' => array());

    if ($result->num_rows != 0)
        while ($row = $result->fetch_assoc())
            $results['data'][] = $row;

    return $results;
}
*/

header('Content-Type: application/json');
echo '{"error":"Book API V1 has been deprecated"}';

/*
if (!isset($_POST['type']))
    die('{ "response" : false, "error" : "User API `type` not provided" }');

if (!function_exists($_POST['type']))
    die('{ "response" : false, "error" : "User API function `'.$_POST['type'].'` does not exist" }');

echo json_encode(($_POST['type']($permissionLevels, $elevated)), JSON_UNESCAPED_UNICODE);
*/

?>