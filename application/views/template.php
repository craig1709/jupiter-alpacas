<?php session_start(); ?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
	<head>
		<title><?php echo $page_title; ?> - Jupiter Alpacas</title>
		<link rel="stylesheet" href="/css/main.php<?php if (isset($page)) { echo '?page=garments'; } ?>" type="text/css">
		<meta name="author" content="Craig &quot;craig1709&quot; Roberts">
		<script type="text/javascript" src="/js/jquery-1.2.6.js"></script>
		<script type="text/javascript" src="/js/aslimbox.js"></script>
		<script type="text/javascript" src="/js/thickbox.js"></script>
	</head>
	<body id="body-<?php echo $this->uri->segment(1); ?>">
	
		<div id="header">
		
			<h1><img src="/images/logo.png" alt=""><?php echo $header_1; ?></h1>
			
			<?php
				echo "<ul id=\"menu\">\r\n";
				foreach ($menu as $title => $link)
				{
					echo "\t\t\t\t<li><a href=\"$link\">$title</a></li>\r\n";
				}
				echo "\t\t\t</ul>\r\n";
			?>
			
		</div>
		
		<div id="wrapper">
			<div id="content">
			
				<h2><?php echo $header_2; ?></h2>
				<?php echo $page_content; ?>
				
			</div>
			
			
			<?php if ($show_sidebar == TRUE) { ?>
			<div id="sidebar">
				
				<h2><?php echo $header_sidebar; ?></h2>
				
				<?php echo $sidebar_content; ?>
				
			</div>
			<?php } ?>
		
			<p id="footer">&copy; 2008 Jupiter Alpacas</p>
		
		</div>
		
	</body>
</html>
