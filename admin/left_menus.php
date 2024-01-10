<div class="col-md-3">
	<div class="list-group">
		<a href="dashboard.php" class="list-group-item active main-color-bg"><span class="glyphicon glyphicon-cog" aria-hidden="true"></span>
		Dashboard <span class="badge"><?php echo $post->totalPost()+$category->totalCategory()+$user->totalUser(); ?></span>
		</a>		
		<a href="posts.php" class="list-group-item"><span class="glyphicon glyphicon-pencil" aria-hidden="true"></span> Articles<span class="badge"><?php echo $post->totalPost(); ?></span></a>
		<a href="images.php" class="list-group-item"><span class="glyphicon glyphicon-picture" aria-hidden="true"></span> Images <span class="badge"></span></a>
		

		<?php if($_SESSION['user_type'] == 1){ ?>

		<a href="categories.php" class="list-group-item"><span class="glyphicon glyphicon-list-alt" aria-hidden="true"></span> Categories<span class="badge"><?php echo $category->totalCategory(); ?></span></a>
		<a href="users.php" class="list-group-item"><span class="glyphicon glyphicon-user" aria-hidden="true"></span> Users <span class="badge"><?php echo $user->totalUser(); ?></span></a>
		
		<a href="pages.php" class="list-group-item"><span class="glyphicon glyphicon-book" aria-hidden="true"></span> Pages <span class="badge"></span></a>
		<a href="menu.php" class="list-group-item"><span class="glyphicon glyphicon-menu-hamburger" aria-hidden="true"></span> Menu <span class="badge"></span></a>

		<?php } ?>
	</div>
</div>