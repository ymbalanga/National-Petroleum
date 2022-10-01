<?php

session_start();

if ($_SESSION["login"] == '' && $_SESSION["role"] == '') {

    header("location : index.php");
} else {
   
}
?>