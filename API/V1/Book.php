<?php

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

$elevated = (isset($_SESSION['Logged in'])) ? $_SESSION['Logged in'] === true : false;

function AddBook()
{
    require_once '../../sql_connection.php';

    $Identifier = '';
    $Title = '';
    $Author = '';
    $Dewey = '';
    $ISBN = '';
    $Metadata = '{}';

    if (!isset($_POST['Identifier']) || $_POST['Identifier'] == '')
        return array('response' => false, 'error' => 'Identifier not provided');
    
    $exists = CheckBookExists();
    if ($exists['response'])
        return array('response' => false, 'error' => 'A book with that Identifier already exists');
    if (isset($exists['error']))
        return array('response' => false, 'error' => $exists['error']);

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
        return array('response' => false, 'error' => 'Could not prepare statement in Book API V1');
    }
    $statement->bind_param('ssssss', $Identifier, $Title, $Author, $Dewey, $ISBN, $Metadata);
    if (!$statement->execute())
    {
        $statement->close();
        $conn->close();
        return array('response' => false, 'error' => 'Could not execute statement in Book API V1');
    }

    $query = 'SELECT `Title`, `Author`, `Dewey`, `ISBN`FROM `books` WHERE `Identifier` = ? LIMIT 1;';
    $statement = $conn->prepare($query);
    if (!$statement)
        return array('response' => false, 'error' => 'Could not prepare statement in Book API V1');
    $statement->bind_param('s', $Identifier);
    if (!$statement->execute())
    {
        $statement->close();
        $conn->close();
        return array('response' => false, 'error' => 'Could not execute statement in Book API V1');
    }
    $result = $statement->get_result();
    if ($result->num_rows == 0)
    {
        $statement->close();
        $conn->close();
        return array('response' => false, 'error' => 'Could not validate Insertion in Book API V1');
    }
    $row = $result->fetch_assoc();
    if ($row['Title'] != $Title || $row['Author'] != $Author || $row['Dewey'] != $Dewey || $row['ISBN'] != $ISBN)
    {
        $statement->close();
        $conn->close();
        return array('response' => false, 'error' => 'Could not validate Insertion in Book API V1');
    }
    
    $statement->close();
    $conn->close();
    return array('response' => true);
}

function CheckBookExists()
{
    require_once '../../sql_connection.php';

    $conn = GetDBConnection();
    if (!isset($_POST['Identifier']) || $_POST['Identifier'] == '')
        return array('response' => false, 'error' => 'Identifier not provided');
    
    $Identifier = $_POST['Identifier'];

    $query = 'SELECT `ID` FROM `books` WHERE `Identifier` = ?;';
    $statement = $conn->prepare($query);
    if (!$statement)
    {
        $statement->close();
        $conn->close();
        return array('response' => false, 'error' => 'Could not prepare statement in Book API V1');
    }

    $statement->bind_param('s', $Identifier);
    if (!$statement->execute())
    {
        $statement->close();
        $conn->close();
        return array('response' => false, 'error' => 'Could not execute statement in Book API V1');
    }

    $result = $statement->get_result();
    $statement->close();
    $conn->close();
    return array('response' => $result->num_rows != 0);
}

function EditBook()
{
    require_once '../../sql_connection.php';

    $Identifier = '';
    $Title = NULL;
    $Author = NULL;
    $Dewey = NULL;
    $ISBN = NULL;
    $Metadata = NULL;

    if (!isset($_POST['Identifier']) || $_POST['Identifier'] == '')
        return array('response' => false, 'error' => 'Identifier not provided');
    $Identifier = $_POST['Identifier'];

    $exists = CheckBookExists();
    if (!$exists['response'])
        return array('response' => false, 'error' => 'A book with that Identifier does not exist');
    if (isset($exists['error']))
        return array('response' => false, 'error' => $exists['error']);

    if (isset($_POST['Title']))
        $Title = $_POST['Title'];
    if (isset($_POST['Author']))
        $Author = $_POST['Author'];
    if (isset($_POST['Dewey']))
        $Dewey = $_POST['Dewey'];
    if (isset($_POST['ISBN']))
        $ISBN = $_POST['ISBN'];

    $query = 'SELECT `Title`, `Author`, `Dewey`, `ISBN`, `Metadata` FROM `books` WHERE `Identifier` = ?;';
    $statement = $conn->prepare($query);
    if (!$statement)
    {
        $statement->close();
        $conn->close();
        return array('response' => false, 'error' => 'Could not prepare statement in Book API V1');
    }
    $statement->bind_param('s', $Identifier);
    if (!$statement->execute())
    {
        $statement->close();
        $conn->close();
        return array('response' => false, 'error' => 'Could not execute statement in Book API V1');
    }

    $result = $statement->get_result();
    if ($result->num_rows == 0)
    {
        $statement->close();
        $conn->close();
        return array('response' => false, 'error' => 'Could not retrive data book to change in Book API V1');
    }
    $row = $result->fetch_assoc();
    if ($Title == NULL)
        $Title = $row['Title'];
    if ($Author == NULL)
        $Author = $row['Author'];
    if ($Dewey == NULL)
        $Dewey = $row['Dewey'];
    if ($ISBN == NULL)
        $ISBN = $row['ISBN'];
    if ($Metadata == NULL)
        $Metadata = $row['Metadata'];

    $query = 'UPDATE `books` SET `Title` = ?, `Author` = ?, `Dewey` = ?, `ISBN` = ?, `Metadata` = ? WHERE `Identifier` = ?;';
    $statement = $conn->prepare($query);
    if (!$statement)
    {
        $statement->close();
        $conn->close();
        return array('response' => false, 'error' => 'Could not prepare statement in Book API V1');
    }
    $statement->bind_param('ssssss', $Title, $Author, $Dewey, $ISBN, $Metadata, $Identifier);
    if (!$statement->execute())
    {
        $statement->close();
        $conn->close();
        return array('response' => false, 'error' => 'Could not execute statement in Book API V1');
    }

    $query = 'SELECT `Title`, `Author`, `Dewey`, `ISBN`, `Metadata` FROM `books` WHERE `Identifier` = ?;';
    $statement = $conn->prepare($query);
    if (!$statement)
    {
        $statement->close();
        $conn->close();
        return array('response' => false, 'error' => 'Could not prepare statement in Book API V1');
    }
    $statement->bind_param('s', $Identifier);
    if (!$statement->execute())
    {
        $statement->close();
        $conn->close();
        return array('response' => false, 'error' => 'Could not execute statement in Book API V1');
    }

    $result = $statement->get_result();
    if ($result->num_rows == 0)
    {
        $statement->close();
        $conn->close();
        return array('response' => false, 'error' => 'Could not retrive data book to change in Book API V1');
    }
    $row = $result->fetch_assoc();
    if ($Title != $row['Title'] || $Author != $row['Author'] || $Dewey != $row['Dewey'] || $ISBN != $row['ISBN'] || $Metadata != $row['Metadata'])
    {
        $statement->close();
        $conn->close();
        return array('response' => false, 'error' => 'Could not validate Edit in Book API V1');
    }
    
    $statement->close();
    $conn->close();
    return array('response' => true);
}

function GetBook()
{
    require_once '../../sql_connection.php';

    if (!isset($_POST['Identifier']) || $_POST['Identifier'] == '')
        return array('response' => false, 'error' => 'Identifier not provided');
    $Identifier = $_POST['Identifier'];

    $exists = CheckBookExists();
    if (!$exists['response'])
        return array('response' => false, 'error' => 'A book with that Identifier does not exist');
    if (isset($exists['error']))
        return array('response' => false, 'error' => $exists['error']);

    $query = 'SELECT `Identifier`, `Title`, `Author`, `Dewey`, `ISBN`, `Availability`, `BorrowedUntil` FROM `books` WHERE `Identifier` = ?;';
    $statement = $conn->prepare($query);
    if (!$statement)
    {
        $statement->close();
        $conn->close();
        return array('response' => false, 'error' => 'Could not prepare statement in Book API V1');
    }

    $statement->bind_param('s', $Identifier);
    if (!$statement->execute())
    {
        $statement->close();
        $conn->close();
        return array('response' => false, 'error' => 'Could not execute statement in Book API V1');
    }

    $result = $statement->get_result();
    if ($result->num_rows == 0)
    {
        $statement->close();
        $conn->close();
        return array('response' => false, 'error' => 'Could not retrive data book in Book API V1');
    }
    $row = $result->fetch_assoc();
    $row['response'] = true;

    $result = $statement->get_result();
    $statement->close();
    $conn->close();
    return json_encode($row);
}

function RemoveBook()
{
    require_once '../../sql_connection.php';

    if (!isset($_POST['Identifier']) || $_POST['Identifier'] == '')
        return array('response' => false, 'error' => 'Identifier not provided');
    $Identifier = $_POST['Identifier'];

    $exists = CheckBookExists();
    if (!$exists['response'])
        return array('response' => false, 'error' => 'A book with that Identifier does not exist');
    if (isset($exists['error']))
        return array('response' => false, 'error' => $exists['error']);
    
    $query = 'DELETE FROM `books` WHERE `Identifier` = ?';
    $statement = $conn->prepare($query);
    if (!$statement)
    {
        $statement->close();
        $conn->close();
        return array('response' => false, 'error' => 'Could not prepare statement in Book API V1');
    }

    $statement->bind_param('s', $Identifier);
    if (!$statement->execute())
    {
        $statement->close();
        $conn->close();
        return array('response' => false, 'error' => 'Could not execute statement in Book API V1');
    }

    $exists = CheckBookExists();
    if ($exists['response'])
        return array('response' => false, 'error' => 'The given book can not be removed from the database');
    if (isset($exists['error']))
        return array('response' => false, 'error' => $exists['error']);
    
    $statement->close();
    $conn->close();
    return array('response' => true);
}

header('Content-Type: application/json');

if (!isset($_POST['type']))
    die('{ "response" : false, "error" : "Book API `type` not provided" }');

if (!function_exists($_POST['type']))
    die('{ "response" : false, "error" : "Book API function `'.$_POST['type'].'` does not exist" }');

echo json_encode(($_POST['type']()));
?>