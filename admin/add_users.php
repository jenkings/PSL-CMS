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

$user->id = (isset($_GET['id']) && $_GET['id']) ? $_GET['id'] : '0';
$saveMessage = '';
if(!empty($_POST["saveUser"]) && $_POST["nick"]!='') {
	
	$user->first_name = $_POST["first_name"];
	$user->last_name = $_POST["last_name"];	
	$user->nick = $_POST["nick"];
	$user->type = $_POST["user_type"];	
	$user->deleted = $_POST["user_status"];		
	if($user->id) {	
		$user->updated = date('Y-m-d H:i:s');
		if($user->update()) {
			$saveMessage = "User updated successfully!";
		}
	} else {	
		$user->password = $_POST["password"];
		$lastInserId = $user->insert();
		if($lastInserId) {
			$user->id = $lastInserId;
			$saveMessage = "User saved successfully!";
		}
	}
}

$userDetails = $user->getUser();
 
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
				<h1><span class="glyphicon glyphicon-cog" aria-hidden="true"></span> User <small>editation</small></h1>
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
					<h3 class="panel-title">Add / Edit User</h3>
				  </div>
				  <div class="panel-body">
				  
					<form method="post" id="postForm">							
						<?php if ($saveMessage != '') { ?>
							<div id="login-alert" class="alert alert-success col-sm-12"><?php echo $saveMessage; ?></div>                            
						<?php } ?>
						<div class="form-group">
							<label for="title" class="control-label">First Name</label>
							<input type="text" class="form-control" id="first_name" name="first_name" value="<?php echo $userDetails['first_name']; ?>" placeholder="First name..">							
						</div>
						
						<div class="form-group">
							<label for="title" class="control-label">Last Name</label>
							<input type="text" class="form-control" id="last_name" name="last_name" value="<?php echo $userDetails['last_name']; ?>" placeholder="Last name..">							
						</div>
						
						<div class="form-group">
							<label for="title" class="control-label">nick</label>
							<input type="text" class="form-control" id="nick" name="nick" value="<?php echo $userDetails['nick']; ?>" placeholder="nick..">							
						</div>					
						<?php 
						if(!$user->id) {	
						?>
						<div class="form-group">
							<label for="title" class="control-label">Password</label>
							<input type="password" class="form-control" id="password" name="password" value="<?php echo $userDetails['password']; ?>" placeholder="password..">							
						</div>	
						<?php } ?>						
											
						<div class="form-group">
							<label for="status" class="control-label">User Type </label>							
							<label class="radio-inline">
								<input type="radio" name="user_type" id="admin" value="1" <?php if($userDetails['type'] == 1) { echo "checked";} ?>>Administrator
							</label>
							<label class="radio-inline">
								<input type="radio" name="user_type" id="author" value="2" <?php if($userDetails['type'] == 2) { echo "checked";} ?>>Author
							</label>												
						</div>		

						<div class="form-group">
							<label for="status" class="control-label">User Status </label>							
							<label class="radio-inline">
								<input type="radio" name="user_status" id="active" value="0" <?php if(!$userDetails['deleted']) { echo "checked";} ?>>Active
							</label>
							<label class="radio-inline">
								<input type="radio" name="user_status" id="inactive" value="1" <?php if($userDetails['deleted']) { echo "checked";} ?>>Inactive
							</label>												
						</div>	
						
						<input type="submit" name="saveUser" id="saveUser" class="btn btn-info" value="Save" />											
					</form>				
				  </div>
				</div>
			</div>
		</div>
	</div>
</section>
<?php include('../template/footer.php');?>
