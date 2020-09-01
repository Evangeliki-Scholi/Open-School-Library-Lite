<?php
$serverUrl = "localhost";
$username = "root";
$password = "";
$database = "Open School Library Lite";

$conn = new mysqli($serverUrl, $username, $password, $database);

if ($conn->connect_error)
{
    die("Connection to server failed: ".$conn->connect_error);
}

$conn->set_charset("utf8");

function GetDBConnection()
{
    $serverUrl = "localhost";
    $username = "root";
    $password = "";
    $database = "Open School Library Lite";

    $conn = new mysqli($serverUrl, $username, $password, $database);

    if ($conn->connect_error)
    {
        die("Connection to server failed: ".$conn->connect_error);
    }

    $conn->set_charset("utf8");
    return $conn;
}