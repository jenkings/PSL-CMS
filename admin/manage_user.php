<?php
include_once '../models/Database.php';
include_once '../models/User.php';
include_once '../Config.php';


$database = new Database();
$db = $database->getConnection();

$user = new User($db);

if(!empty($_POST['action']) && $_POST['action'] == 'userListing') {
	$user->getUsersListing();
}
if(!empty($_POST['action']) && $_POST['action'] == 'userDelete') {
	$user->id = (isset($_POST['userId']) && $_POST['userId']) ? $_POST['userId'] : '0';
	$user->delete();
}
?>