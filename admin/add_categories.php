<?php
session_start();
include_once '../models/Database.php';
include_once '../models/User.php';
include_once '../models/Post.php';
include_once '../models/Category.php';
include_once '../Config.php';


$database = new Database();
$db = $database->getConnection();

$user = new User($db);
$post = new Post($db);
$category = new Category($db);

if(!$user->loggedIn()) {
	header("location: index.php");
}

$category = new Category($db);

$category->id = (isset($_GET['id']) && $_GET['id']) ? $_GET['id'] : '0';

$saveMessage = '';
if(!empty($_POST["categorySave"]) && $_POST["categoryName"]!='') {
	
	$category->name = $_POST["categoryName"];	
	if($category->id) {			
		if($category->update()) {
			$saveMessage = "Category updated successfully!";
		}
	} else {			
		$lastInserId = $category->insert();
		if($lastInserId) {
			$category->id = $lastInserId;
			$saveMessage = "Category saved successfully!";
		}
	}
}

$categoryDetails = $category->getCategory();
 
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
				<h1><span class="glyphicon glyphicon-cog" aria-hidden="true"></span> Category <small>editation</small></h1>
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
					<h3 class="panel-title">Add / Edit Category</h3>
				  </div>
				  <div class="panel-body">
				  
					<form method="post" id="postForm">							
						<?php if ($saveMessage != '') { ?>
							<div id="login-alert" class="alert alert-success col-sm-12"><?php echo $saveMessage; ?></div>                            
						<?php } ?>
						
						<div class="form-group">
							<label for="title" class="control-label">Category Name</label>
							<input type="text" class="form-control" id="categoryName" name="categoryName" value="<?php echo $categoryDetails['name']; ?>" placeholder="Category name..">							
						</div>				
						
						<input type="submit" name="categorySave" id="categorySave" class="btn btn-info" value="Save" />											
					</form>				
				  </div>
				</div>
			</div>
		</div>
	</div>
</section>
<?php include('../template/footer.php');?>
