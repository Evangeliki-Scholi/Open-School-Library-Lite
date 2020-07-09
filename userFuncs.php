<?php

class User
{
    private $_ID = null;
    private $_name = null;
    private $_identifier = null;

    public static function GetUserJSON($identifier, $elevated)
    {
        if (!$elevated) return "{}";
        require_once "sql_connection.php";

        $sql = "SELECT `ID`, `Identifier`, `Name` FROM `users` WHERE `Identifier` = ? LIMIT 1;";
        $sql = $conn->prepare($sql);
        $sql->bind_param("s", $identifier);
        
        if (!$sql->execute()) return "{}";
        $result = $sql->get_result();
        if (!isset($result->num_rows) || $result->num_rows == 0) return "{}";
        $result = $result->fetch_assoc();

        return json_encode($result, JSON_UNESCAPED_UNICODE);
    }
}

function addUser($userID, $name)
{
    require_once "sql_connection.php";
    $userID = md5($userID);
    $sql = $conn->prepare("INSERT INTO `users` (`ID`, `Name`, `Identifier`) VALUES (NULL, ?, ?)");
    $sql->bind_param("ss", $name, $userID);
    echo (($sql->execute()) ? "{ \"response\": true }" : "{ \"response\": false }");
}

function checkIfExists($userID)
{
    require_once "sql_connection.php";
    $userID = md5($userID);
    $sql = $conn->prepare("SELECT `ID` FROM `users` WHERE `Identifier` = ?");
    $sql->bind_param("s", $userID);
    if (!$sql->execute()) return -1;
    $result = $sql->get_result();
    if (!isset($result->num_rows) || $result->num_rows == 0) return 0;
    return 1;
}

function getName($userID)
{
    require "sql_connection.php";
    return getNameHashed(md5($userID));
}

function getNameHashed($userID)
{
    require "sql_connection.php";
    $sql = $conn->prepare("SELECT `Name` FROM `users` WHERE `UserIdentification` = ?;");
    $sql->bind_param("s", $userID);
    if (!$sql->execute()) return -1;
    $result = $sql->get_result();
    if (!isset($result->num_rows) || $result->num_rows == 0) return 0;
    $row = $result->fetch_assoc();
    return $row["Name"];
}

?>