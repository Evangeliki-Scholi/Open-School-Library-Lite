<?php
$elevated = (isset($_SESSION['Logged in'])) ? $_SESSION['Logged in'] === true : false;

function GetSetting ($name)
{
    require 'settings.php';
    return ((isset($_SETTINGS[$name])) ? $_SETTINGS[$name] : $name);
};

?>

<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="content-type" content="text/html; charset=UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="contributors" content="Rikarnto Bariampa">
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css" integrity="sha384-9aIt2nRpC12Uk9gS9baDl411NQApFmC26EwAOH8WgZl5MYYxFfc+NcPb1dKGj7Sk" crossorigin="anonymous">
        <script src="https://code.jquery.com/jquery-3.5.1.min.js" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js" integrity="sha384-OgVRvuATP1z7JjHLkuOU7Xw704+h835Lr+6QL9UvYjZE3Ipu6Tp75j7Bh/kR0JKI" crossorigin="anonymous"></script>
        <script src="js/alert.js"></script>
        <script src="js/hash.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/js-sha512/0.8.0/sha512.min.js"></script>
    </head>
    <body>
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark" id="navbar">
            <a class="navbar-brand" href="#"><?php echo (isset($_SETTINGS["Site Name"]) ? $_SETTINGS["Site Name"] : "Open School Library Lite"); ?></a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#collapsableNavBar" aria-controls="collapsableNavBar" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="collapsableNavBar">
                <ul class="navbar-nav mr-auto">
                    <li class="nav-item active">
                        <a class="nav-link" href="index.php"><?php echo (isset($_SETTINGS['Home']) ? $_SETTINGS['Home'] : 'Home'); ?><span class="sr-only">(current)</span></a>
                    </li>
                    <?php
                    if ($elevated)
                    {
                        echo '<li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Books</a>
                        <div class="dropdown-menu" aria-labelledby="navbarDropdown">';
                        echo '<a class="dropdown-item" href="borrow.php">'.GetSetting('Borrow').'</a>';
                        echo '<a class="dropdown-item" href="return.php">'.GetSetting('Return').'</a>';
                        echo '<a class="dropdown-item" href="edit.php">'.GetSetting('Edit Book').'</a>';
                        echo '<a class="dropdown-item" href="new.php">'.GetSetting('New Book').'</a>';
                        echo '</div></li>';

                        echo '<li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Users</a>
                        <div class="dropdown-menu" aria-labelledby="navbarDropdown">';
                        echo '<a class="dropdown-item" href="addUser.php">'.GetSetting('New User').'</a>';
                        echo '</div></li>';

                        echo '<li class="nav-item active"><a class="nav-link" href="settingsFuncs.php">'.GetSetting('Settings').'</a></li>';
                        echo '<li class="nav-item active"><button type="button" class="btn btn-link" style="color:#FFFFFF;text-decoration: none !important" onclick="checkUpdate();">'.GetSetting('Check Update').'</button></li>';
                    }
                    ?>
                </ul>
                <form class="form-inline my-2 my-lg-0" onsubmit="return onSearch();">
                    <?php
                    echo '<input class="form-control mr-sm-2" type="search" placeholder="'.GetSetting('Search').'" aria-label="Search" id="tagBook" autofocus>';
                    echo '<button class="btn btn-outline-success my-2 my-sm-0" type="Submit">'.GetSetting('Search').'</button>';
                    ?>
                </form>
                &nbsp;&nbsp;
                <?php
                    if ($elevated)
                        echo '<form class="form-inline my-2 my-lg-0" method="GET" action="account.php"><button class="btn btn-outline-success my-2 my-sm-0" type="submit">Account</button>';
                    else
                        echo '<form class="form-inline my-2 my-lg-0" method="GET" action="login.php"><button class="btn btn-outline-success my-2 my-sm-0" type="submit">Login</button>';
                ?>
                </form>
            </div>
        </nav>
        <br>
        <br>
        <div id="content" style="width:85%;margin:0px auto">