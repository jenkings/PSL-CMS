<?php 
include_once 'models/Database.php';
include_once 'models/Pages.php';
include_once 'models/Menu.php';
include_once 'models/Markdown.php';
include_once './Config.php';

$database = new Database();
$db = $database->getConnection();

$page = new Pages($db);
$menu = new Menu($db);
$markdownParser = new Markdown();

$page->link = $_GET['link'];

$post = $page->getPageByLink();

include('template/header.php');

?>
<title><?php echo $post['title']; ?> - <?= Config::WEBSITE_NAME ?></title>
<link href="css/style.css" rel="stylesheet" id="bootstrap-css">
<?php include('template/container.php');?>
<div class="container">		

		<?php include('template/topmenu.php');?>
	
		<?php 
			$content = str_replace("\n\r", "<br><br>", $post['content']);
		?>

			<div class="col-md-10 blogShort">
			<h2><?php echo $post['title']; ?></h2>
			<br><br>
			<article>
				<p><?php echo $markdownParser->convertToHtml($content); ?> 	</p>
			</article>		
			</div>
		
		<div class="col-md-12 gap10"></div>
		

</div>
<?php include('template/footer.php');?>
