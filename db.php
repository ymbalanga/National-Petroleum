<?php
try
	{

	 $pdo =  new PDO("mysql:dbname=station;host=localhost","root","");
     $pdo->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
     $pdo->setAttribute(PDO::MYSQL_ATTR_INIT_COMMAND, "SET NAMES 'UTF8'");
   
	}
	catch(PDOException $e)
	{
		die('Erreur : '.$e->getMessage());
	}
   ?>