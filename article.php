<?php 
include_once 'models/Database.php';
include_once 'models/Articles.php';
include_once 'models/Menu.php';
include_once './Config.php';

$database = new Database();
$db = $database->getConnection();

$article = new Articles($db);
$menu = new Menu($db);

$article->id = (isset($_GET['id']) && $_GET['id']) ? $_GET['id'] : '0';

$result = $article->getArticles();

include('template/header.php');

?>
<title><?= Config::WEBSITE_NAME ?></title>
<link href="css/style.css" rel="stylesheet" id="bootstrap-css">
<?php include('template/container.php');?>
<div class="container">		

		<?php include('template/topmenu.php');?>
	
		<?php 
		foreach($result as $post) {
			$date = date_create($post['created']);
			$message = str_replace("\n\r", "<br><br>", $post['message']);
		?>
			<div class="col-md-10 blogShort">
			<h2><?php echo $post['title']; ?></h2>
			<em><strong>Published on</strong>: <?php echo date_format($date, "d F Y");	?></em>
			<em><strong>Category:</strong> <a href="#" target="_blank"><?php echo $post['category']; ?></a></em>
			<br><br>
			<article>
				<p><?php echo $message; ?> 	</p>
			</article>		
			</div>
		<?php } ?>   
		
		<div class="col-md-12 gap10"></div>
		

</div>
<?php include('template/footer.php');?>