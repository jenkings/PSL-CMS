<?php
class Articles {	
   
	private $postTable = 'articles';
	private $categoryTable = 'categories';
	private $userTable = 'users';
	private $conn;
	
	public function __construct($db){
        $this->conn = $db;
    }	    
	
	public function getArticles(){
		$query = '';
		if($this->id) {		
			$query = " AND p.id ='".$this->id."'";
		}
		$sqlQuery = "
			SELECT p.id, p.title, p.message, p.category_id, u.first_name, u.last_name, p.status, p.created, p.updated, c.name as category
			FROM ".$this->postTable." p
			LEFT JOIN ".$this->categoryTable." c ON c.id = p.category_id
			LEFT JOIN ".$this->userTable." u ON u.id = p.userid
			WHERE p.status ='published' $query ORDER BY p.id DESC";
			
		$stmt = $this->conn->prepare($sqlQuery);		
		$stmt->execute();
		$result = $stmt->fetchAll();	
		return $result;
	}
	
	function formatMessage($string, $wordsreturned) {
		$retval = $string;  //  Just in case of a problem
		$array = explode(" ", $string);
		if (count($array)<=$wordsreturned){
			$retval = $string;
		}else{
			array_splice($array, $wordsreturned);
			$retval = implode(" ", $array)." ...";
		}
		return $retval;
	}
	
	public function totalPost(){		
		$sqlQuery = "SELECT * FROM ".$this->postTable;			
		$stmt = $this->conn->prepare($sqlQuery);			
		$stmt->execute();
		$result = $stmt->fetchAll();
		return $result->num_rows;	
	}	
}
?>