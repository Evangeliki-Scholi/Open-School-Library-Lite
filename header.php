<?php
$elevated = (isset($_SESSION["logged in"]));
?>

<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="content-type" content="text/html; charset=UTF-8">
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css" integrity="sha384-9aIt2nRpC12Uk9gS9baDl411NQApFmC26EwAOH8WgZl5MYYxFfc+NcPb1dKGj7Sk" crossorigin="anonymous">
        <script src="https://code.jquery.com/jquery-3.5.1.min.js" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js" integrity="sha384-OgVRvuATP1z7JjHLkuOU7Xw704+h835Lr+6QL9UvYjZE3Ipu6Tp75j7Bh/kR0JKI" crossorigin="anonymous"></script>
        <script src="js/alert.js"></script>
    </head>
    <body>
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark" id="navbar">
            <a class="navbar-brand" href="#">Open School Library Lite</a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#collapsableNavBar" aria-controls="collapsableNavBar" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="collapsableNavBar">
                <ul class="navbar-nav mr-auto">
                    <li class="nav-item active">
                        <a class="nav-link" href="index.php">Home<span class="sr-only">(current)</span></a>
                    </li>
                <?php
                    if (isset($_SESSION) && isset($_SESSION["logged in"]) && $_SESSION["logged in"] === true)
                    {
                        echo "\t<li class=\"nav-item active\">
                        <a class=\"nav-link\" href=\"borrow.php\">Borrow</a>
                    </li>\n";
                        echo "\t\t\t\t\t<li class=\"nav-item active\">
                        <a class=\"nav-link\" href=\"return.php\">Return</a>
                    </li>\n";
                        echo "\t\t\t\t\t<li class=\"nav-item active\">
                        <a class=\"nav-link\" href=\"edit.php\">Edit Book</a>
                    </li>\n";
                        echo "\t\t\t\t\t<li class=\"nav-item active\">
                        <a class=\"nav-link\" href=\"new.php\">New Book</a>
                    </li>\n";
                        echo "\t\t\t\t\t<li class=\"nav-item active\">
                        <a class=\"nav-link\" href=\"addUser.php\">New User</a>
                    </li>\n";
                        echo "\t\t\t\t\t<li class=\"nav-item active\">
                        <button type=\"button\" class=\"btn btn-link\" style=\"color:#FFFFFF;text-decoration: none !important\" onclick=\"checkUpdate();\">Check for update</button>
                    </li>\n";
                    }
                ?>
                </ul>
                <form class="form-inline my-2 my-lg-0" onsubmit="return onSearch();">
                    <input class="form-control mr-sm-2" type="search" placeholder="Search" aria-label="Search" id="tagBook" autofocus>
                    <button class="btn btn-outline-success my-2 my-sm-0" type="Submit">Search</button>
                </form>
                &nbsp;&nbsp;
                <?php
                    if (isset($_SESSION) && isset($_SESSION["logged in"]) && $_SESSION["logged in"] === true)
                    {
                        echo "<form class=\"form-inline my-2 my-lg-0\" method=\"GET\" action=\"logout.php\">
                    <button class=\"btn btn-outline-success my-2 my-sm-0\" type=\"submit\">Logout</button>\n";
                    }
                    else
                    {
                        echo "<form class=\"form-inline my-2 my-lg-0\" method=\"GET\" action=\"login.php\">
                    <button class=\"btn btn-outline-success my-2 my-sm-0\" type=\"submit\">Login</button>\n";
                    }
                ?>
                </form>
            </div>
        </nav>
        <br>
        <br>
        <div id="content" style="width:85%;margin:0px auto">