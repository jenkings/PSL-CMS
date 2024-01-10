<?php
if (session_id() == "") session_start();
class Menu { 
	
	private $menuTable = 'menu';	
	private $conn;
	
	public function __construct($db){
        $this->conn = $db;
    }	
	
	public function getMenuListing(){
		if($_SESSION['user_type'] != 1) return;

		$sqlQuery = "
			SELECT id,title,link,`order` 
			FROM ".$this->menuTable."  
			 ";
		if(!empty($_POST["search"]["value"])){
			$sqlQuery .= ' title LIKE "%'.$_POST["search"]["value"].'%" ';				
		}
		if(!empty($_POST["order"])){
			$sqlQuery .= 'ORDER BY '.$_POST['order']['0']['column'].' '.$_POST['order']['0']['dir'].' ';
		} else {
			$sqlQuery .= 'ORDER BY `order` ASC ';
		}
		if($_POST["length"] != -1){
			$sqlQuery .= 'LIMIT ' . $_POST['start'] . ', ' . $_POST['length'];
		}

		$stmt = $this->conn->prepare($sqlQuery);
		$stmt->execute();
		$result = $stmt->fetchAll();	
		
		$stmtTotal = $this->conn->prepare("SELECT COUNT(*) as count; FROM ".$this->menuTable);
		$stmtTotal->execute();
		$allResult = $stmtTotal->fetch(PDO::FETCH_ASSOC);
		$allRecords = $allResult['count'];		
		
		$displayRecords = count($result);
		$menu = array();		
		foreach ($result as $m) { 
			$rows = array();				
			$rows[] = $m['order'] . " &nbsp;&nbsp;" .
			'<button type="button" name="moveup" id="'.$m["id"].'" class="btn btn-success btn-xs moveup" ><span class="glyphicon glyphicon-triangle-top" aria-hidden="true"></span></button>' .
			'<button type="button" name="movedown" id="'.$m["id"].'" class="btn btn-success btn-xs movedown" ><span class="glyphicon glyphicon-triangle-bottom" aria-hidden="true"></span></button>' ;
			$rows[] = $m['title'];
			$rows[] = $m['link'];
			$rows[] = '<a href="add_menu.php?id='.$m["id"].'" class="btn btn-warning btn-xs update">Edit</a>';
			$rows[] = '<button type="button" name="delete" id="'.$m["id"].'" class="btn btn-danger btn-xs delete" >Delete</button>';
			$menu[] = $rows;
		}
		
		$output = array(
			"draw"	=>	intval($_POST["draw"]),			
			"iTotalRecords"	=> 	$displayRecords,
			"iTotalDisplayRecords"	=>  $allRecords,
			"data"	=> 	$menu
		);
		
		echo json_encode($output);	
	}

	public function getMenu(){		
		if($this->id) {
			$sqlQuery = "
				SELECT id,link,title 
				FROM ".$this->menuTable." 
				WHERE id = :id ";
			$stmt = $this->conn->prepare($sqlQuery);
			$stmt->bindParam("id", $this->id);	
			$stmt->execute();
			$result = $stmt->fetch();
			$post = $result;
			return $post;
		}		
	}
	
	
	public function getMenuArray(){		
			$sqlQuery = "
			SELECT id, `order`,title,link
			FROM ".$this->menuTable." 			
			ORDER BY `order` ASC ";
			$stmt = $this->conn->prepare($sqlQuery);
			$stmt->execute();
			$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
			return $result;	
	}
	
	public function insert(){
		if($_SESSION['user_type'] != 1) return;

		if($this->title.$this->link) {
			$maxQuery = $this->conn->prepare("SELECT MAX(`order`) as max FROM ".$this->menuTable."");
			$maxQuery->execute();
			$maxResult = $maxQuery->fetch(PDO::FETCH_ASSOC);
			$max = ($maxResult['max'] * 1) + 1;

			$stmt = $this->conn->prepare("
				INSERT INTO ".$this->menuTable."(`title`,`link`,`order`)
				VALUES(:s1,:s2,:s3)");
		
			$this->title = htmlspecialchars(strip_tags($this->title));
			$this->link = htmlspecialchars(strip_tags($this->link));
			
			$stmt->bindParam("s1", $this->title);
			$stmt->bindParam("s2", $this->link);
			$stmt->bindParam("s3", $max);
			
			if($stmt->execute()){
				return $this->conn->lastInsertId();
			}		
		}
	}
	
	public function update(){
		if($_SESSION['user_type'] != 1) return;

		if($this->id) {			
			$stmt = $this->conn->prepare("
				UPDATE ".$this->menuTable." 
				SET title= :s1, link = :s2
				WHERE id = :i");
	 
			$this->id = htmlspecialchars(strip_tags($this->id));
			$this->link = htmlspecialchars(strip_tags($this->link));
			$this->title = htmlspecialchars(strip_tags($this->title));
			
			$stmt->bindParam("s1", $this->title);
			$stmt->bindParam("s2", $this->link);
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
				DELETE FROM ".$this->menuTable." 				
				WHERE id = :i");

			$this->id = htmlspecialchars(strip_tags($this->id));

			$stmt->bindParam("i", $this->id);

			if($stmt->execute()){
				return true;
			}
		}
	}



	public function moveUp(){
		if($_SESSION['user_type'] != 1) return;

		if($this->id) {		

			$curOrderQuery = $this->conn->prepare("SELECT `order` as o FROM ".$this->menuTable." WHERE id = :id");
			$this->id = htmlspecialchars(strip_tags($this->id));
			$curOrderQuery->bindParam("id", $this->id);
			$curOrderQuery->execute();
			$curOrderResult = $curOrderQuery->fetch(PDO::FETCH_ASSOC);
			$curOrder = ($curOrderResult['o'] * 1);

			if($curOrder == 0) return; //Nižší už nejde

			$q1 = $this->conn->prepare("UPDATE ".$this->menuTable." SET `order` = null WHERE id = :id");
			$q1->bindParam("id", $this->id);
			$q1->execute();
			$q1->execute();

			$replacementOrder = $curOrder - 1;
			$q2 = $this->conn->prepare("UPDATE ".$this->menuTable." SET `order` = `order` + 1 WHERE `order` = :order");
			$q2->bindParam("order", $replacementOrder);
			$q2->execute();
			$q2->execute();

			$newOrder = ($curOrder-1);
			$q3 = $this->conn->prepare("UPDATE ".$this->menuTable." SET `order` = :order  WHERE id = :id");
			$q3->bindParam("order", $newOrder);
			$q3->bindParam("id", $this->id);
			$q3->execute();
			$q3->execute();
		}
	}

	public function moveDown(){
		if($_SESSION['user_type'] != 1) return;
		
		if($this->id) {		
			$curOrderQuery = $this->conn->prepare("SELECT `order` as o FROM ".$this->menuTable." WHERE id = :id");
			$this->id = htmlspecialchars(strip_tags($this->id));
			$curOrderQuery->bindParam("id", $this->id);
			$curOrderQuery->execute();
			$curOrderResult = $curOrderQuery->fetch(PDO::FETCH_ASSOC);
			$curOrder = ($curOrderResult['o'] * 1);

			$maxQuery = $this->conn->prepare("SELECT MAX(`order`) as max FROM ".$this->menuTable."");
			$maxQuery->execute();
			$maxResult = $maxQuery->fetch(PDO::FETCH_ASSOC);
			$max = ($maxResult['max'] * 1) + 1;

			if($curOrder >= $max-1) return; //Už jsem na konci

			$q1 = $this->conn->prepare("UPDATE ".$this->menuTable." SET `order` = null WHERE id = :id");
			$q1->bindParam("id", $this->id);
			$q1->execute();
			$q1->execute();

			$replacementOrder = $curOrder + 1;
			$q2 = $this->conn->prepare("UPDATE ".$this->menuTable." SET `order` = `order` - 1 WHERE `order` = :order");
			$q2->bindParam("order", $replacementOrder);
			$q2->execute();
			$q2->execute();

			$newOrder = ($curOrder+1);
			$q3 = $this->conn->prepare("UPDATE ".$this->menuTable." SET `order` = :order  WHERE id = :id");
			$q3->bindParam("order", $newOrder);
			$q3->bindParam("id", $this->id);
			$q3->execute();
			$q3->execute();
		}
	}










	
}
?>