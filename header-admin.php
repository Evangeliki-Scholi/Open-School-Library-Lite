<?php

function StartHTML()
{
	echo '<!DOCTYPE html>
<html>
';
}

function HEAD($NameOfTitle)
{
	echo "\t".'<head>
		<meta http-equiv="content-type" content="text/html; charset=UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<meta name="contributors" content="Rikarnto Bariampa">
		<title>'.$NameOfTitle.'</title>
		<link rel="icon" type="image/ico" href="favicon.ico">

		<!-- [Bootstrap] -->
		<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css" integrity="sha384-TX8t27EcRE3e/ihU7zmQxVncDAy5uIKz4rEkgIXeMed4M0jlfIDPvg6uqKI2xXr2" crossorigin="anonymous">
		<!-- [/Bootstrap] -->

		<!-- [Admin LTE] -->
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/admin-lte/3.0.5/css/adminlte.min.css">
		<!-- [/Admin LTE] -->
	</head>
';
}

?>