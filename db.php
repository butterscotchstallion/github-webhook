<?php
require 'db-config.php';

function getConnection() {
	try {
		$connection = new PDO(sprintf('mysql:dbname=%s;host=%s;charset=utf8', 
									DB_NAME,
									DB_HOST),
									DB_USER, 
									DB_PASSWORD);
									
		$connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		
		return $connection;
	} catch(PDOException $e) {
		echo '<br>DB ERROR: ', $e->getMessage();
        return false;
	}
}