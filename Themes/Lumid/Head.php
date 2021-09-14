<?php

function StartHTML($Title = 'Open School Library Lite', $LoggedIn = false, $Level = 3, $Name = 'User')
{
    echo '<!DOCTYPE html>
<html>
';
}

function Head($Title = 'Open School Library Lite', $LoggedIn = false, $Level = 3, $Name = 'User')
{
    echo '  <head>
        <meta http-equiv="content-type" content="text/html; charset=UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="contributors" content="Rikarnto Bariampa">
        <title>'.$Title.((0 <= $Level && $Level <= 1) ? ' Admin Panel' : '').'</title>
        <link rel="icon" type="image/ico" href="favicon.ico">

        <!-- [Bootstrap] -->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css" integrity="sha384-TX8t27EcRE3e/ihU7zmQxVncDAy5uIKz4rEkgIXeMed4M0jlfIDPvg6uqKI2xXr2" crossorigin="anonymous">
        <!-- [/Bootstrap] -->

        <!-- [Admin LTE] -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/admin-lte/3.0.5/css/adminlte.min.css">
        <!-- [/Admin LTE] -->

        <script>
            const level = '.$Level.';
            const maxLevel = 3; 
        </script>

        <style>
            .custom-form-control {
                width: 100%;
                padding: 12px 20px;
                margin: 8px 0;
                display: inline-block;
                border: 1px solid #ccc;
                border-radius: 4px;
                box-sizing: border-box;
            }
        </style>
    </html>
';
}

?>