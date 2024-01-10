<?php
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


include('../template/header.php');
?>
<title>Admin - <?= Config::WEBSITE_NAME ?></title>
<script src="js/jquery.dataTables.min.js"></script>
<script src="js/dataTables.bootstrap.min.js"></script>		
<link rel="stylesheet" href="css/dataTables.bootstrap.min.css" />
<script src="js/users.js"></script>	
<link href="css/style.css" rel="stylesheet" type="text/css" >  
</head>
<body>

<header id="header">
	<div class="container">
		<div class="row">
			<div class="col-md-10">
				<h1><span class="glyphicon glyphicon-cog" aria-hidden="true"></span> Users <small>management</small></h1>
			</div>
			<br>
			<?php include('./dropdown.php')?>
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
					<div class="panel-heading"style="background-color:  #095f59;">
						<h3 class="panel-title">Latest Users</h3>
					</div>
					<div class="panel-body">
						<div class="panel-heading">
							<div class="row">
								<div class="col-md-10">
									<h3 class="panel-title"></h3>
								</div>
								<div class="col-md-2" align="right">
									<a href="add_users.php" class="btn btn-default btn-xs">Add New</a>				
								</div>
							</div>
						</div>
						<table id="userList" class="table table-bordered table-striped">
							<thead>
								<tr>
									<th>Name</th>									
									<th>Nick</th>
									<th>Type</th>	
									<th>Status</th>																				
									<th></th>
									<th></th>	
								</tr>
							</thead>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>


 <?php include('../template/footer.php');?>
