<?php

session_start();
include '../db.php';

if (isset($_POST['iduser'])) {
    extract($_POST);
    $update = $pdo->prepare('UPDATE  users SET  etat =:etat'
            . ' WHERE id =:iduser');
    if ($update->execute(array(
                'etat' => 1,
                'iduser' => $iduser)
            )) {
        echo "<p class='alert alert-success'>User Deactived !!!</p>";
    } else {
        echo "<p class='alert alert-danger'>Deactivation Failed !!!</p>";
    }
    exit();
}


if (isset($_POST['iduseractive'])) {
    extract($_POST);
    $update = $pdo->prepare('UPDATE  users SET  etat =:etat'
            . ' WHERE id =:iduser');
    if ($update->execute(array(
                'etat' => 0,
                'iduser' => $iduseractive)
            )) {
        echo "<p class='alert alert-success'>User activated !!!</p>";
    } else {
        echo "<p class='alert alert-danger'>activation Failed !!!</p>";
    }
    exit();
}


if (isset($_POST["update"])) {
    extract($_POST);

    $update = $pdo->prepare('UPDATE  users SET  login =:login, pass =:pass, role =:role '
            . ' WHERE id =:iduser');
    if ($update->execute(array(
                'login' => $login,
                'pass' => $mot,
                'role' => $roletype,
                'iduser' => $userid)
            )) {
        echo "<p class='alert alert-success'>User Information Updated !!!</p>";
    } else {
        echo "<p class='alert alert-danger'>Update Failed !!!</p>";
    }

    exit();
}


if (!empty($_POST['login']) and ! empty($_POST['mot']) and ! empty($_POST['roletype'])) {
    extract($_POST);

    $station = $_SESSION['station'];

    try {

        if (existUser($pdo, $login)) {

            echo "<p class='alert alert-danger'>Recording Failed !!!</p>";
            echo "<p class='alert alert-danger'>User With this login is Already exist  !!!</p>";
            exit();
        }
    } catch (Exception $ex) {
        echo "<p class='alert alert-danger'>" . $ex->getMessage() . "</p>";
    }

    
    $search = $pdo->prepare('INSERT INTO users (login,pass,role,time,station,etat) '
            . 'VALUES (:login,:pass,:role,now(),:station,:etat)');
    if ($search->execute(array(
                'login' => $login,
                'pass' => $mot,
                'role' => $roletype,
                'station' => $station,
                'etat' => 0)
            )) {
        echo "<p class='alert alert-success'>Information Recorded !!!</p>";
        echo "<script type='text/javascript'>window.location.reload();</script>";
    } else {
        echo "<p class='alert alert-danger'>Recording Failed !!!</p>";
        
    }

    exit();
}

function existUser($pdo, $login) {

    $search = $pdo->prepare('SELECT id FROM users WHERE login =:login ');
    $search->execute(array("login" => $login));
    $result = $search->fetch(PDO::FETCH_OBJ);

    if ($result) {
        return true;
    }

    return false;
}
?>

