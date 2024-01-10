<?php
class Database{
    
    public function getConnection(){		
		try {

			if (file_exists("./database.sqlite")) {
			    $con = new \PDO("sqlite:./database.sqlite");
			} else {
			    $con = new \PDO("sqlite:../database.sqlite");
			}
			
			return $con;
		}catch (PDOException $err) {
		    echo $err->getMessage();
		    exit;
		}
    }
}
?>