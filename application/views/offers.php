<div id="products">
	<?php
	
	$product_model = new Product_Model;
	
	$products = $product_model->get_all();

	$count = 0;
	foreach ($products as $productid => $product_data) {
	
		if ($product_data['offer'] == TRUE) {
			echo ($count % 6 == 0) ? '<div class="product_row">' : '';
			echo "\t\t" . '<div class="product_preview">
				<form action="/products/add/' . $productid . '" method="post">
				<a href="/products_data/product_details.php?id=' . $productid . '&amp;TB_iframe=true&amp;height=400&amp;width=600" class="thickbox">
				<img src="/images/timthumb.php?src=' . urlencode($product_data['image']) . '&amp;h=120" alt="">
				</a>
				<p>' .
				$product_data['name'] . '</p><p>&pound;' . $product_data['price'] . '</p>
				<p><a href="/products_data/product_details.php?id=' . $productid . '&amp;TB_iframe=true&amp;height=400&amp;width=600" class="thickbox">View Details</a></p>
				<p><input type="submit" value="Add to Basket"></p>
				</form>
				</div>';
			echo ($count % 6 == 0) ? '</div>' : '';
			$count++;
		}
	}

	?>
</div>