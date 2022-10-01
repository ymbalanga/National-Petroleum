<?php
session_start();

$_SESSION["station"] = '';
$_SESSION["taux"] = '';

header("location: choicestation.php");




