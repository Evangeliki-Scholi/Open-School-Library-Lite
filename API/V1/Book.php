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
    0 => array('AddBook', 'BorrowBook', 'CheckBookBorrowed', 'CheckBookExists', 'EditBook', 'GetBook', 'RemoveBook', 'ReturnBook'),
    1 => array('AddBook', 'BorrowBook', 'CheckBookBorrowed', 'CheckBookExists', 'EditBook', 'GetBook', 'RemoveBook', 'ReturnBook')
);

function AddBook($permissionLevels, $elevated)
{
    if (!array_key_exists($elevated, $permissionLevels) || !in_array(__FUNCTION__, $permissionLevels[$elevated]))
        return array('response' => false, 'error' => 'You do not have the right permissions');

    require_once '../../sql_connection.php';

    $Identifier = '';
    $Title = '';
    $Author = '';
    $Dewey = '';
    $ISBN = '';
    $Metadata = '{}';

    if (!isset($_POST['Identifier']) || $_POST['Identifier'] == '')
        return array('response' => false, 'error' => 'Identifier not provided');
    
    $exists = CheckBookExists($permissionLevels, $elevated);
    if ($exists['response'])
        return array('response' => false, 'error' => 'A book with that Identifier already exists');
    if (isset($exists['error']))
        return array('response' => false, 'error' => $exists['error']);

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
        $conn->close();
        return array('response' => false, 'error' => 'Could not prepare statement in Book API V1');
    }
    $statement->bind_param('ssssss', $Identifier, $Title, $Author, $Dewey, $ISBN, $Metadata);
    if (!$statement->execute())
    {
        $statement->close();
        $conn->close();
        return array('response' => false, 'error' => 'Could not execute statement in '.__FILE__.':'.__FUNCTION__.':'.__LINE__);
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
        return array('response' => false, 'error' => 'Could not execute statement in '.__FILE__.':'.__FUNCTION__.':'.__LINE__);
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

function BorrowBook($permissionLevels, $elevated)
{
    if (!array_key_exists($elevated, $permissionLevels) || !in_array(__FUNCTION__, $permissionLevels[$elevated]))
        return array('response' => false, 'error' => 'You do not have the right permissions');

    require_once '../../sql_connection.php';

    if (!isset($_POST['Identifier']) || $_POST['Identifier'] == '' || !isset($_POST['UserID']) || $_POST['UserID'] == '')
        return array('response' => false, 'error' => 'Book Identifier or User ID not provided');
    $Identifier = $_POST['Identifier'];
    $UserID = $_POST['UserID'];

    $exists = CheckBookExists($permissionLevels, $elevated);
    if (!$exists['response'])
        return array('response' => false, 'error' => 'A book with that Identifier does not exist');
    if (isset($exists['error']))
        return array('response' => false, 'error' => $exists['error']);

    $borrowed = CheckBookBorrowed($permissionLevels, $elevated);
    if ($borrowed['response'] && !isset($borrowed['error']))
        return array('response' => false, 'error' => 'A book with that Identifier is already borrowed');
    else if (isset($borrowed['error']))
        return $borrowed;

    $query = 'UPDATE `books` SET `Availability` = 0, `BorrowedByID` = ?, `BorrowedUntill` = ? WHERE `Identifier` = ?;';
    $statement = $conn->prepare($query);
    if (!$statement)
    {
        $conn->close();
        return array('response' => false, 'error' => 'Could not prepare statement in Book API V1');
    }

    $dateInTwoWeeks = date("Y-m-d", strtotime('+2 weeks'));

    $statement->bind_param('sss', $UserID, $dateInTwoWeeks,$Identifier);
    if (!$statement->execute())
    {
        $statement->close();
        $conn->close();
        return array('response' => false, 'error' => 'Could not execute statement in Book API V1');
    }
    $statement->close();
    $conn->close();
    return array('response' => true);
}

function CheckBookBorrowed($permissionLevels, $elevated)
{
    if (!array_key_exists($elevated, $permissionLevels) || !in_array(__FUNCTION__, $permissionLevels[$elevated]))
        return array('response' => false, 'error' => 'You do not have the right permissions');
        
    require_once '../../sql_connection.php';

    $exists = CheckBookExists($permissionLevels, $elevated);
    if (!$exists['response'])
        return array('response' => false, 'error' => 'A book with that Identifier does not exist');
    if (isset($exists['error']))
        return array('response' => false, 'error' => $exists['error']);

    $conn = GetDBConnection();
    if (!isset($_POST['Identifier']) || $_POST['Identifier'] == '')
        return array('response' => false, 'error' => 'Identifier not provided');
    
    $Identifier = $_POST['Identifier'];

    $query = 'SELECT `Availability` FROM `books` WHERE `Identifier` = ?;';
    $statement = $conn->prepare($query);
    if (!$statement)
    {
        $conn->close();
        return array('response' => false, 'error' => 'Could not prepare statement in Book API V1');
    }

    $statement->bind_param('s', $Identifier);
    if (!$statement->execute())
    {
        $statement->close();
        $conn->close();
        return array('response' => false, 'error' => 'Could not execute statement in '.__FILE__.':'.__FUNCTION__.':'.__LINE__);
    }

    $result = $statement->get_result();
    $row = $result->fetch_assoc();
    $statement->close();
    $conn->close();
    return array('response' => $row['Availability'] == 0);
}

function CheckBookExists($permissionLevels, $elevated)
{
    if (!array_key_exists($elevated, $permissionLevels) || !in_array(__FUNCTION__, $permissionLevels[$elevated]))
        return array('response' => false, 'error' => 'You do not have the right permissions');

    require_once '../../sql_connection.php';

    $conn = GetDBConnection();
    if (!isset($_POST['Identifier']) || $_POST['Identifier'] == '')
        return array('response' => false, 'error' => 'Identifier not provided');
    
    $Identifier = $_POST['Identifier'];

    $query = 'SELECT `ID` FROM `books` WHERE `Identifier` = ?;';
    $statement = $conn->prepare($query);
    if (!$statement)
    {
        $conn->close();
        return array('response' => false, 'error' => 'Could not prepare statement in Book API V1');
    }

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

function EditBook($permissionLevels, $elevated)
{
    if (!array_key_exists($elevated, $permissionLevels) || !in_array(__FUNCTION__, $permissionLevels[$elevated]))
        return array('response' => false, 'error' => 'You do not have the right permissions');

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

    $exists = CheckBookExists($permissionLevels, $elevated);
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
    if (isset($_POST['Metadata']))
    {
        json_decode($_POST['Metadata']);
        if (json_last_error() != JSON_ERROR_NONE)
        {
            $conn->close();
            return array('response' => false, 'error' => 'Metadata have to be in JSON formatting in Book API V1');
        }
        $Metadata = $_POST['Metadata'];
    }

    $query = 'SELECT `Title`, `Author`, `Dewey`, `ISBN`, `Metadata` FROM `books` WHERE `Identifier` = ?;';
    $statement = $conn->prepare($query);
    if (!$statement)
    {
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

function GetBook($permissionLevels, $elevated)
{
    if (!array_key_exists($elevated, $permissionLevels) || !in_array(__FUNCTION__, $permissionLevels[$elevated]))
        return array('response' => false, 'error' => 'You do not have the right permissions');

    require_once '../../sql_connection.php';

    if (!isset($_POST['Identifier']) || $_POST['Identifier'] == '')
        return array('response' => false, 'error' => 'Identifier not provided');
    $Identifier = $_POST['Identifier'];

    $exists = CheckBookExists($permissionLevels, $elevated);
    if (!$exists['response'])
        return array('response' => false, 'error' => 'A book with that Identifier does not exist');
    if (isset($exists['error']))
        return array('response' => false, 'error' => $exists['error']);

    $query = 'SELECT `Identifier`, `Title`, `Author`, `Dewey`, `ISBN`, `Availability`, `BorrowedUntill`, `BorrowedByID`, `Metadata` FROM `books` WHERE `Identifier` = ?;';
    $statement = $conn->prepare($query);
    if (!$statement)
    {
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

    $statement->close();
    $conn->close();
    return $row;
}

function RemoveBook($permissionLevels, $elevated)
{
    if (!array_key_exists($elevated, $permissionLevels) || !in_array(__FUNCTION__, $permissionLevels[$elevated]))
        return array('response' => false, 'error' => 'You do not have the right permissions');

    require_once '../../sql_connection.php';

    if (!isset($_POST['Identifier']) || $_POST['Identifier'] == '')
        return array('response' => false, 'error' => 'Identifier not provided');
    $Identifier = $_POST['Identifier'];

    $exists = CheckBookExists($permissionLevels, $elevated);
    if (!$exists['response'])
        return array('response' => false, 'error' => 'A book with that Identifier does not exist');
    if (isset($exists['error']))
        return array('response' => false, 'error' => $exists['error']);
    
    $query = 'DELETE FROM `books` WHERE `Identifier` = ?';
    $statement = $conn->prepare($query);
    if (!$statement)
    {
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

    $exists = CheckBookExists($permissionLevels, $elevated);
    if ($exists['response'])
        return array('response' => false, 'error' => 'The given book can not be removed from the database');
    if (isset($exists['error']))
        return array('response' => false, 'error' => $exists['error']);
    
    $statement->close();
    $conn->close();
    return array('response' => true);
}

function ReturnBook($permissionLevels, $elevated)
{
    if (!array_key_exists($elevated, $permissionLevels) || !in_array(__FUNCTION__, $permissionLevels[$elevated]))
        return array('response' => false, 'error' => 'You do not have the right permissions');

    require_once '../../sql_connection.php';

    if (!isset($_POST['Identifier']) || $_POST['Identifier'] == '')
        return array('response' => false, 'error' => 'Identifier not provided');
    $Identifier = $_POST['Identifier'];

    $exists = CheckBookExists($permissionLevels, $elevated);
    if (!$exists['response'])
        return array('response' => false, 'error' => 'A book with that Identifier does not exist');
    if (isset($exists['error']))
        return array('response' => false, 'error' => $exists['error']);

    $borrowed = CheckBookBorrowed($permissionLevels, $elevated);
    if (!$borrowed['response'] && !isset($borrowed['error']))
        return array('response' => false, 'error' => 'A book with that Identifier is not borrowed');
    else if (isset($borrowed['error']))
        return $borrowed;

    $query = 'UPDATE `books` SET `Availability` = 1, `BorrowedByID` = NULL, `BorrowedUntill` = NULL WHERE `Identifier` = ?;';
    $statement = $conn->prepare($query);
    if (!$statement)
    {
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
    $statement->close();
    $conn->close();
    return array('response' => true);
}

function SearchBooks($permissionLevels, $elevated)
{
    require_once '../../sql_connection.php';

    if (!isset($_POST['SearchTag']) || $_POST['SearchTag'] == '' || strlen($_POST['SearchTag']) < 2)
        return array('response' => false, 'error' => 'Search tag not provided');
    $SearchTag = '%'.$_POST['SearchTag'].'%';
    $SearchIdentifier = $_POST['SearchTag'];
    $Skip = (isset($_POST['Skip']) ? $_POST['Skip'] : 0);
    $Limit = (isset($_POST['Limit']) ? (($_POST['Limit'] <= 50) ? $_POST['Limit'] : 20) : 20);

    $query = '';
    if (0 <= $elevated && $elevated <= 1)
        $query = 'SELECT `books`.`Identifier`, `books`.`Title`, `books`.`Author`, `books`.`Dewey`, `books`.`ISBN`, `books`.`Availability`, `books`.`BorrowedUntill`, `users`.`Name` FROM `books` LEFT JOIN `users` ON `books`.`BorrowedByID` = `users`.`Identifier` WHERE ( UPPER(`books`.`TITLE`) LIKE UPPER(?) OR UPPER(`books`.`Author`) LIKE UPPER(?) OR UPPER(`books`.`Dewey`) LIKE UPPER(?) OR UPPER(`books`.`ISBN`) LIKE UPPER(?) OR `books`.`Identifier` = ?) ORDER BY `books`.`ID` LIMIT '.$Skip.', '.$Limit.';';
    else
        $query = 'SELECT `books`.`Identifier`, `books`.`Title`, `books`.`Author`, `books`.`Dewey`, `books`.`ISBN`, `books`.`Availability`, `books`.`BorrowedUntill` FROM `books` WHERE ( UPPER(`books`.`TITLE`) LIKE UPPER(?) OR UPPER(`books`.`Author`) LIKE UPPER(?) OR UPPER(`books`.`Dewey`) LIKE UPPER(?) OR UPPER(`books`.`ISBN`) LIKE UPPER(?) OR `books`.`Identifier` = ?) ORDER BY `books`.`ID` LIMIT '.$Skip.', '.$Limit.';';
    
    $statement = $conn->prepare($query);
    if (!$statement)
    {
        $conn->close();
        return array('response' => false, 'error' => 'Could not prepare statement in Book API V1');
    }

    $statement->bind_param('sssss', $SearchTag, $SearchTag, $SearchTag, $SearchTag, $SearchIdentifier);
    if (!$statement->execute())
    {
        $statement->close();
        $conn->close();
        return array('response' => false, 'error' => 'Could not execute statement in Book API V1');
    }

    $result = $statement->get_result();
    $results = array('response' => false, 'data' => array());

    if ($result->num_rows == 0)
        return $results;
    $results['response'] = true;
    while ($row = $result->fetch_assoc())
        $results['data'][] = $row;

    return $results;
}
*/

header('Content-Type: application/json');
echo '{"error":"Book API V1 has been deprecated"}';

/*
if (!isset($_POST['type']))
    die('{ "response" : false, "error" : "Book API `type` not provided" }');

if (!function_exists($_POST['type']))
    die('{ "response" : false, "error" : "Book API function `'.$_POST['type'].'` does not exist" }');

echo json_encode(($_POST['type']($permissionLevels, $elevated)), JSON_UNESCAPED_UNICODE);
*/

?>