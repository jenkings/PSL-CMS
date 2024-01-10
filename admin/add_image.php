<?php
session_start();
include_once '../models/Database.php';
include_once '../models/User.php';
include_once '../models/Post.php';
include_once '../models/Category.php';
include_once '../models/ImagesManager.php';
include_once '../Config.php';


$database = new Database();
$db = $database->getConnection();

$user = new User($db);
$post = new Post($db);
$category = new Category($db);

if(!$user->loggedIn()) {
	header("location: index.php");
}




$saveMessage = '';
if(!empty($_POST["imageSave"])) {
	
	$imagesManager = new ImagesManager();
	if(empty($_POST["imageName"])){
		$imagesManager->validateImage();
	}else{
		$imagesManager->validateImage($_POST["imageName"]);
	}
	$imagesManager->uploadImage();	

	$saveMessage = $imagesManager->getMessage();


}


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
				<h1><span class="glyphicon glyphicon-cog" aria-hidden="true"></span> Image <small>upload</small></h1>
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
					<h3 class="panel-title">Upload Image</h3>
				  </div>
				  <div class="panel-body">
				  
					<form method="post" id="postForm" enctype="multipart/form-data">							
						<?php if ($saveMessage != '') { ?>
							<div id="login-alert" class="alert alert-success col-sm-12"><?php echo $saveMessage; ?></div>                            
						<?php } ?>


						<div class="form-group">
							<label for="imageFile" class="control-label">Image file</label>
							<input class="form-control"  class="form-control" id="imageFile" name="imageFile" type="file" />
						</div>		
						
						<div class="form-group">
							<label for="imageName" class="control-label">New image name</label>
							<input type="text" class="form-control" id="imageName" name="imageName" placeholder="new name, if wanted">			
						</div>		
	
						
						<input type="submit" name="imageSave" id="imageSave" class="btn btn-info" value="Save" />											
					</form>				
				  </div>
				</div>
			</div>
		</div>
	</div>
</section>
<?php include('../template/footer.php');?>
