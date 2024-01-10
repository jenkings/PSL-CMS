<?php
include_once '../models/Database.php';
include_once '../models/ImagesManager.php';
include_once '../Config.php';


$database = new Database();
$db = $database->getConnection();

$imagesManager = new ImagesManager();

if(!empty($_POST['action']) && $_POST['action'] == 'imageListing') {
	$imagesManager->getImagesListing();
}
if(!empty($_POST['action']) && $_POST['action'] == 'imageDelete') {
	if(isset($_POST['imageId']) && $_POST['imageId']){
		$imagesManager->delete($_POST['imageId']);
	}
}
?>