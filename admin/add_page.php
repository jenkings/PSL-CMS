<?php
session_start();
include_once '../models/Database.php';
include_once '../models/User.php';
include_once '../models/Post.php';
include_once '../models/Category.php';
include_once '../models/Pages.php';
include_once '../Config.php';


$database = new Database();
$db = $database->getConnection();

$user = new User($db);
$post = new Post($db);
$category = new Category($db);

if(!$user->loggedIn()) {
	header("location: index.php");
}

$page = new Pages($db);

$page->id = (isset($_GET['id']) && $_GET['id']) ? $_GET['id'] : '0';

$saveMessage = '';
if(!empty($_POST["pageSave"]) && $_POST["pageTitle"]!='' && $_POST["pageLink"]!=''  && $_POST["pageContent"]!='') {
	
	$page->title = $_POST["pageTitle"];
	$page->link = $_POST["pageLink"];
	$page->content = $_POST["pageContent"];


	if($page->id) {			
		if($page->update()) {
			$saveMessage = "Page updated successfully!";
		}
	} else {			
		$lastInserId = $page->insert();
		if($lastInserId) {
			$page->id = $lastInserId;
			$saveMessage = "Page saved successfully!";
		}
	}
}

$pageyDetails = $page->getPage();
 
include('../template/header.php');
?>
<title>Admin - <?= Config::WEBSITE_NAME ?></title>
<script src="js/jquery.dataTables.min.js"></script>
<script src="js/dataTables.bootstrap.min.js"></script>		
<link rel="stylesheet" href="css/dataTables.bootstrap.min.css" />
<script src="js/posts.js"></script>	
<link href="css/style.css" rel="stylesheet" type="text/css" >  
</head>
<body>
<header id="header">
	<div class="container">
		<div class="row">
			<div class="col-md-10">
				<h1><span class="glyphicon glyphicon-cog" aria-hidden="true"></span> Page <small>editation</small></h1>
			</div>
			<br>			
		</div>
	</div>
</header>
<br>
<section id="main">
	<div class="container">
		<div class="row">	
			<?php include "left_menus.php"; ?>
			<div class="col-md-9">
				<div class="panel panel-default">
				  <div class="panel-heading">
					<h3 class="panel-title">Add / Edit page</h3>
				  </div>
				  <div class="panel-body">
				  
					<form method="post" id="postForm">							
						<?php if ($saveMessage != '') { ?>
							<div id="login-alert" class="alert alert-success col-sm-12"><?php echo $saveMessage; ?></div>                            
						<?php } ?>
						
						<div class="form-group">
							<label for="title" class="control-label">Page Title</label>
							<input type="text" class="form-control" id="pageTitle" name="pageTitle" value="<?php echo $pageyDetails['title']; ?>" placeholder="Page title..">							
						</div>

						<div class="form-group">
							<label for="title" class="control-label">Page link</label>
							<input type="text" class="form-control" id="pageLink" name="pageLink" value="<?php echo $pageyDetails['link']; ?>" placeholder="Page link">							
						</div>		

						<div class="form-group">
							<label for="title" class="control-label">Page content</label>
							<textarea class="form-control" id="pageContent" name="pageContent"><?php echo $pageyDetails['content']; ?> </textarea>
						</div>			
						
						<input type="submit" name="pageSave" id="pageSave" class="btn btn-info" value="Save" />											
					</form>				
				  </div>
				</div>
			</div>
		</div>
	</div>
</section>
<?php include('../template/footer.php');?>
