<?php
class Articles {	
   
	private $postTable = 'articles';
	private $categoryTable = 'categories';
	private $userTable = 'users';
	private $conn;
	
	public function __construct($db){
        $this->conn = $db;
    }	    
	
	public function getArticles($cnt = null,$offset = null){
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

		if(is_int($cnt)){
			$sqlQuery .= " LIMIT " . $cnt;
		}
		if(is_int($offset)){
			$sqlQuery .= " OFFSET " . $offset;
		}
			
		$stmt = $this->conn->prepare($sqlQuery);		
		$stmt->execute();
		$result = $stmt->fetchAll();
		return $result;
	}
	

	public function getPageArticles($page = 1){
		if(!is_int($page)){
			$page = 1;
		}
		return $this->getArticles(Config::ARTICLES_PER_PAGE,(($page - 1) * Config::ARTICLES_PER_PAGE) );
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
		$sqlQuery = "SELECT COUNT(*) as count FROM ".$this->postTable;			
		$stmt = $this->conn->prepare($sqlQuery);			
		$stmt->execute();
		$result = $stmt->fetch(PDO::FETCH_ASSOC);
		return $result['count'];	
	}

	public function getPagesCount(){
		return ceil($this->totalPost() / Config::ARTICLES_PER_PAGE);
	}

}
?>