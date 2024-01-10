<?php
if (session_id() == "") session_start();
class Category { 
	
	private $categoryTable = 'categories';	
	private $conn;
	
	public function __construct($db){
        $this->conn = $db;
    }	
	
	public function getCategoryListing(){	
		if($_SESSION['user_type'] != 1) return;
		
		$sqlQuery = "
			SELECT id, name
			FROM ".$this->categoryTable."  
			 ";
		
		if(!empty($_POST["search"]["value"])){
			$sqlQuery .= ' name LIKE "%'.$_POST["search"]["value"].'%" ';				
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
		
		$stmtTotal = $this->conn->prepare("SELECT COUNT(*) as count; FROM ".$this->categoryTable);
		$stmtTotal->execute();
		$allResult = $stmtTotal->fetch(PDO::FETCH_ASSOC);
		$allRecords = $allResult['count'];		
		
		$displayRecords = count($result);
		$categories = array();		
		foreach ($result as $category) { 
			$rows = array();				
			$rows[] = $category['id'];
			$rows[] = $category['name'];					
			$rows[] = '<a href="add_categories.php?id='.$category["id"].'" class="btn btn-warning btn-xs update">Edit</a>';
			$rows[] = '<button type="button" name="delete" id="'.$category["id"].'" class="btn btn-danger btn-xs delete" >Delete</button>';
			$categories[] = $rows;
		}
		
		$output = array(
			"draw"	=>	intval($_POST["draw"]),			
			"iTotalRecords"	=> 	$displayRecords,
			"iTotalDisplayRecords"	=>  $allRecords,
			"data"	=> 	$categories
		);
		
		echo json_encode($output);	
	}
	
	public function getCategory(){		
		if($this->id) {
			$sqlQuery = "
			SELECT id, name
			FROM ".$this->categoryTable." 			
			WHERE id = :id; ";
			$stmt = $this->conn->prepare($sqlQuery);
			$stmt->bindParam("id", $this->id);	
			$stmt->execute();
			$result = $stmt->fetch(PDO::FETCH_ASSOC);
			$category = $result;
			return $category;
		}		
	}
	
	public function insert(){
		if($_SESSION['user_type'] != 1) return;

		if($this->name) {

			$stmt = $this->conn->prepare("
				INSERT INTO ".$this->categoryTable."(`name`)
				VALUES(:s)");
		
			$this->name = htmlspecialchars(strip_tags($this->name));						
			$stmt->bindParam("s", $this->name);
			
			if($stmt->execute()){
				return $stmt->insert_id;
			}		
		}
	}
	
	public function update(){
		if($_SESSION['user_type'] != 1) return;
		
		if($this->id) {			
			$stmt = $this->conn->prepare("
				UPDATE ".$this->categoryTable." 
				SET name= :s
				WHERE id = :i");
	 
			$this->id = htmlspecialchars(strip_tags($this->id));
			$this->name = htmlspecialchars(strip_tags($this->name));			
			
			$stmt->bindParam("s", $this->name);
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
				DELETE FROM ".$this->categoryTable." 				
				WHERE id = :i");

			$this->id = htmlspecialchars(strip_tags($this->id));

			$stmt->bindParam("i", $this->id);

			if($stmt->execute()){
				return true;
			}
		}
	}
	
	public function totalCategory(){		
		$sqlQuery = "SELECT COUNT(*) as count; FROM ".$this->categoryTable;			
		$stmt = $this->conn->prepare($sqlQuery);			
		$stmt->execute();
		$result = $stmt->fetch(PDO::FETCH_ASSOC);
		return $result['count'];
	}	
}
?>