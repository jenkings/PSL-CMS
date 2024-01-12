<?php
if (session_id() == "") session_start();
class Pages {	
   
	private $pageTable = 'pages';
	private $menuTable = 'menu';
	private $conn;
	
	public function __construct($db){
        $this->conn = $db;
    }	
	
	public function getPagesListing(){		
		if($_SESSION['user_type'] != 1) return;
		
		$sqlQuery = "
			SELECT id,link,title,content 
			FROM ".$this->pageTable. " ";
		
		if(!empty($_POST["search"]["value"])){
			$sqlQuery .= ' title LIKE "%'.$_POST["search"]["value"].'%" ';
			$sqlQuery .= ' OR link LIKE "%'.$_POST["search"]["value"].'%" ';
			$sqlQuery .= ' OR content LIKE "%'.$_POST["search"]["value"].'%" ';	
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
		
		$stmtTotal = $this->conn->prepare("SELECT COUNT(*) as count; FROM ".$this->pageTable);
		$stmtTotal->execute();
		$allResult = $stmtTotal->fetch(PDO::FETCH_ASSOC);
		$allRecords = $allResult['count'];
		
		
		$displayRecords = count($result);
		$posts = array();		
		foreach ($result as $post) { 				
			$rows = array();	
			
			$rows[] = $post['link'];			
			$rows[] = $post['title'];	
			$rows[] = htmlspecialchars($post['content']);
			$rows[] = '<button type="button" name="add" id="'.$post["id"].'" class="btn btn-success btn-xs add" >Add to menu</button>';
			$rows[] = '<a href="add_page.php?id='.$post["id"].'" class="btn btn-warning btn-xs update">Edit</a>';
			$rows[] = '<button type="button" name="delete" id="'.$post["id"].'" class="btn btn-danger btn-xs delete" >Delete</button>';
			$posts[] = $rows;
		}
		
		$output = array(
			"draw"	=>	intval($_POST["draw"]),			
			"iTotalRecords"	=> 	$displayRecords,
			"iTotalDisplayRecords"	=>  $allRecords,
			"data"	=> 	$posts
		);
		
		echo json_encode($output);	
	}
	
	public function getPage(){		
		if($this->id) {
			$sqlQuery = "
				SELECT id,link,title,content 
				FROM ".$this->pageTable." 
				WHERE id = :id ";
			$stmt = $this->conn->prepare($sqlQuery);
			$stmt->bindParam("id", $this->id);	
			$stmt->execute();
			$result = $stmt->fetch();
			$post = $result;
			return $post;
		}		
	}

	public function getPageByLink(){		
		if($this->link) {
			$sqlQuery = "
				SELECT id,link,title,content 
				FROM ".$this->pageTable." 
				WHERE link = :link ";
			$stmt = $this->conn->prepare($sqlQuery);
			$stmt->bindParam("link", $this->link);	
			$stmt->execute();
			$result = $stmt->fetch();
			$post = $result;
			return $post;
		}		
	}
	
	public function insert(){
		if($_SESSION['user_type'] != 1) return;

		if($this->link && $this->title && $this->content) {

			$stmt = $this->conn->prepare("
				INSERT INTO ".$this->pageTable."(`title`, `link`, `content`)
				VALUES(:t, :l, :c)");
		
			$this->title = htmlspecialchars(strip_tags($this->title));
			$this->link = htmlspecialchars(strip_tags($this->link));

			$stmt->bindParam("t", $this->title);
			$stmt->bindParam("l", $this->link);
			$stmt->bindParam("c", $this->content);

			
			if($stmt->execute()){
				return $this->conn->lastInsertId();
			}		
		}
	}
	
	public function update(){
		if($_SESSION['user_type'] != 2) return;

		if($this->id) {			
			$stmt = $this->conn->prepare("
				UPDATE ".$this->pageTable." 
				SET `link` = :link, `title` = :title, `content` = :cntnt 
				WHERE id = :id");

			$this->link = htmlspecialchars(strip_tags($this->link));
			$this->title = htmlspecialchars(strip_tags($this->title));
			//$this->content = htmlspecialchars(strip_tags($this->content));
			$this->id = htmlspecialchars(strip_tags($this->id));
			
			$stmt->bindParam("link", $this->link);
			$stmt->bindParam("title", $this->title);
			$stmt->bindParam("cntnt",$this->content);
			$stmt->bindParam("id", $this->id);

			if($stmt->execute()){
				return true;
			}			
		}
		
	}
	
	public function delete(){
		if($_SESSION['user_type'] != 1) return;

		if($this->id) {	
			$stmt = $this->conn->prepare("
				DELETE FROM ".$this->pageTable." 				
				WHERE id = :id;");
			$this->id = htmlspecialchars(strip_tags($this->id));
			$stmt->bindParam(":id", $this->id);
			if($stmt->execute()){
				return true;
			}
		}
	}
	
	public function getPosts(){		
		$sqlQuery = "
			SELECT id, title, link, content 
			FROM ".$this->pageTable;
		$stmt = $this->conn->prepare($sqlQuery);
		$stmt->execute();
		$result = $stmt->fetchAll();			
		return $result;	
	}


	public function addToMenu(){
		if($_SESSION['user_type'] != 1) return;

		if($this->id) {	
			$maxQuery = $this->conn->prepare("SELECT MAX(`order`) as max FROM ".$this->menuTable."");
			$maxQuery->execute();
			$maxResult = $maxQuery->fetch(PDO::FETCH_ASSOC);
			$max = ($maxResult['max'] * 1) + 1;

			$dataQuery = $this->conn->prepare("SELECT title,link FROM ".$this->pageTable."");
			$dataQuery->execute();
			$dataResult = $dataQuery->fetch(PDO::FETCH_ASSOC);
			

			$stmt = $this->conn->prepare("
				INSERT INTO ".$this->menuTable."(`title`,`link`,`order`)
				VALUES(:s1,:s2,:s3)");
		
			$this->title = htmlspecialchars(strip_tags($dataResult['title']));
			$this->link = "./page.php?link=" . $dataResult['link'];
			
			$stmt->bindParam("s1", $this->title);
			$stmt->bindParam("s2", $this->link);
			$stmt->bindParam("s3", $max);
			
			if($stmt->execute()){
				return $this->conn->lastInsertId();
			}		
		}
	}


}
?>
