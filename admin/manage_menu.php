<?php
include_once '../models/Database.php';
include_once '../models/Menu.php';
include_once '../Config.php';


$database = new Database();
$db = $database->getConnection();

$menu = new Menu($db);

if(!empty($_POST['action']) && $_POST['action'] == 'menuListing') {
	$menu->getMenuListing();
}
if(!empty($_POST['action']) && $_POST['action'] == 'menuDelete') {
	$menu->id = (isset($_POST['menuId']) && $_POST['menuId']) ? $_POST['menuId'] : '0';
	$menu->delete();
}


if(!empty($_POST['action']) && $_POST['action'] == 'menuMoveUp') {
	$menu->id = (isset($_POST['menuId']) && $_POST['menuId']) ? $_POST['menuId'] : '0';
	$menu->moveUp();
}


if(!empty($_POST['action']) && $_POST['action'] == 'menuMoveDown') {
	$menu->id = (isset($_POST['menuId']) && $_POST['menuId']) ? $_POST['menuId'] : '0';
	$menu->moveDown();
}
?>