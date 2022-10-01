<?php

try {

    $pdo = new PDO("mysql:dbname=home;host=localhost", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::MYSQL_ATTR_INIT_COMMAND, "SET NAMES 'UTF8'");

    function insertDb($id, $nom) {
        global $pdo;
        $req = $pdo->prepare('INSERT INTO test(id,nom) VALUES (:id,:nom)');
        $req->execute(array('id' => $id, 'nom' => $nom));
    }

    function update($id) {
        global $pdo;
        $req = $pdo->prepare('UPDATE test WHERE id = :id');
        $req->execute(array('id' => $id));
    }

    function delete($id) {
        global $pdo;
        $req = $pdo->prepare('DELETE * FROM test WHERE id = :id');
        $req->execute(array('id' => $id));
    }

    delete(1);

    function recherche($nom) {
        global $pdo;
        $req = $pdo->prepare('SELECT * FROM test WHERE nom = :nom');
        $req->execute(array('nom' => $nom));
    }

    function seekById($id) {
        global $pdo;
        $req = $pdo->prepare('SELECT * FROM test WHERE id = :id');
        $req->execute(array('id' => $id));
    }

} catch (PDOException $e) {
    die('Erreur : ' . $e->getMessage());
}
?>
