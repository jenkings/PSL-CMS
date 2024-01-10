<?php
include_once '../models/Database.php';
include_once '../models/Post.php';
include_once '../Config.php';


$database = new Database();
$db = $database->getConnection();

$post = new Post($db);

if(!empty($_POST['action']) && $_POST['action'] == 'postListing') {
	$post->getPostsListing();
}
if(!empty($_POST['action']) && $_POST['action'] == 'postDelete') {
	$post->id = (isset($_POST['postId']) && $_POST['postId']) ? $_POST['postId'] : '0';
	$post->delete();
}
?>