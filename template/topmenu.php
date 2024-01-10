		<div id="blog" class="row">
			<div class="header">
			<a href="./index.php" class="logo">
				<img src="<?= Config::ROOT_PATH?>resources/images/logo.png">
				<?= Config::WEBSITE_NAME ?>
			</a>
			<div id="slogan"> <?= Config::WEBSITE_SLOGAN ?> </div>
			<?php
				$menuItems = $menu->getMenuArray();
			?>
			<div class="header-right">
				<?php
					foreach($menuItems as $item){
						echo '<a href="' . $item['link'] . '">' . $item['title'] . '</a>';
					}
				?>
			</div>
		</div>	