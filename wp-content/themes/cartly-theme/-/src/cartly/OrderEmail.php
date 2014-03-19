<?php

require_once('Data.php');
require_once('Store.php');
require_once('Utilities.php');

// Testing
//$orderId = 18;

if (!empty($orderId))
{	
	$store = new Store();

	$order = $store->GetOrder($orderId);
	$orderItems = $store->GetOrderItems($orderId);
	$orderNumber = Utilities::GetOrderNumber($order->id);
}
else
{
	die('Order Not Found');
}

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD XHTML 1.0 Transitional //EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<title></title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta name="viewport" content="width=320, target-densitydpi=device-dpi">
<style type="text/css">
/* Mobile-specific Styles */
@media only screen and (max-width: 660px) { 
table[class=w0], td[class=w0] { width: 0 !important; }
table[class=w10], td[class=w10], img[class=w10] { width:10px !important; }
table[class=w15], td[class=w15], img[class=w15] { width:5px !important; }
table[class=w30], td[class=w30], img[class=w30] { width:10px !important; }
table[class=w60], td[class=w60], img[class=w60] { width:10px !important; }
table[class=w125], td[class=w125], img[class=w125] { width:80px !important; }
table[class=w130], td[class=w130], img[class=w130] { width:55px !important; }
table[class=w140], td[class=w140], img[class=w140] { width:90px !important; }
table[class=w160], td[class=w160], img[class=w160] { width:180px !important; }
table[class=w170], td[class=w170], img[class=w170] { width:100px !important; }
table[class=w180], td[class=w180], img[class=w180] { width:80px !important; }
table[class=w195], td[class=w195], img[class=w195] { width:80px !important; }
table[class=w220], td[class=w220], img[class=w220] { width:80px !important; }
table[class=w240], td[class=w240], img[class=w240] { width:180px !important; }
table[class=w255], td[class=w255], img[class=w255] { width:185px !important; }
table[class=w275], td[class=w275], img[class=w275] { width:135px !important; }
table[class=w280], td[class=w280], img[class=w280] { width:135px !important; }
table[class=w300], td[class=w300], img[class=w300] { width:140px !important; }
table[class=w325], td[class=w325], img[class=w325] { width:95px !important; }
table[class=w360], td[class=w360], img[class=w360] { width:140px !important; }
table[class=w410], td[class=w410], img[class=w410] { width:180px !important; }
table[class=w470], td[class=w470], img[class=w470] { width:200px !important; }
table[class=w580], td[class=w580], img[class=w580] { width:280px !important; }
table[class=w640], td[class=w640], img[class=w640] { width:300px !important; }
table[class*=hide], td[class*=hide], img[class*=hide], p[class*=hide], span[class*=hide] { display:none !important; }
table[class=h0], td[class=h0] { height: 0 !important; }
p[class=footer-content-left] { text-align: center !important; }
#headline p { font-size: 30px !important; }
.article-content, #left-sidebar{ -webkit-text-size-adjust: 90% !important; -ms-text-size-adjust: 90% !important; }
.header-content, .footer-content-left {-webkit-text-size-adjust: 80% !important; -ms-text-size-adjust: 80% !important;}
img { height: auto; line-height: 100%;}
 } 
/* Client-specific Styles */
#outlook a { padding: 0; }	/* Force Outlook to provide a "view in browser" button. */
body { width: 100% !important; }
.ReadMsgBody { width: 100%; }
.ExternalClass { width: 100%; display:block !important; } /* Force Hotmail to display emails at full width */
/* Reset Styles */
/* Add 100px so mobile switch bar doesn't cover street address. */
body { background-color: #e5e5e5; margin: 0; padding: 0; }
img { outline: none; text-decoration: none; display: block;}
br, strong br, b br, em br, i br { line-height:100%; }
h1, h2, h3, h4, h5, h6 { line-height: 100% !important; -webkit-font-smoothing: antialiased; }
h1 a, h2 a, h3 a, h4 a, h5 a, h6 a { color: blue !important; }
h1 a:active, h2 a:active,  h3 a:active, h4 a:active, h5 a:active, h6 a:active {	color: red !important; }
/* Preferably not the same color as the normal header link color.  There is limited support for psuedo classes in email clients, this was added just for good measure. */
h1 a:visited, h2 a:visited,  h3 a:visited, h4 a:visited, h5 a:visited, h6 a:visited { color: purple !important; }
/* Preferably not the same color as the normal header link color. There is limited support for psuedo classes in email clients, this was added just for good measure. */  
table td, table tr { border-collapse: collapse; }
.yshortcuts, .yshortcuts a, .yshortcuts a:link,.yshortcuts a:visited, .yshortcuts a:hover, .yshortcuts a span {
color: black; text-decoration: none !important; border-bottom: none !important; background: none !important;
}	/* Body text color for the New Yahoo.  This example sets the font of Yahoo's Shortcuts to black. */
/* This most probably won't work in all email clients. Don't include code blocks in email. */
code {
  white-space: normal;
  word-break: break-all;
}
#background-table { background-color: #e5e5e5; }
#background-table a:link,
#background-table a:visited {
	color: #4290b2 !important;
	text-decoration: none;
}
.product-table p {
	margin: 0 0 0 0;
	font-size: 16px;
}
.product-table td {
	padding: 5px 0;
}
/* Webkit Elements */
#top-bar { -webkit-font-smoothing: antialiased; background-color: #2E2E2E; color: #888888; }
#top-bar a { font-weight: bold; color: #eeeeee; text-decoration: none;}
#footer { -webkit-font-smoothing: antialiased; }
/* Fonts and Content */
body, td { font-family: 'Helvetica Neue', Arial, Helvetica, Geneva, sans-serif; }
.header-content, .footer-content-left, .footer-content-right { -webkit-text-size-adjust: none; -ms-text-size-adjust: none; }
/* Prevent Webkit and Windows Mobile platforms from changing default font sizes on header and footer. */
.header-content { font-size: 12px; color: #888888; }
.header-content a { font-weight: bold; color: #eeeeee; text-decoration: none; }
#headline p { color: #444444; font-family: 'Helvetica Neue', Arial, Helvetica, Geneva, sans-serif; font-size: 36px; margin-top:0px; margin-bottom:10px; }
#subheadline p { color: #444444; font-family: 'Helvetica Neue', Arial, Helvetica, Geneva, sans-serif; font-size: 22px; margin-top:0px; padding-bottom: 20px;margin-bottom:20px;
}
#headline p a { color: #eeeeee; text-decoration: none; }
.article-title { font-size: 18px; line-height:24px; color: #444444; font-weight:bold; margin-top:0px; margin-bottom:10px; font-family: 'Helvetica Neue', Arial, Helvetica, Geneva, sans-serif; }
.article-title-small { font-size: 18px; line-height:24px; color: #444444; font-weight:bold; margin-top:0px; margin-bottom:20px; font-family: 'Helvetica Neue', Arial, Helvetica, Geneva, sans-serif; }
.article-title a { color: #b0b0b0; text-decoration: none; }
.article-title.with-meta {margin-bottom: 0;}
.article-meta { font-size: 13px; line-height: 20px; color: #ccc; font-weight: bold; margin-top: 0;}
.article-content { font-size: 13px; line-height: 18px; color: #444444; margin-top: 0px; margin-bottom: 18px; font-family: 'Helvetica Neue', Arial, Helvetica, Geneva, sans-serif; }
.article-content a { color: #2f82de; font-weight:bold; text-decoration:none; }
.article-content img { max-width: 100% }
.article-content ol, .article-content ul { margin-top:0px; margin-bottom:18px; margin-left:19px; padding:0; }
.article-content li { font-size: 13px; line-height: 18px; color: #444444; }
.article-content li a { color: #2f82de; text-decoration:underline; }
.article-content p {margin-bottom: 15px;}
.footer-content-left { font-size: 12px; line-height: 15px; color: #888888; margin-top: 0px; margin-bottom: 15px; }
.footer-content-left a { color: #eeeeee; font-weight: bold; text-decoration: none; }
.footer-content-right { font-size: 11px; line-height: 16px; color: #888888; margin-top: 0px; margin-bottom: 15px; }
.footer-content-right a { color: #eeeeee; font-weight: bold; text-decoration: none; }
#footer { background-color: #ffffff; color: #666666; }
#footer a { font-weight: bold; }
#permission-reminder { white-space: normal; }
#street-address { color: #ffffff; white-space: normal; }
.product-table td.row-header {
	border-bottom: 1px solid #ddd;
}
.product-table td.row {
	padding: 10px 0;
	border-bottom: 1px solid #eee;
}
.product-table td.row p {
	margin-bottom: 5px;
}
.product-table td.row .meta {
	font-size: 14px;
	color: #999;
}
#shipping {
	font-size: 14px;
	margin: 0;
	line-height: 18px;
}
#shipping .article-title-small {
	font-size: 16px;
	margin-bottom: 10px;
}
#shipping p {
	margin: 0;
}
.totals-table {
	padding: 10px 10px 5px 10px;
	background: #eee;
}
.totals-table td {
	padding: 0px 5px 5px 5px;
}
</style>
<!--[if gte mso 9]>
<style _tmplitem="66" >
.article-content ol, .article-content ul {
   margin: 0 0 0 24px;
   padding: 0;
   list-style-position: inside;
}
</style>
<![endif]-->
</head>
<body>

<?php if (isset($order)) : ?>

<table width="100%" cellpadding="0" cellspacing="0" border="0" id="background-table">
	<tbody>
	<tr>
		<td align="center" bgcolor="#e5e5e5">
			<table class="w640" style="margin:0 10px;" width="640" cellpadding="0" cellspacing="0" border="0">
            <tbody>
            	<tr>
            		<td class="w640" width="640" height="30"></td>
				</tr>    
            	<tr>
                	<td class="w640" width="640"></td>
                </tr>
                <tr>
	                <td id="header" class="w640" width="640" align="center" bgcolor="#ffffff">
						<table class="w640" width="640" cellpadding="0" cellspacing="0" border="0">
						<tbody>
							<tr>
								<td class="w30" width="30"></td>
								<td class="w580" width="580" height="30"></td>
								<td class="w30" width="30"></td>
							</tr>
							<tr>
								<td class="w30" width="30"></td>
								<td class="w580" width="580">
									<div id="headline">
										<p>
											<strong><singleline label="Title"><?php echo get_bloginfo('name') ?></singleline></strong>
										</p>
									</div>
									<div id="subheadline">
										<p>
											<singleline label="Order Number">Order #<?php echo $orderNumber ?></singleline>
										</p>
									</div>
								</td>
								<td class="w30" width="30"></td>
							</tr>
						</tbody>
						</table>
					</td>
                </tr>
                <tr id="simple-content-row">
                	<td class="w640" width="640" bgcolor="#ffffff">
                		<table class="w640" width="640" cellpadding="0" cellspacing="0" border="0">
                		<tbody>
                			<tr>
                				<td class="w30" width="30"></td>
                				<td class="w580" width="580">
                					<repeater>
				                    <layout label="Product gallery">
				                        <table class="w580 product-table" width="580" cellpadding="0" cellspacing="0" border="0">
				                            <tbody>
				                            <tr>
				                                <td class="w180 row-header" width="75" valign="top">
				                                	<p align="left" class="article-title">
				                                    	<singleline label="Product Info">Product</singleline>
				                                    </p>
				                                </td>
				                                <td class="w180 row-header" width="150" valign="top">
				                                	<p align="left" class="article-title">
				                                    </p>
				                                </td>
				                                <td class="w180 row-header" width="120" valign="top">
				                                	<p align="left" class="article-title">
				                                    	<singleline label="Product Quantity">Quantity</singleline>
				                                    </p>
				                                </td>
				                                <td class="w180 row-header" width="180" valign="top">
				                                	<p align="left" class="article-title">
				                                    	<singleline label="Product Price">Price</singleline>
				                                    </p>
				                                </td>
				                            </tr>
				                            <?php foreach($orderItems as $orderItem) : ?>
				                            <?php
											$image = wp_get_attachment_image_src(get_post_thumbnail_id($orderItem->product_id), 'thumbnail');
											$image_info = get_post($orderItem->product_id);
											?>
				                            <tr>
				                                <td colspan="2" class="w180 row" width="150" valign="top">
				                                	<p align="left" class="">
														<singleline label="Product Info"><a href="<?php echo get_permalink($orderItem->product_id) ?>"><?php echo $image_info->post_title ?></a></singleline>
													</p>
													<p align="left" class="meta">
														<singleline label="Product Info"><?php echo $orderItem->option_value ?></singleline>
													</p>
				                                </td>
				                                <td class="w180 row" width="120" valign="top">
				                                	<p align="left" class="">
				                                    	<singleline label="Product Quantity"><?php echo $orderItem->quantity ?></singleline>
				                                    </p>
				                                </td>
				                                <td class="w180 row" width="180" valign="top">
				                                	<p align="left" class="">
				                                    	<singleline label="Product Price"><?php echo CartlyUtilities::PrintMoney($orderItem->total) ?></singleline>
				                                    </p>
				                                </td>
				                            </tr>
				                            <?php endforeach; ?>
											</tbody>
										</table>						
				                    </layout>
								</repeater>
							</td>
							<td class="w30" width="30"></td>
						</tr>
					</tbody>
				</table>
			</td>
		</tr>
		<tr>
			<td class="w640" width="640" height="30" bgcolor="#ffffff"></td>
		</tr>
		<tr>
			<td class="w640" width="640">
				<table id="shipping" class="w640" width="640" cellpadding="0" cellspacing="0" border="0" bgcolor="#ffffff">
					<tbody>
					<tr>
						<td class="w30" width="45"></td>
                		<td class="w180" width="180">
							<p align="left" class="article-title-small">Ship To:</p>
							<p><?php echo $order->shipping_name ?></p>
							<p><?php echo $order->address_1 ?></p>
							<p><?php echo $order->address_2 ?></p>
							<p><?php echo $order->city ?>, <?php echo $order->state_region ?></p>
							<p><?php echo $order->zip ?></p>
							<p><?php echo $order->country ?></p>
						</td>
						<td class="w180" width="290" valign="top">
							<p align="left" class="article-title-small">Total:</p>
							<table cellpadding="0" cellspacing="0" class="totals-table">
								<tr>
									<?php if (count($orderItems) == 1) : ?>
									<td class="accounting">Subtotal (1 Item):</td>
									<?php else: ?>
									<td class="accounting">Subtotal (<span class="item-count"><?php echo count($orderItems) ?></span> Items):</td>
									<?php endif; ?>
									<td class="subtotal"><?php echo CartlyUtilities::PrintMoney($order->subtotal) ?></td>
								</tr>
								<tr>
									<td class="accounting">Shipping:</td>
									<td><?php echo CartlyUtilities::PrintMoney($order->shipping) ?></td>
								</tr>
								<tr>
									<td class="accounting">Tax:</td>
									<td><?php echo CartlyUtilities::PrintMoney($order->tax) ?></td>
								</tr>
								<tr>
									<td class="accounting total"><strong>Grand Total:</strong></td>
									<td class="grand total"><strong><?php echo CartlyUtilities::PrintMoney($order->total) ?></strong></td>
								</tr>
							</table>
						</td>
						<td class="w30" width="30"></td>
					</tr>
					<tr>
						<td class="w30" width="30"></td>
						<td class="w580 h0" width="360" height="15"></td>
						<td class="w0" width="60"></td>
						<td class="w0" width="160"></td>
						<td class="w30" width="30"></td>
					</tr>
					</tbody>
				</table>
			</td>
		</tr>
		<tr>
			<td class="w640" width="640" height="30" bgcolor="#ffffff"></td>
		</tr>
		<tr>
			<td class="w640" width="640">
				<table id="footer" class="w640" width="640" cellpadding="0" cellspacing="0" border="0" bgcolor="#ffffff">
					<tbody>
					<tr>
						<td class="w30" width="30"></td>
						<td class="w580" width="360" valign="top">
							<p align="left" class="footer-content-left">
								<a href="<?php echo esc_url(home_url('/')); ?>"><?php echo get_bloginfo('name') ?></a>
							</p>
						</td>
						<td class="hide w0" width="60"></td>
						<td class="hide w0" width="160" valign="top">
							<p id="street-address" align="right" class="footer-content-right"></p>
						</td>
						<td class="w30" width="30"></td>
					</tr>
					<tr>
						<td class="w30" width="30"></td>
						<td class="w580 h0" width="360" height="15"></td>
						<td class="w0" width="60"></td>
						<td class="w0" width="160"></td>
						<td class="w30" width="30"></td>
					</tr>
					</tbody>
				</table>
			</td>
		</tr>
		<tr>
			<td class="w640" width="640" height="60"></td>
		</tr>
		<tr>
			<td class="w640" width="640" height="20" bgcolor="#e5e5e5"></td>
		</tr>
		</tbody>
		</table>
	</td>
</tr>
</tbody>
</table>

<?php else : ?>
<h1>Order Not Found</h1>
<?php endif; ?>

</body>
</html>