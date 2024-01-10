<?php
include_once '../models/Database.php';
include_once '../models/Pages.php';
include_once '../Config.php';


$database = new Database();
$db = $database->getConnection();

$pages = new Pages($db);

if(!empty($_POST['action']) && $_POST['action'] == 'pagesListing') {
	$pages->getPagesListing();
}
if(!empty($_POST['action']) && $_POST['action'] == 'pageDelete') {
	$pages->id = (isset($_POST['pageId']) && $_POST['pageId']) ? $_POST['pageId'] : '0';
	$pages->delete();
}

if(!empty($_POST['action']) && $_POST['action'] == 'pageAddToMenu') {
	$pages->id = (isset($_POST['pageId']) && $_POST['pageId']) ? $_POST['pageId'] : '0';
	$pages->addToMenu();
}


?>