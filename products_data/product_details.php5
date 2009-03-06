<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
	<head>
		<title><?php echo $page_title; ?> - Jupiter Alpacas</title>
		<link rel="stylesheet" href="/css/main.php5<?php if (isset($page)) { echo '?page=garments'; } ?>" type="text/css">
		<meta name="author" content="Craig &quot;craig1709&quot; Roberts">
	</head>
	<body id="product-details">
	
		<?php
			include('products.php');
			$id = $_GET['id'];
			$product = $products[$id];
		?>
	
		<h1><?php echo $product['name']; ?></h1>
		
		<div id="description">
			<h2>Available sizes	</h2>
			<ul id="sizes">
			<?php
				$sizes = explode(',', $product['sizes']);
				if (empty($product['sizes'])) {
					echo '<li>Single size</li>';
				} else {
					foreach ($sizes as $size) {
						switch (strtolower($size)) {
								case "s":
									$size_name = "Small";
								break;
								case "m":
									$size_name = "Medium";
								break;
								case "l":
									$size_name = "Large";
								break;
								default:
									$size_name = ucwords($size);
								break;
							}
						echo '<li>' . $size_name . '</li>';
					}
				}
			?>
			</ul>
			<p id="size-info"><em>Select size at checkout.</em></p>
			
			<h2>Product Description</h2>
			<?php echo $product['desc']; ?>
		</div>
		
		<img src="/images/timthumb.php5?src=<?php echo $product['image']; ?>&amp;w=160">
		
	</body>
</html>