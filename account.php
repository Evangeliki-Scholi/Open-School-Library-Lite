<?php
session_start();
$elevated = (isset($_SESSION['Logged in'])) ? $_SESSION['Logged in'] === true : false;

if(isset($_POST['Name']) && isset($_POST['Username']) && isset($_POST['New_Password']) && isset($_POST['Algo']) && $elevated)
{
    require_once 'sql_connection.php';
    $id = $_SESSION['Admin ID'];
    $sql;
    if ($_POST['New_Password'] != '')
    {
        $lpassword = password_hash($_POST['New_Password'], PASSWORD_BCRYPT);
        $sql = $conn->prepare('UPDATE `admins` SET `Name` = ?, `Username` = ?, `Password` = ?, `Algo` = ? WHERE `admins`.`ID` = ?');
        $sql->bind_param('ssssd', $_POST['Name'], $_POST['Username'], $lpassword, $_POST['Algo'], $id);
    }
    else
    {
        $sql = $conn->prepare('UPDATE `admins` SET `Name` = ?, `Username` = ? WHERE `admins`.`ID` = ?');
        $sql->bind_param('ssd', $_POST['Name'], $_POST['Username'], $id);
    }
    $executed = $sql->execute();
    if ($executed)
    {
        $_SESSION['Name'] = $_POST['Name'];
        $_SESSION['Username'] = $_POST['Username'];
    }
    echo ('{"response":'.(($executed == true) ? 'true' : 'false').'}');
    die();
}
?>