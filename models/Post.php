<?php
if (session_id() == "") session_start();
class Post {	
   
	private $postTable = 'articles';
	private $categoryTable = 'categories';
	private $userTable = 'users';	
	private $conn;
	
	public function __construct($db){
        $this->conn = $db;
    }	
	
	public function getPostsListing(){		
		
		$whereQuery = '';
		if($_SESSION['user_type'] == 2) {
			$whereQuery = "WHERE p.userid ='".$_SESSION['userid']."'";
		}	
		
		$sqlQuery = "
			SELECT p.id, p.title, p.category_id, u.first_name, u.last_name, p.status, p.created, p.updated, c.name 
			FROM ".$this->postTable." p
			LEFT JOIN ".$this->categoryTable." c ON c.id = p.category_id
			LEFT JOIN ".$this->userTable." u ON u.id = p.userid
			$whereQuery";
		
		if(!empty($_POST["search"]["value"])){
			$sqlQuery .= ' title LIKE "%'.$_POST["search"]["value"].'%" ';
			$sqlQuery .= ' OR message LIKE "%'.$_POST["search"]["value"].'%" ';
			$sqlQuery .= ' OR created LIKE "%'.$_POST["search"]["value"].'%" ';
			$sqlQuery .= ' OR updated LIKE "%'.$_POST["search"]["value"].'%" ';			
		}
		if(!empty($_POST["order"])){
			$sqlQuery .= 'ORDER BY '.$_POST['order']['0']['column'].' '.$_POST['order']['0']['dir'].' ';
		} else {
			$sqlQuery .= 'ORDER BY p.id DESC ';
		}
		if($_POST["length"] != -1){
			$sqlQuery .= 'LIMIT ' . $_POST['start'] . ', ' . $_POST['length'];
		}

		$stmt = $this->conn->prepare($sqlQuery);
		$stmt->execute();
		$result = $stmt->fetchAll();	
		
		$stmtTotal = $this->conn->prepare("SELECT COUNT(*) as count; FROM ".$this->postTable);
		$stmtTotal->execute();
		$allResult = $stmtTotal->fetch(PDO::FETCH_ASSOC);
		$allRecords = $allResult['count'];
		
		
		$displayRecords = count($result);
		$posts = array();		
		foreach ($result as $post) { 				
			$rows = array();	
			$status = '';
			if($post['status'] == 'published')	{
				$status = '<span class="label label-success">Published</span>';
			} else if($post['status'] == 'draft') {
				$status = '<span class="label label-warning">Draft</span>';
			} else if($post['status'] == 'archived') {
				$status = '<span class="label label-danger">Archived</span>';
			}
			
			$rows[] = ucfirst($post['title']);
			$rows[] = $post['name'];	
			$rows[] = ucfirst($post['first_name'])." ".$post['last_name'];	
			$rows[] = $status;				
			$rows[] = $post['created'];	
			$rows[] = $post['updated'];
			$rows[] = '<a href="compose_post.php?id='.$post["id"].'" class="btn btn-warning btn-xs update">Edit</a>';
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
	
	public function getPost(){		
		if($this->id) {
			$sqlQuery = "
				SELECT p.id, p.title, p.message, p.category_id, p.status, p.created, p.updated, c.name 
				FROM ".$this->postTable." p
				LEFT JOIN ".$this->categoryTable." c ON c.id = p.category_id
				WHERE p.id = :id ";
			$stmt = $this->conn->prepare($sqlQuery);
			$stmt->bindParam("id", $this->id);	
			$stmt->execute();
			$result = $stmt->fetch();
			$post = $result;
			return $post;
		}		
	}
	
	public function insert(){
		
		if($this->title && $this->message) {

			$stmt = $this->conn->prepare("
				INSERT INTO ".$this->postTable." (`title`, `message`, `category_id`, `userid`, `status`, `created` , `updated`) 
				VALUES(:t,:m,:c,:ui,:s,:cr,:u)");
		
			$this->title = htmlspecialchars(strip_tags($this->title));
			$this->message = htmlspecialchars(strip_tags($this->message));
			$this->category = htmlspecialchars(strip_tags($this->category));
			$this->userid = htmlspecialchars(strip_tags($this->userid));
			$this->status = htmlspecialchars(strip_tags($this->status));
			$this->created = htmlspecialchars(strip_tags($this->created));		
			$this->updated = htmlspecialchars(strip_tags($this->updated));
			
			$stmt->bindParam("t", $this->title);
			$stmt->bindParam("m", $this->message);
			$stmt->bindParam("c", $this->category);
			$stmt->bindParam("ui", $this->userid);
			$stmt->bindParam("s", $this->status);
			$stmt->bindParam("u", $this->updated);
			$stmt->bindParam("cr", $this->created);
			
			if($stmt->execute()){
				return $this->conn->lastInsertId();
			}		
		}
	}
	
	public function update(){
		
		if($this->id) {			
			$stmt = $this->conn->prepare("
				UPDATE ".$this->postTable." 
				SET title= :t, message = :m, category_id = :c, status= :s, updated = :u 
				WHERE id = :i;");
	 
			$this->id = htmlspecialchars(strip_tags($this->id));
			$this->title = htmlspecialchars(strip_tags($this->title));
			$this->message = htmlspecialchars(strip_tags($this->message));
			$this->category = htmlspecialchars(strip_tags($this->category));
			$this->status = htmlspecialchars(strip_tags($this->status));
			$this->updated = htmlspecialchars(strip_tags($this->updated));			
			
			$stmt->bindParam("t", $this->title);
			$stmt->bindParam("m", $this->message);
			$stmt->bindParam("c", $this->category);
			$stmt->bindParam("s", $this->status);
			$stmt->bindParam("u", $this->updated);
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
				DELETE FROM ".$this->postTable." 				
				WHERE id = :id;");
			$this->id = htmlspecialchars(strip_tags($this->id));
			$stmt->bindParam(":id", $this->id);
			if($stmt->execute()){
				return true;
			}
		}
	}
	
	public function getCategories(){		
		$sqlQuery = "
			SELECT id, name 
			FROM ".$this->categoryTable;
		$stmt = $this->conn->prepare($sqlQuery);
		$stmt->execute();
		$result = $stmt->fetchAll();			
		return $result;	
	}
	
	public function totalPost(){		
		$sqlQuery = "SELECT COUNT(*) as count FROM ".$this->postTable;			
		$stmt = $this->conn->prepare($sqlQuery);			
		$stmt->execute();
		$result = $stmt->fetch(PDO::FETCH_ASSOC);
		return $result['count'];	
	}	
}
?>