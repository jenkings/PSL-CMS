<?php
class ImagesManager  {

	const FOLDER = "../public/images/";
	const MAX_FILE_SIZE = 1024 * 1024 * 4; // 4MB

	protected $message = [];
	protected $permitted_types = [
		'image/jpeg',
		'image/bmp',
		'image/png',
		'image/jpg',
		'image/gif'
	];
	private $file_name;
	private $file_type;
	private $is_validated;
	private $temp_name;
	private $new_name;
	private $is_name_changed;
	private $file_error;
	private $file_size;
	
	public function __construct ()  {
		$this->file_name = $_FILES['imageFile']['name'];
		$this->file_type = $_FILES['imageFile']['type'];
		$this->temp_name = $_FILES['imageFile']['tmp_name'];
		$this->file_error = $_FILES['imageFile']['error'];
		$this->file_size = $_FILES['imageFile']['size'];		
		$this->is_validated = false;
		$this->is_name_changed = false;
	}	

	public function validateImage ($newName = null)  {
		if ( strlen($this->file_name) > 255 || strlen($this->file_name) < 1 )  {
			$this->message[] .= "Change file name and upload again";
			return;
		}

		if($newName !== null){
			$this->file_name = $newName;
		}

		if ( file_exists(self::FOLDER . $this->file_name) )  {
			// do not reject file rather keep it.
			$fullpath = self::FOLDER . $this->file_name;
			$file_info = pathinfo($fullpath);
			$this->new_name = $file_info['imageFile'];
			$this->new_name .= "-". mt_rand(1000, 10000000);
			$this->new_name .= "." . $file_info['extension'];
			$this->is_name_changed = true;
		}

		if ( in_array($this->file_type, $this->permitted_types) )  {
			$this->is_validated = true;
		} 
		if ( $this->file_size > self::MAX_FILE_SIZE )  {
			$this->message[] .= "Images more than 1 MB are not allowed";
			$this->is_validated = false;
		}
	}
	public function uploadImage ()  {

		if ( $this->is_validated )  {
			if ( $this->is_name_changed )  {
				$result = move_uploaded_file($this->temp_name, self::FOLDER . $this->new_name);
				if ( $result )  {
					$this->message[] .= "Image uploaded";
				}
			}
			$is_moved = move_uploaded_file($this->temp_name, self::FOLDER . $this->file_name);
			if ( !$is_moved )  {
				$this->message[] .= "Can't move the file to folder";
			}
		}
		else  {
			$this->message[] .= "Not validated";
		}
	}

	public function getSavedIages(){
		$scan = scandir(self::FOLDER);

		$out = array();

		foreach ($scan as $file) {
		    $filePath = self::FOLDER . '/' . $file;
		    if (is_file($filePath)) {
		        $out[] = $file;
		    }
		}
		return $out;
	}

	public function delete($imageName){
		unlink(self::FOLDER . $imageName);
	}

	public function getMessage(){
		return $this->message[0];
	}

	public function getImagesListing(){		
		$result = $this->getSavedIages();
		$images = array();		
		foreach ($result as $file) { 				
			$rows = array();	
			$rows[0] = $file;
			$rows[1] = "<a href='".self::FOLDER . $file."' target='blank'><img src=\"" . self::FOLDER . $file . "\" style='max-height:70px;'></a>";
			$rows[2] = '<button type="button" name="delete" id="'.$file.'" class="btn btn-danger btn-xs delete" >Delete</button>';
			$images[] = $rows;
		}

		$output = array(	
			"iTotalRecords"	=> 	count($result),
			"iTotalDisplayRecords"	=>  count($result),
			"data"	=> 	$images
		);
		echo json_encode($output);	
	}


}
