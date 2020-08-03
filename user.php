<?php

header('Content-Type: application/json');
if (!isset($_SESSION))
    session_start();
$elevated = (isset($_SESSION['Logged in']));

require 'userFuncs.php';


if (!isset($_POST['type']))
{
    echo '{}';
    exit();
}
$type = $_POST['type'];

switch($type)
{
    case 'addUser':
        if (!isset($_GET['userID']) || !isset($_GET['name']))
        {
            echo '{ "response" : false }';
            exit();
        }

        $userID = $_GET['userID'];
        $name = $_GET['name'];

        addUser($userID, $name);

        break;
    
    case 'checkIfExists':
        if (!isset($_GET['userID']))
        {
            echo '{ "response" : false }';
            exit();
        }

        $userID = $_GET['userID'];
        if ($userID == '')
        {
            echo '{ "error" : true }';
            exit();
        }

        $result = checkIfExists($userID);
        if ($result === -1)
            echo '{ "error " : true }';
        else
            echo '{ "response" : '.$result.' }';

        break;

    case 'getName':
        if (!isset($_GET['userID']))
        {
            echo '{}';
            exit();
        }
        $userID = $_GET['userID'];
        if ($userID == '')
        {
            echo '{ "error" : true }';
            exit();
        }
        $result = getName($userID);
        if ($result === -1 || $result === 0)
            echo '{ "error" : true }';
        else
            echo '{ "response" : "'.$result.'" }';
        break;

    case 'getUser':
        if (!isset($_POST['format']) || !isset($_POST['userID']))
            echo '';
        else if ($_POST['format'] == 'json')
            echo User::GetUserJSON(md5($_POST['userID']), $elevated);
        //else if ($_GET["format"] == "xml");
        //    echo User::GetUserXML(md5($_GET["id"]), $elevated);
        break;

    case 'getNameHashed':
            if (!isset($_GET['userID']))
            {
                echo '{}';
                exit();
            }
            $userID = $_GET['userID'];
            if ($userID == '')
            {
                echo '{ "error" : true }';
                exit();
            }
            $result = getNameHashed($userID);
            if ($result === -1 || $result === 0)
                echo '{ "error" : true }';
            else
                echo '{ "response" : "'.$result.'" }';
            break;

    default:
        echo '{ "error" : true }';
        exit();
}

?>