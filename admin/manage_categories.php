<?php
include_once '../models/Database.php';
include_once '../models/Category.php';
include_once '../Config.php';


$database = new Database();
$db = $database->getConnection();

$category = new Category($db);

if(!empty($_POST['action']) && $_POST['action'] == 'categoryListing') {
	$category->getCategoryListing();
}
if(!empty($_POST['action']) && $_POST['action'] == 'categoryDelete') {
	$category->id = (isset($_POST['categoryId']) && $_POST['categoryId']) ? $_POST['categoryId'] : '0';
	$category->delete();
}
?>