<?php

require_once('Cookie.php');
require_once('Store.php');

class Cart
{
	public $productCount;
	public $itemCount;
	public $products;
	public $cartTotal;
	public $cartTax;
	public $cartShipping;
	
	private $cartCookie;
	private $cartCookieName;
	private $store;
	
	// Constructor
	function __construct()
	{
		$this->store = new Store();
	    $this->cartCookie = new EncryptedCookie();
		$this->cartCookieName = 'cartly_cart';
		$this->products = $this->GetCart();
		$this->cartTotal = $this->GetCartTotal($this->products, $this->cartShipping, $this->cartTax);
		
		$this->Update($this->products);
	}
	
	// Public Methods
	public function GetCart()
	{
		$cookie = $this->cartCookie->GetCookie($this->cartCookieName);
		
		if ($cookie)
		{
			return (array)json_decode($cookie);
		}
		else
		{
			return array();
		}
	}
	
	public function AddToCart($productId, $optionId, $quantity)
	{	
		$alreadyInCart = false;
		$products = $this->GetCart();
		
		foreach($products as $product)
		{
			if ($product->product_id == $productId && $product->option_id == $optionId)
			{
				$product->quantity += $quantity;
				$alreadyInCart = true;
			}
		}
		
		if (!$alreadyInCart)
		{
			$products[] = (object)array('product_id' => $productId, 'option_id' => $optionId, 'quantity' => $quantity);
		}
		
		$this->cartCookie->SetCookie($this->cartCookieName, json_encode($products), true);
		$this->Update($products);
		return $this->GetCartMeta($productId, $optionId, $products, $quantity);
	}
	
	public function UpdateCart($productId, $optionId, $quantity)
	{
		$products = $this->GetCart();
		
		foreach($products as $product)
		{
			if ($product->product_id == $productId && $product->option_id == $optionId)
			{
				$product->quantity = $quantity;
				break;
			}
		}
		
		$this->cartCookie->SetCookie($this->cartCookieName, json_encode($products), true);
		$this->Update($products);
		return $this->GetCartMeta($productId, $optionId, $products, $quantity);
	}
	
	public function RemoveFromCart($productId, $optionId)
	{	
		$products = $this->GetCart();
		
		$productIndex = 0;
		
		foreach($products as $product)
		{	
			if ($product->product_id == $productId && $product->option_id == $optionId)
			{
				unset($products[$productIndex]);
				break;
			}
			
			$productIndex++;
		}
		
		$this->cartCookie->SetCookie($this->cartCookieName, json_encode(array_values($products)), true);
		$this->Update($products);
		return $this->GetCartMeta($productId, $optionId, $products);
	}
	
	public function ProductCount($products)
	{
		return count($products);
	}
	
	public function ItemCount($products)
	{	
		$count = 0;
		
		foreach ($products as $product)
		{
			$count += $product->quantity;
		}
		
		return $count;
	}
	
	public function GetCartTotal($products, &$cartShipping, &$cartTax)
	{
		return $this->store->GetCartTotal($products, $cartShipping, $cartTax);
	}
	
	public function GetCartMeta($productId, $optionId, $products, $quantity = 0)
	{	
		$cartMeta = array(
			'itemCount' => $this->GetItemCount($products),
			'productTotal' => $this->GetProductTotal($productId, $optionId, $quantity),
			'cartTotal' => $this->GetCartTotal($products, $this->cartShipping, $this->cartTax),
			'cartTax' => $this->GetCartTax(),
			'cartShipping' => $this->GetCartShipping()
		);
		
		return $cartMeta;
	}
	
	public function GetItemCount($products)
	{
		$count = 0;
		
		foreach($products as $product)
		{
			$count += $product->quantity;
		}
		
		return $count;
	}
	
	public function GetProductTotal($productId, $optionId, $quantity)
	{
		return $this->store->GetProductTotal($productId, $optionId, $quantity);
	}
	
	public function GetCartTax()
	{
		return $this->cartTax;
	}
	
	public function GetCartShipping()
	{
		return $this->cartShipping;
	}
	
	public function EmptyCart()
	{
		$this->cartCookie->DestroyCookie($this->cartCookieName);
		$this->cartCookie = new EncryptedCookie();
	}
	
	// Private Methods
	private function Update($products)
	{
		$this->products = $products;
		$this->productCount = $this->ProductCount($products);
		$this->itemCount = $this->ItemCount($products);
		$this->cartTotal = $this->GetCartTotal($products, $this->cartShipping, $this->cartTax);
	}
}

?>