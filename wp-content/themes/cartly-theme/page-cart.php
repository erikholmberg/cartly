<?php
/*
TEMPLATE NAME: Cart
*/

// Check if products in cart are still available
if ($cartlyPluginActive)
{
	$itemRemoved = $store->CheckCartProducts($cart);
	$removeFromCart = get_option('cartly_remove_add_to_cart');
	$countries = $store->GetCountries();
}

?>

<?php get_header(); ?>
<?php if (have_posts()) while (have_posts()) : the_post(); ?>

<section class="page-content">

<section class="cart <?php echo $cart->itemCount == 0 ? 'empty' : 'full' ?> <?php echo !empty($removeFromCart) ? 'remove-add-to-cart' : ''; ?>">

	<div class="tame">
	
		<?php if ($cartlyPluginActive) : ?>
		
			<?php if ($itemRemoved) : ?>
			
			<div class="item-removed">
				<h2>An item that was in your cart is no longer available</h2>
				<p>We have removed it, but you can head to <a href="<?php echo get_bloginfo('url') ?>">the shop</a> and see if a similar item is available</p>
			</div>
			
			<?php endif; ?>
		
			<?php if ($cart->itemCount > 0) : ?>
			
			<form class="product-form">
			
			<div class="shopping-cart">
			
				<?php if ($cart->itemCount == 1) : ?>
				<h2>Your have <strong class="item-count">1</strong> <span class="item-label">item</span> in your cart</h2>
				<?php else : ?>
				<h2>Your have <strong class="item-count"><?php echo $cart->itemCount ?></strong> <span class="item-label">items</span> in your cart</h2>
				<?php endif; ?>
			
				<div class="row header clear">
					<div class="product-info"><label>Product</label></div>
					<div class="product-price"><label>Item Price</label></div>
					<div class="product-quantity"><label>Quantity</label></div>
					<div class="product-total-price"><label>Price</label></div>
					<div class="product-remove"><label class="mobile-hide">Remove</label></div>
				</div>
				<?php
				
				$taxes = 0.00;
				$shipping = 0.00;
				
				foreach($cart->products as $cartProduct) :
				
				$product = $store->GetProducts('publish', 'large', 'post_date', NULL, array($cartProduct->product_id));
				
				if (!empty($product->options))
				{
					foreach ($product->options as $option)
					{
						if ($option['id'] == $cartProduct->option_id)
						{
							$product->quantity = $option['quantity'];
							break;
						}
					}
				}
				
				$product->cart_quantity = $cartProduct->quantity;
				$product->cart_option = $cartProduct->option_id;
				$product->price = $store->GetProductPrice($product, $product->cart_option);
				
				$error = (!empty($removeFromCart) && $cartProduct->quantity > $product->quantity) ? 'error' : '';
				
				?>
				<div class="product row clear <?php echo $error; ?>" data-product-id="<?php echo $product->ID ?>" data-option-id="<?php echo $product->cart_option ?>" data-quantity-available="<?php echo $product->quantity; ?>">
					<div class="product-info clear">
						<div class="product-image">
							<a href="<?php echo get_permalink($product->ID) ?>">
								<?php if (!empty($product->featured_thumb)) : ?>				
								<img src="<?php echo $product->featured_thumb ?>" alt="<?php echo $product->post_title ?>" title="<?php echo $product->post_title ?>" />
								<?php endif; ?>
							</a>
						</div>
						<div class="product-meta">
							<h4><a href="<?php echo get_permalink($product->ID) ?>"><?php echo $product->post_title ?></a></h4>
							<?php if ($option = $store->GetProductOption($product, $product->cart_option)) : ?>
							<h5><?php echo $option ?></h5>
							<?php endif; ?>
							<?php if ($product->quantity > 0) : ?>
							<h6 class="error quantity-error">Only <span class="quantity"><?php echo $product->quantity; ?></span> left, please reduce quantity</h6>
							<?php else : ?>
							<h6 class="error quantity-error">None left, please remove from your cart</h6>
							<?php endif; ?>						
						</div>
					</div>
					<div class="product-price">
							<p><?php echo Utilities::PrintMoney($product->price) ?></p>
							<?php if (!empty($product->on_sale)) : ?>
							<p><strong class="alert">Sale!</strong></p>
							<?php endif; ?>
					</div>
					<div class="product-quantity">
						<input type="text" name="quantity" value="<?php echo $product->cart_quantity ?>" class="number" />
						<button class="update-cart" data-product-id="<?php echo $product->ID ?>">Update</button>
					</div>
					<div class="product-total-price" data-product-id="<?php echo $product->ID ?>">				
						<p><?php echo Utilities::PrintMoney($product->cart_quantity * $product->price) ?></p>
					</div>
					<div class="product-remove remove-from-cart">
						<p><a href="#" class="delete">Remove</a></p>
					</div>
				</div>
				<?php endforeach; ?>
				<div class="row footer clear">
					<table class="totals-table">
						<tr>
							<?php if ($cart->itemCount == 1) : ?>
							<td colspan="4" class="accounting">Subtotal (<span class="item-count">1</span> <span class="item-label">item</span>):</td>
							<?php else: ?>
							<td colspan="4" class="accounting">Subtotal (<span class="item-count"><?php echo $cart->itemCount ?></span> <span class="item-label">items</span>):</td>
							<?php endif; ?>
							<td class="subtotal"><?php echo Utilities::PrintMoney($cart->cartTotal) ?></td>
						</tr>
						<tr>
							<td colspan="4" class="accounting">Shipping:</td>
							<td class="shipping"><?php echo Utilities::PrintMoney($cart->cartShipping) ?></td>
						</tr>
						<tr>
							<td colspan="4" class="accounting">Tax:</td>
							<td class="tax"><?php echo Utilities::PrintMoney($cart->cartTax) ?></td>
						</tr>
						<tr>
							<td colspan="4" class="accounting total">Grand Total:</td>
							<td class="grand total"><?php echo Utilities::PrintMoney($cart->cartTotal + $cart->cartShipping) ?></td>
						</tr>
					</table>
				</div>
			</div>
			
			</form>
			
			<?php endif; ?>

		<?php endif; ?>
		
		<div class="no-items <?php echo $cart->itemCount > 0 ? 'initial-hide' :'' ?>">
			<h2>Your have no items in your cart</h2>
			<p>Head to <a href="<?php echo get_bloginfo('url') ?>">the shop</a> and treat yourself!</p>
		</div>
		
		<div class="order-placed-user initial-hide">
			<h2>Your order has been received</h2>
			<p>You can expect to receive your goods shortly. If you want to see the details of your order, head to your <a href="<?php echo get_permalink($accountPage->ID) ?>">account</a> page.</p>
		</div>
		
		<div class="order-placed-guest initial-hide">
			<h2>Your order has been received</h2>
			<p>You can expect to receive your goods shortly.</p>
		</div>
		
	</div>
</section>

<?php if ($cart->itemCount > 0) : ?>

<section class="checkout">
	<form class="checkout-form">
	<div class="tame">
		
		<h2>Checkout</h2>
		
		<?php if (!$account->IsAuthorized()) : ?>
		<p>If you have an account you can <a href="<?php echo get_permalink($loginPage->ID) ?>?redirect=<?php echo urlencode(get_permalink($post->ID)) ?>">log in</a> to save this order to your history.</p>
		<?php endif; ?>
		
		<div class="checkout-steps clear">
			<div class="left">
				<h3>Step 1: Shipping</h3>
				
				<h4>Shipping Address</h4>
				
				<div class="shipping-address">
				
					<table class="shipping">
						<tr>
							<td class="label">
								<label for="shipping[full_name]">Full Name</label>
							</td>
							<td class="input">
								<input type="text" name="shipping[full_name]" maxlength="50" />
							</td>
						</tr>
						
						<?php if (!$account->IsAuthorized()) : ?>
						
						<tr>
							<td class="label">
								<label for="shipping[email]">Email</label>
							</td>
							<td class="input">
								<input type="text" name="shipping[email]" maxlength="128" />
							</td>
						</tr>
						
						<?php endif; ?>
						
						<tr>
							<td class="label">
								<label for="shipping[address_1]">Address 1</label>
							</td>
							<td class="input">
								<input type="text" name="shipping[address_1]" maxlength="60" />
							</td>
						</tr>
						<tr>
							<td class="label">
								<label for="shipping[address_2]">Address 2</label>
							</td>
							<td class="input">
								<input type="text" name="shipping[address_2]" maxlength="60" />
							</td>
						</tr>
						<tr>
							<td class="label">
								<label for="shipping[city]">City</label>
							</td>
							<td class="input">
								<input type="text" name="shipping[city]" maxlength="50" />
							</td>
						</tr>
						<tr>
							<td class="label">
								<label for="shipping[state_region]">State / Region</label>
							</td>
							<td class="input">
								<input type="text" name="shipping[state_region]" maxlength="50" />
							</td>
						</tr>
						<tr>
							<td class="label">
								<label for="shipping[zip]">Zip</label>
							</td>
							<td class="input">
								<input type="text" name="shipping[zip]" maxlength="20" />
							</td>
						</tr>
						<tr>
							<td class="label">
								<label for="shipping[country_id]">Country</label>
							</td>
							<td class="input">
								<select name="shipping[country_id]">
									<?php foreach($countries as $country) : ?>
									<option value="<?php echo $country->code ?>"><?php echo $country->name ?></option>
									<?php endforeach; ?>
								</select>
							</td>
						</tr>
						<tr>
							<td class="label">
								<label for="shipping[phone]">Phone</label>
							</td>
							<td class="input">
								<input type="text" name="shipping[phone]" maxlength="20" />
							</td>
						</tr>
						<tr>
							<td class="label"></td>
							<td class="input same">
								<label for="same_address" class="checkbox"><input type="checkbox" name="same_address" checked /> Shipping and Billing address are the same </label>
							</td>
						</tr>
					</table>
					
				</div>
				
				<div class="billing-address initial-hide">
				
					<h4>Billing Address</h4>
					
					<table class="billing">
						<tr>
							<td class="label">
								<label for="billing[full_name]">Full Name</label>
							</td>
							<td class="input">
								<input type="text" name="billing[full_name]" maxlength="50" />
							</td>
						</tr>
						<tr>
							<td class="label">
								<label for="billing[address_1]">Address 1</label>
							</td>
							<td class="input">
								<input type="text" name="billing[address_1]" maxlength="60" />
							</td>
						</tr>
						<tr>
							<td class="label">
								<label for="billing[address_2]">Address 2</label>
							</td>
							<td class="input">
								<input type="text" name="billing[address_2]" maxlength="60" />
							</td>
						</tr>
						<tr>
							<td class="label">
								<label for="billing[city]">City</label>
							</td>
							<td class="input">
								<input type="text" name="billing[city]" maxlength="50" />
							</td>
						</tr>
						<tr>
							<td class="label">
								<label for="billing[state_region]">State / Region</label>
							</td>
							<td class="input">
								<input type="text" name="billing[state_region]" maxlength="50" />
							</td>
						</tr>
						<tr>
							<td class="label">
								<label for="billing[zip]">Zip</label>
							</td>
							<td class="input">
								<input type="text" name="billing[zip]" maxlength="20" />
							</td>
						</tr>
						<tr>
							<td class="label">
								<label for="billing[country_id]">Country</label>
							</td>
							<td class="input">
								<select name="billing[country_id]">
									<?php foreach($countries as $country) : ?>
									<option value="<?php echo $country->code ?>"><?php echo $country->name ?></option>
									<?php endforeach; ?>
								</select>
							</td>
						</tr>
						<tr>
							<td class="label">
								<label for="billing[phone]">Phone</label>
							</td>
							<td class="input">
								<input type="text" name="billing[phone]" maxlength="20" />
							</td>
						</tr>
					</table>
				
				</div>
				
			</div>
			<div class="middle">
				<h3>Step 2: Billing</h3>
				<h4>Credit Card Information</h4>
				
				<div class="credit-card">
				
					<table class="credit">
						<tr>
							<td class="label">
								<label for="credit[number]">Card Number</label>
							</td>
							<td class="input">
								<input type="text" name="credit[number]" maxlength="20" />
							</td>
						</tr>
						<tr>
							<td class="label">
								<label for="expires">Expires on</label>
							</td>
							<td class="input">
								<select name="credit[exp-month]">
									<option value="1">1 - Jan</option>
									<option value="2">2 - Feb</option>
									<option value="3">3 - Mar</option>
									<option value="4">4 - Apr</option>
									<option value="5">5 - May</option>
									<option value="6">6 - Jun</option>
									<option value="7">7 - Jul</option>
									<option value="8">8 - Aug</option>
									<option value="9">9 - Sep</option>
									<option value="10">10 - Oct</option>
									<option value="11">11 - Nov</option>
									<option value="12">12 - Dec</option>
								</select>
								<select name="credit[exp-year]">
									<?php for ($index = 0; $index < 15; $index++) { $year = date('Y') + $index; ?>
									<option value="<?php echo $year ?>"><?php echo $year ?></option>
									<?php } ?>
								</select>
							</td>
						</tr>
						<tr>
							<td class="label">
								<label for="credit[cvc]">CVC</label>
							</td>
							<td class="input">
								<input type="text" name="credit[cvc]" maxlength="4" />
							</td>
						</tr>
					</table>
				
				</div>
				
			</div>
			<div class="right">
			
				<div class="place-order">
				
					<h3>Step 3: Place Order</h3>
					<h4>Confirmation</h4>
					
					<p>Thank you for your business. Please check your address to make sure you get your products on time.</p>
					
					<?php if (!$account->IsAuthorized()) : ?>
					
					<div class="account clear">
						<label for="create_account" class="checkbox">
						<input type="checkbox" name="create_account" checked />Create an account for saving carts across devices + viewing all your orders</label>
					</div>
					
					<div class="create-account">
						<table class="account-details">
							<tr>
								<td class="label">
									<label for="account[password]">Password</label>
								</td>
								<td class="input">
									<input type="password" name="account[password]" maxlength="32" />
								</td>
							</tr>
						</table>
					</div>
					<?php endif; ?>
					
					<div class="order-button">
						<input type="submit" class="button big" value="Place Your Order" />
					</div>
					
					<img src="<?php echo get_template_directory_uri(); ?>/-/img/loading.gif" class="loading" alt="" title="" />
					<p class="order-error error"></p>
					<p class="order-success success"></p>
					
				</div>
				
			</div>
		</div>
	</div>
	</form>
	
</section>

</section>

<?php endif; ?>

<?php endwhile; ?>
<?php get_footer(); ?>
