<div id="cart">
	<form name="cartform" action="<?php echo $action; ?>" method="post">
	<p>
		<!--PAYPAL VARS-->
		<input type="hidden" name="currency_code" value="GBP">
		<input type="hidden" name="upload" value="1">
		<input type="hidden" name="cmd" value="_cart">
		<input type="hidden" name="business" value="craig1709@googlemail.com">
		<input type="hidden" name="return" value="<?php echo 'http://' . $_SERVER['SERVER_NAME'] . '/cart/thankyou'; ?>">
	</p>
	<p><strong>Note:</strong> Size selections are made on the next page.</p>
	<table>
		<tr><th class="quantity">Quantity</th><th>Product</th><th class="price">Price</th></tr>
		<?php

		//test cart data
		//$_SESSION['products'] = array('squarebag' => 1, 'babyhat' => 1);

		$products = new Product_Model;
		
		$curr_products = (!empty($_COOKIE['products'])) ? unserialize($_COOKIE['products']) : array();
		
		$total = 0;
		$count = 0;
		foreach ($curr_products as $product => $quantity) {
			$count++;
			$product_data = $products->get_productinfo($product);
			echo "\t\t" . '<tr>
				<td class="quantity"><input name="quantity-' . $product . '" type="input" size="2" maxlength="2" value="' . $quantity . '"><a href="/products/remove/' . $product . '"><img src="/images/cross.png" title="Remove" alt="Remove"></a></td>';
				/*<td><select name="SIZE">';
				$sizes = explode(',', $product_data['sizes']);
				if (empty($product_data['sizes'])) {
					echo "<option value=\"na\">Single size</option>";
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
						echo "<option value=\"$size\">$size_name</option>";
					}
				}
			echo '</select></td>';*/
			echo '
				<td><input class="itemName" name="item_name_' . $count . '" type="text" readonly="readonly" value="' . $product_data['name'] . '"></td>
				<td class="price">&pound;<input class="itemName" name="amount_' . $count . '" readonly="readonly" type="text" value="' . $product_data['price']*$quantity . '"></td>
			</tr>' . "\r\n";
			$total += $product_data['price']*$quantity;
		}

		?>
		<tr><td class="quantity"></td><td></td><td class="price"><strong>Postage + Packaging: </strong>&pound;<input class="itemName" readonly="readonly" type="text" name="shipping" value="8.50"><?php $total += 3.95; ?></td></tr>
		<tr><td class="quantity"></td><td></td><td class="price"><strong>Total: </strong>&pound;<?php echo $total; ?></td></tr>
	</table>
	<p id="checkout"><?php echo $buttons; ?></p>
	</form>
</div>
