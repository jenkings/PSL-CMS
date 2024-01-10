<?php
if (session_id() == "") session_start();

class User {	
   
	const SALT = "qwe4165dfh";

	private $userTable = 'users';	
	private $conn;
	
	public function __construct($db){
        $this->conn = $db;
    }	    
	
	public function login(){
		if($this->nick && $this->password) {
			$hash = crypt($this->password,self::SALT);
			$sqlQuery = "
				SELECT * FROM ".$this->userTable." 
				WHERE nick = :nick AND password = :pass";
			$stmt = $this->conn->prepare($sqlQuery);

			

			$stmt->bindParam("nick", $this->nick, );	
			$stmt->bindParam("pass",  $hash);	
			$stmt->execute();
			$result = $stmt->fetch(PDO::FETCH_ASSOC);
			if($result){
				$user = $result;
				$_SESSION["userid"] = $user['id'];
				$_SESSION["user_type"] = $user['type'];
				$_SESSION["name"] = $user['first_name']." ".$user['last_name'];					
				return 1;		
			} else {
				return 0;		
			}			
		} else {
			return 0;
		}
	}
	
	public function loggedIn (){
		if(!empty($_SESSION["userid"])) {
			return 1;
		} else {
			return 0;
		}
	}
	
	public function totalUser(){		
		$sqlQuery = "SELECT COUNT(*) as count FROM ".$this->userTable;			
		$stmt = $this->conn->prepare($sqlQuery);			
		$stmt->execute();
		$result = $stmt->fetch(PDO::FETCH_ASSOC);
		return $result['count'];
	}	

	public function getUsersListing(){		
		
		$whereQuery = '';
		if($_SESSION['user_type'] == 2) {
			$whereQuery = "WHERE id ='".$_SESSION['userid']."'";
		}		
		
		$sqlQuery = "
			SELECT id, first_name, last_name, nick, type, deleted
			FROM ".$this->userTable."  
			$whereQuery ";
		
		if(!empty($_POST["search"]["value"])){
			$sqlQuery .= ' first_name LIKE "%'.$_POST["search"]["value"].'%" ';
			$sqlQuery .= ' OR last_name LIKE "%'.$_POST["search"]["value"].'%" ';
			$sqlQuery .= ' OR nick LIKE "%'.$_POST["search"]["value"].'%" ';
			$sqlQuery .= ' OR type LIKE "%'.$_POST["search"]["value"].'%" ';			
		}
		if(!empty($_POST["order"])){
			$sqlQuery .= 'ORDER BY '.$_POST['order']['0']['column'].' '.$_POST['order']['0']['dir'].' ';
		} else {
			$sqlQuery .= 'ORDER BY id DESC ';
		}
		if($_POST["length"] != -1){
			$sqlQuery .= 'LIMIT ' . $_POST['start'] . ', ' . $_POST['length'];
		}

		$stmt = $this->conn->prepare($sqlQuery);
		$stmt->execute();
		$result = $stmt->fetchAll();	
		
		$stmtTotal = $this->conn->prepare("SELECT COUNT(*) as count FROM ".$this->userTable);
		$stmtTotal->execute();
		$allResult = $stmtTotal->fetch(PDO::FETCH_ASSOC);
		$allRecords = $allResult['count'];
		
		
		$displayRecords = count($result);
		$users = array();		
		foreach ($result as $user) { 				
			$rows = array();	
			$status = '';
			if($user['deleted'])	{
				$status = '<span class="label label-danger">Inactive</span>';
			} else {
				$status = '<span class="label label-success">Active</span>';
			}
			
			$type = '';
			if($user['type'] == 1){
				$type = '<span class="label label-danger">Admin</span>';
			} else if($user['type'] == 2){
				$type = '<span class="label label-warning">Author</span>';
			}
			
			$rows[] = ucfirst($user['first_name'])." ".$user['last_name'];
			$rows[] = $user['nick'];
			$rows[] = $type;			
			$rows[] = $status;				
			$rows[] = '<a href="add_users.php?id='.$user["id"].'" class="btn btn-warning btn-xs update">Edit</a>';
			$rows[] = '<button type="button" name="delete" id="'.$user["id"].'" class="btn btn-danger btn-xs delete" >Delete</button>';
			$users[] = $rows;
		}
		
		$output = array(
			"draw"	=>	intval($_POST["draw"]),			
			"iTotalRecords"	=> 	$displayRecords,
			"iTotalDisplayRecords"	=>  $allRecords,
			"data"	=> 	$users
		);
		
		echo json_encode($output);	
	}
	
	public function getUser(){		
		if($this->id) {
			$sqlQuery = "
			SELECT id, first_name, last_name, nick, type, deleted
			FROM ".$this->userTable." 			
			WHERE id = :i ";
			$stmt = $this->conn->prepare($sqlQuery);
			$stmt->bindParam("i", $this->id);	
			$stmt->execute();
			$user = $stmt->fetch(PDO::FETCH_ASSOC);
			return $user;
		}		
	}
	
	public function insert(){
		if($_SESSION['user_type'] != 1) return;
		
		if($this->nick && $this->password) {

			$stmt = $this->conn->prepare("
				INSERT INTO ".$this->userTable."(`first_name`, `last_name`, `nick`, `password`, `type`, `deleted`)
				VALUES(:f,:l,:e,:p,:t,:d)");
		
			$this->first_name = htmlspecialchars(strip_tags($this->first_name));
			$this->last_name = htmlspecialchars(strip_tags($this->last_name));
			$this->nick = htmlspecialchars(strip_tags($this->nick));
			$this->password = htmlspecialchars(strip_tags($this->password));
			$this->type = htmlspecialchars(strip_tags($this->type));
			$this->deleted = htmlspecialchars(strip_tags($this->deleted));		
						
			$stmt->bindParam("f", $this->first_name);
			$stmt->bindParam("l", $this->last_name);
			$stmt->bindParam("e", $this->nick);
			$stmt->bindParam("p", crypt($this->password,self::SALT));
			$stmt->bindParam("t", $this->type);
			$stmt->bindParam("d", $this->deleted);

			
			if($stmt->execute()){
				return $this->conn->lastInsertId();
			}		
		}
	}
	
	public function update(){
		if($_SESSION['user_type'] != 1) return;


		if($this->id) {			
			$stmt = $this->conn->prepare("
				UPDATE ".$this->userTable." 
				SET first_name= :f, last_name = :l, nick = :e, type = :t, deleted= :d
				WHERE id = :i");
	 
			$this->id = htmlspecialchars(strip_tags($this->id));
			$this->first_name = htmlspecialchars(strip_tags($this->first_name));
			$this->last_name = htmlspecialchars(strip_tags($this->last_name));
			$this->nick = htmlspecialchars(strip_tags($this->nick));
			$this->type = htmlspecialchars(strip_tags($this->type));
			$this->deleted = htmlspecialchars(strip_tags($this->deleted));			
			
			$stmt->bindParam("f", $this->first_name);
			$stmt->bindParam("l", $this->last_name);
			$stmt->bindParam("e", $this->nick);
			$stmt->bindParam("t", $this->type);
			$stmt->bindParam("d", $this->deleted);
			$stmt->bindParam("i", $this->id);
			
			if($stmt->execute()){
				return true;
			}			
		}
		
	}
	
	public function delete(){
		if($_SESSION['user_type'] != 1) return;
		if($this->id) {	
		
			$stmt = $this->conn->prepare("
				DELETE FROM ".$this->userTable." 				
				WHERE id = :i");

			$this->id = htmlspecialchars(strip_tags($this->id));

			$stmt->bindParam("i", $this->id);

			if($stmt->execute()){
				return true;
			}
		}
	}

}
?>