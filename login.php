<?php
if (!isset($_SESSION))
    session_start();

$elevated = (isset($_SESSION['Logged in'])) ? $_SESSION['Logged in'] === true : false;
if ($elevated)
{
    echo '';
    die();
}

function GetAlgo($lusername)
{
    try
    {
        require 'sql_connection.php';
        $sql = $conn->prepare('SELECT `Algo` FROM `admins` WHERE `Username` = ?;');
        $sql->bind_param('s', $lusername);
        if (!$sql->execute()) return '{}';
        $result = $sql->get_result();
        if ($result->num_rows != 1) return '{}';
        $row = $result->fetch_assoc();
        return '{ "response" : "'.$row['Algo'].'" }';
    }
    catch (Exception $e)
    {
        return '{}';
    }
}

function LogIn($lusername, $lpassword)
{
    try
    {
        require_once 'sql_connection.php';
        $sql = $conn->prepare('SELECT `ID`, `Name`, `Password` FROM `admins` WHERE `Username` = ?');
        $sql->bind_param('s', $lusername);
        if (!$sql->execute()) return '{}';
        $result = $sql->get_result();
        if ($result->num_rows === 0) return '{}';
        $row = $result->fetch_assoc();
        if (password_verify($lpassword, $row['Password']))
        {
            $_SESSION['Logged in'] = true;
            $_SESSION['Name'] = $row['Name'];
            $_SESSION['Admin ID'] = $row['ID'];
            $_SESSION['Username'] = $lusername;
            return '{ "response": true }';
        }
        else
            return '{ "response": false }';
    }
    catch (Exception $e)
    {
        return '{ "response": false }';
    }
}

if (isset($_POST['type']))
{
    switch ($_POST['type'])
    {
        case 'GetAlgo':
            echo (isset($_POST['username'])) ? GetAlgo($_POST['username']) : '{}';
            break;

        case 'LogIn':
            echo (isset($_POST['username']) && isset($_POST['password'])) ? LogIn($_POST['username'], $_POST['password']) : '{}';
            break;

        default:
            return '{}';
            break;
    }

    exit();
}
/*if (isset($_POST['username']) && isset($_POST['password']))
{
    require_once 'sql_connection.php';

    $sql = $conn->prepare('SELECT `id`, `password`, `Name` FROM `admins` WHERE `username`=?');
    $sql->bind_param('s', $_POST['username']);
    if ($sql->execute())
    {
        $result = $sql->get_result();
        $row = ($result->fetch_assoc());
        if (!isset($_POST['hashed']) && password_verify($_POST['password'], $row['password']))
        {
            echo "Needs update";
            $newPassword = password_hash(md5($_POST['password']), PASSWORD_BCRYPT);
            $sql = $conn->prepare('UPDATE `admins` SET `password` = ? WHERE `admins`.`id` = ?');
            $sql->bind_param('sd', $newPassword, $row['id']);
            if ($sql->execute())
            {
                $_SESSION['Logged in'] = true;
                $_SESSION['Name'] = $row['Name'];
                $_SESSION['Admin ID'] = $row['id'];
                $_SESSION['Username'] = $_POST['username'];
            }
        }
        else if (password_verify($_POST['password'], $row['password']) || password_verify(hash('sha256', $_POST['password']), $row['password']))
        {
            $_SESSION['Logged in'] = true;
            $_SESSION['Name'] = $row['Name'];
            $_SESSION['Admin ID'] = $row['id'];
            $_SESSION['Username'] = $_POST['username'];
        }
    }
}*/
?>