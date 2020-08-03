<?php

header('Content-Type: application/json');

if (!isset($_SESSION))
    session_start();
$elevated = (isset($_SESSION['Logged in']));

require 'bookFuncs.php';


if (!isset($_POST['type']))
{
    echo '{}';
    exit();
}
$type = $_POST['type'];

switch($type)
{
    case 'addBook':
        if (!isset($_GET['id']) || !isset($_GET['title']) || !isset($_GET['author']) || !isset($_GET['dewey']) || !isset($_GET['ISBN']))
        {
            echo '{ "response": false }';
            exit();
        }

        echo Book::AddBook($_GET['id'], $_GET['title'], $_GET['author'], $_GET['dewey'], $_GET['ISBN']);
        break;

    case 'borrowBook':
        if (!isset($_POST['bookID']) || !isset($_POST['userID']))
        {
            echo '{ "response": false }';
            exit();
        }

        $id = $_POST['bookID'];
        $userID = $_POST['userID'];
        
        borrowBook($id, $userID);
        
        break;

    case 'deleteBook':
        if (!isset($_GET['id']))
        {
            echo '{ "response": false }';
            exit();
        }

        echo Book::DeleteBook($_GET['id']);
        break;
    
    case 'editBook':
        if (!isset($_GET['id']) || !isset($_GET['title']) || !isset($_GET['author']) || !isset($_GET['dewey']) || !isset($_GET['ISBN']))
        {
            echo '{ "response": false }';
            exit();
        }

        echo Book::EditBook($_GET['id'], $_GET['title'], $_GET['author'], $_GET['dewey'], $_GET['ISBN']);
        break;
    
    case 'getBook':
        if (!isset($_POST['format']) || !isset($_POST['id']))
            echo '';
        else if ($_POST['format'] == 'json')
            echo Book::GetBookJSON($_POST['id'], $elevated);
        //else if ($_GET["format"] == "xml");
        //    echo Book::GetBookXML($_GET["id"], $elevated);
        break;

    case 'returnBook':
        if (!isset($_GET['id']))
        {
            echo '{ "response": false }';
            exit();
        }

        $id = $_GET['id'];
        returnBook($id);

        break;
    
    case 'searchBook':
        if (!isset($_GET['tag']))
        {
            echo '{}';
            exit();
        }

        /*$tag = $_GET["tag"];
        searchBook($tag);*/

        echo Book::searchBookJSON($_GET['tag'], 0, 10, $elevated);
        break;

    default:
        echo '{}';
        exit();
}
?>