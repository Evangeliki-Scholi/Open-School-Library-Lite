<?php

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

$elevated = (isset($_SESSION['Logged in']) && isset($_SESSION['Level'])) ? $_SESSION['Level'] : 3;

function GetSetting ($name)
{
    include 'settings.php';
    if (isset($_SETTINGS) && isset($_SETTINGS[$name]))
        return $_SETTINGS[$name];
    else
        return $name;
};

?>
<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="content-type" content="text/html; charset=UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="contributors" content="Rikarnto Bariampa">
        <title><?php echo GetSetting('Site Name'); ?></title>
        <link rel="icon" type="image/ico" href="favicon.ico">
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css" integrity="sha384-9aIt2nRpC12Uk9gS9baDl411NQApFmC26EwAOH8WgZl5MYYxFfc+NcPb1dKGj7Sk" crossorigin="anonymous">
        <script src="https://code.jquery.com/jquery-3.5.1.min.js" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js" integrity="sha384-OgVRvuATP1z7JjHLkuOU7Xw704+h835Lr+6QL9UvYjZE3Ipu6Tp75j7Bh/kR0JKI" crossorigin="anonymous"></script>
        <script>const elevated = <?php echo $elevated; ?>;</script>
        <script src="js/alert.js"></script>
        <script src="js/mainPreload.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/js-sha512/0.8.0/sha512.min.js"></script>
    </head>
    <body>
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark" id="navbar">
            <a class="navbar-brand" href="#"><?php echo GetSetting('Site Name'); ?></a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#collapsableNavBar" aria-controls="collapsableNavBar" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="collapsableNavBar">
                <ul class="navbar-nav mr-auto" id="navbarItems">
                    <li class="nav-item active">
                    <li class="nav-item active"><button type="button" class="btn btn-link" style="color:#FFFFFF;text-decoration: none !important" onclick="ShowIndex()"><?php echo GetSetting('Home'); ?></button></li>
                    </li>
                <?php
                    if ($elevated < 2)
                    {
                        echo '    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Books</a>
                        <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                            ';
                        echo '<button class="dropdown-item" onclick="ShowBorrowBook();">'.GetSetting('Borrow').'</button>
                            ';
                        echo '<button class="dropdown-item" onclick="ShowReturnBook();">'.GetSetting('Return').'</button>
                            ';
                        echo '<button class="dropdown-item" onclick="ShowEditBook();">'.GetSetting('Edit Book').'</button>
                            ';
                        echo '<button class="dropdown-item" onclick="ShowAddBook();">'.GetSetting('New Book').'</button>
                        ';
                        echo '</div>
                    </li>
                    ';

                        echo '<li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Users</a>
                        <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                            ';
                        echo '<button class="dropdown-item" onclick="ShowAddUser();">'.GetSetting('New User').'</button>
                            ';
                        echo '<button class="dropdown-item" onclick="ShowEditUser();">'.GetSetting('Edit User').'</button>
                        ';
                        echo '</div>
                    </li>
                    ';
                    }
                    if ($elevated < 1)
                    {
                        echo '<li class="nav-item active"><a class="nav-link" href="settingsFuncs.php">'.GetSetting('Settings').'</a></li>
                    ';
                        echo '<li class="nav-item active"><button type="button" class="btn btn-link" style="color:#FFFFFF;text-decoration: none !important" onclick="checkUpdate();">'.GetSetting('Check Update').'</button></li>
                ';
                    }
                ?>
</ul>
                <input class="form-control" style="width: 40%" type="search" placeholder="<?php echo GetSetting('Search'); ?>" aria-label="Search" id="tagBook">
                &nbsp;&nbsp;
                <button class="btn btn-outline-success my-2 my-sm-0" onclick="PerformSearch();"><?php echo GetSetting('Search'); ?></button>
                &nbsp;&nbsp;
                <?php
                    if ($elevated < 3)
                        echo '<button class="btn btn-outline-success my-2 my-sm-0" onclick="ShowAccount()">'.GetSetting('Account').'</button>
';
                    else
                        echo '<button class="btn btn-outline-success my-2 my-sm-0" onclick="ShowLogin()">'.GetSetting('Login').'</button>
';
                ?>
            </div>
        </nav>
        <br>
        <br>
        <br>
        <div id="content" style="width:85%;margin:0px auto">