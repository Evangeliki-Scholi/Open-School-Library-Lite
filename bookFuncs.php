<?php

class Book
{
    private $_ID = null;
    private $_title = null;
    private $_author = null;
    private $_identifier = null;
    private $_dewey = null;
    private $_ISBN = null;
    private $_available = null;
    private $_borrowedByName = null;
    private $_borrowedUntil = null;

    public static function SearchBookJSON($tag, $skip, $limit, $elevated)
    {
        require_once "sql_connection.php";
        $sql = "";
        $etag = "%{$tag}%";

        if (!$elevated)
            $sql = "SELECT `books`.`Identifier`, `books`.`Title`, `books`.`Author`, `books`.`Dewey`, `books`.`ISBN`, `books`.`Availability`, `books`.`BorrowedUntil` FROM `books` WHERE ( UPPER(`books`.`TITLE`) LIKE UPPER(?) OR UPPER(`books`.`Author`) LIKE UPPER(?) OR UPPER(`books`.`Dewey`) LIKE UPPER(?) OR UPPER(`books`.`ISBN`) LIKE UPPER(?) OR  `books`.`Identifier` = ?) ORDER BY `books`.`ID`";
        else
            $sql = "SELECT `books`.`Identifier`, `books`.`Title`, `books`.`Author`, `books`.`Dewey`, `books`.`ISBN`, `books`.`Availability`, `books`.`BorrowedUntil`, `users`.`Name` FROM `books` LEFT JOIN `users` ON `books`.`BorrowedByID` = `users`.`ID` WHERE ( UPPER(`books`.`Title`) LIKE UPPER(?) OR UPPER(`books`.`Author`) LIKE UPPER(?) OR UPPER(`books`.`Dewey`) LIKE UPPER(?) OR UPPER(`books`.`ISBN`) LIKE UPPER(?) OR `books`.`Identifier` = ?) ORDER BY `books`.`ID`";
        
        $sql = $sql." LIMIT 25";
        $sql = $conn->prepare($sql);
        $sql->bind_param("sssss", $etag, $etag, $etag, $etag, $tag);

        if (!$sql->execute()) return "[]";
        
        $result = $sql->get_result();
        $results = array();

        while ($row = $result->fetch_assoc())
            $results[] = $row;

        return json_encode($results, JSON_UNESCAPED_UNICODE);
    }

    public static function DeleteBook($id)
    {
        require_once "sql_connection.php";
        $sql = $conn->prepare("DELETE FROM `books` WHERE `books`.`ID` = ?");
        $sql->bind_param("d", $id);
        return (($sql->execute()) ? "{ \"response\": true }" : "{ \"response\": false }");
    }

    public static function EditBook($id, $title, $author, $dewey, $ISBN)
    {
        require_once "sql_connection.php";
        $sql = $conn->prepare("UPDATE `books` SET `Title` = ?, `Author` = ?, `Dewey` = ?, `ISBN` = ? WHERE `books`.`ID` = ?;");
        $sql->bind_param("ssssi", $title, $author, $dewey, $ISBN, $id);
        return (($sql->execute()) ? "{ \"response\": true }" : "{ \"response\": false }");
    }

    public static function GetBook($identifier)
    {
        require_once "sql_connection.php";
        $sql = "SELECT `books`.`ID`, `books`.`Identifier`, `books`.`Title`, `books`.`Author`, `books`.`Dewey`, `books`.`ISBN`, `books`.`Availability`, `books`.`BorrowedUntil`";
        if (isset($elevated) && $elevated == true)
            $sql = $sql.", `users`.`Name` FROM `books`, `users` WHERE ((`books`.`Availability` != 0 OR `books`.`BorrowedByID` = `users`.`ID`) AND `books`.`Identifier` = ?) LIMIT 1;";
        else
            $sql = $sql." FROM `books` WHERE `books`.`Identifier` = ? LIMIT 1;";

        $sql = $conn->prepare($sql);
        $sql->bind_param("s", $identifier);

        if (!$sql->execute()) return null;
        $result = $sql->get_result();
        if (!isset($result->num_rows) || $result->num_rows != 1) return null;
        $returnVal = new Book();
        $returnVal->_ID = $result["ID"];
        $returnVal->_title = $result["Title"];
        $returnVal->_author = $result["Author"];
        $returnVal->_identifier = $result["Identifier"];
        $returnVal->_dewey = $result["Dewey"];
        $returnVal->_ISBN = $result["ISBN"];
        $returnVal->_available = $result["Availability"];
        $returnVal->_borrowedUntil = $result["BorrowedUntil"];
        if (isset($elevated) && $elevated == true)
            $returnVal->_borrowedByName = $result["Name"];
        return $returnVal;
    }

    public static function GetBookJSON($identifier, $elevated)
    {
        require "sql_connection.php";
        $sql = "SELECT `books`.`ID`, `books`.`Identifier`, `books`.`Title`, `books`.`Author`, `books`.`Dewey`, `books`.`ISBN`, `books`.`Availability`, `books`.`BorrowedUntil`";
        if ($elevated)
            $sql = $sql.", `users`.`Name` FROM `books`, `users` WHERE ((`books`.`Availability` != 0 OR `books`.`BorrowedByID` = `users`.`ID`) AND `books`.`Identifier` = ?) LIMIT 1;";
        else
            $sql = $sql." FROM `books` WHERE `books`.`Identifier` = ? LIMIT 1;";

        $sql = $conn->prepare($sql);
        $sql->bind_param("d", $identifier);

        if (!$sql->execute()) return "{}";
        $result = $sql->get_result();
        if (!isset($result->num_rows) || $result->num_rows == 0) return "{}";
        $result = $result->fetch_assoc();

        if ($elevated && isset($result["Availability"]) && $result["Availability"] != 0)
            $result["Name"] = null;

        $sql = $conn->prepare("UPDATE `books` SET `Found` = 1 WHERE `books`.`Identifier` = ?");
        $sql->bind_param("d", $identifier);
        if (!$sql->execute()) return "{}";

        return json_encode($result, JSON_UNESCAPED_UNICODE);
    }

    public static function AddBook($identifier, $title, $author, $dewey, $ISBN)
    {
        require "sql_connection.php";

        $sql = $conn->prepare("INSERT INTO `books` (`ID`, `Identifier`, `Title`, `Author`, `Dewey`, `ISBN`, `Availability`, `Found`) VALUES (NULL, ?, ?, ?, ?, ?, 1, 1);");
        $sql->bind_param("sssss", $identifier, $title, $author, $dewey, $ISBN);
        return (($sql->execute()) ? "{ \"response\": true }" : "{ \"response\": false }");
    }
    /**
     * public static function GetBookXML($identifier, $elevated)
     * {
     *      require "sql_connection.php";
     *      $sql = "SELECT `books`.`ID`, `books`.`Identifier`, `books`.`Title`, `books`.`Author`, `books`.`Dewey`, `books`.`ISBN`, `books`.`Availability`, `books`.`BorrowedUntil`";
     *      if ($elevated)
     *          $sql = $sql.", `users`.`Name` FROM `books`, `users` WHERE ((`books`.`Availability` != 0 OR `books`.`BorrowedByID` = `users`.`ID`) AND `books`.`Identifier` = ?) LIMIT 1;";
     *      else
     *          $sql = $sql." FROM `books` WHERE `books`.`Identifier` = ? LIMIT 1;";
     * 
     *      $sql = $conn->prepare($sql);
     *      $sql->bind_param("d", $identifier);
     * 
     *      if (!$sql->execute()) return "{}";
     *      $result = $sql->get_result();
     *      if (!isset($result->num_rows) || $result->num_rows == 0) return "{}";
     *      $result = $result->fetch_assoc();
     * 
     *      if ($elevated && isset($result["Availability"]) && $result["Availability"] != 0)
     *          $result["Name"] = null;
     * 
     *      return xmlrpc_encode($result);
     * }
     **/
}

function addBook($barcode, $title, $author, $location)
{
    require_once "sql_connection.php";
    $barcode = md5($barcode);
    $sql = $conn->prepare("INSERT INTO `books` (`id`, `id-md5`, `title`, `author`, `location`, `availability`, `borrowedBy`, `borrowedUntil`) VALUES (NULL, ?, ?, ?, ?, 1, \"\", \"0000-00-00\");");
    $sql->bind_param("ssss", $barcode, $title, $author, $location);
    return $sql->execute();
}

function borrowBook($id, $userID)
{
    require_once "sql_connection.php";
    $dateInTwoWeeks = strtotime('+2 weeks');
    $sql = "UPDATE `books` SET `Availability` = '0', `BorrowedByID` = ?, `BorrowedUntil` = \"".date("Y-m-d", $dateInTwoWeeks)."\" WHERE `books`.`Identifier` = ?;";
    //echo $sql;
    $sql = $conn->prepare($sql);
    $sql->bind_param("ii", $userID, $id);
    echo (($sql->execute()) ? "{ \"response\": true }" : "{ \"response\": false }");
}

function returnBook($id)
{
    require_once "sql_connection.php";
    $sql = $conn->prepare("UPDATE `books` SET `Availability` = 1, `BorrowedByID` = null, `BorrowedUntil` = null WHERE `books`.`ID` = ?");
    $sql->bind_param("i", $id);
    echo (($sql->execute()) ? "{ \"response\": true }" : "{ \"response\": false }");
}

?>