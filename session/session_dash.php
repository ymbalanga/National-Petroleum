<?php

session_start();

if ($_SESSION["login"] == '' && $_SESSION["role"] == '') {

    header("location : index.php");
} elseif ($_SESSION["station"] == '' && $_SESSION["taux"] == '') {
    header("location: choicestation.php");
    
} else {
    
}
?>