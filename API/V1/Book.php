<?php

function AddBook()
{
    require '../../sql_connection.php';

    $Identifier = '';
    $Title = '';
    $Author = '';
    $Dewey = '';
    $ISBN = '';
    $Metadata = '{}';

    if (isset($_POST['Identifier']))
        $Identifier = $_POST['Identifier'];
    if (isset($_POST['Title']))
        $Title = $_POST['Title'];
    if (isset($_POST['Author']))
        $Author = $_POST['Author'];
    if (isset($_POST['Dewey']))
        $Dewey = $_POST['Dewey'];
    if (isset($_POST['ISBN']))
        $ISBN = $_POST['ISBN'];

    $query = 'INSERT INTO `books` (`ID`, `Identifier`, `Title`, `Author`, `Dewey`, `ISBN`, `Availability`, `Found`, `Metadata`) VALUES (NULL, ?, ?, ?, ?, ?, 1, 1, ?);';
    $statement = $conn->prepare($query);
    if (!$statement)
    {
        $statement->close();
        $conn->close();
        return '{ "response" : false, "error" : "Could not prepare statement in Book API V1" }';
    }
    $statement->bind_param('ssssss', $Identifier, $Title, $Author, $Dewey, $ISBN, $Metadata);
    if (!$statement->execute())
    {
        $statement->close();
        $conn->close();
        return '{ "response" : false, "error" : "Could not execute statement in Book API V1" }';
    }

    $query = 'SELECT `Title`, `Author`, `Dewey`, `ISBN`FROM `books` WHERE `Identifier` = ? LIMIT 1;';
    $statement = $conn->prepare($query);
    if (!$statement)
        return '{ "response" : false, "error" : "Could not prepare statement in Book API V1" }';
    $statement->bind_param('s', $Identifier);
    if (!$statement->execute())
    {
        $statement->close();
        $conn->close();
        return '{ "response" : false, "error" : "Could not execute statement in Book API V1" }';
    }
    $result = $statement->get_result();
    if ($result->num_rows == 0)
    {
        $statement->close();
        $conn->close();
        return '{ "response" : false, "error" : "Could not validate Insertion in Book API V1" }';
    }
    $row = $result->fetch_assoc();
    if ($row['Title'] != $Title || $row['Author'] != $Author || $row['Dewey'] != $Dewey || $row['ISBN'] != $ISBN)
    {
        $statement->close();
        $conn->close();
        return '{ "response" : false, "error" : "Could not validate Insertion in Book API V1" }';
    }
    
    $statement->close();
    $conn->close();
    return '{ "response" : true }';
}

header('Content-Type: application/json');

if (!isset($_POST['type']))
    die('{ "response" : false, "error" : "Book API `type` not provided" }');

if (!function_exists($_POST['type']))
    die('{ "response" : false, "error" : "Book API function `'.$_POST['type'].'` does not exist" }');

echo ($_POST['type']());
?>