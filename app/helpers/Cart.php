<?php

namespace sprint\app\helpers;

use \sprint\app\core\Session;
use \sprint\app\core\Security;
use \sprint\app\exceptions\SecurityException;

class Cart
{	
	private $session;
	private $security;

	private $availableInCart = false;
	
	public function __construct()
	{
		$this->session 	= new Session;
		$this->security = new Security;
	}	
	
	public function add(array $item)
	{
		if($this->session->get("cart") === null)
		{
			if(!empty($item))
			{
				$item = array_merge($item, ["quantity" => 1, "total" => $item["price"]]);
				
				$this->session->set("cart", $item, true);
				
				return true;
			}
		}else
		{
			$this->update($item);
			
			if($this->availableInCart === false)
			{
				$item = array_merge($item, ["quantity" => 1, "total" => $item["price"]]);
				
				$this->session->set("cart", $item, true);
				
				return true;
			}
			
		}
	}
	
	public function update(array $item)
	{
		if($this->session->get("cart") !== null)
		{
			foreach($this->session->get("cart") as $index => $itemInCart)
			{				
				if(isset($itemInCart["quantity"]) || isset($itemInCart["price"]))
				{
					$matches = array_diff($itemInCart, $item);

					if(isset($matches["quantity"])) unset($matches["quantity"]);
					if(isset($matches["total"])) 	unset($matches["total"]);

					if(!$matches)
					{
						$itemInCart["quantity"] += 1;

						$total = $itemInCart["quantity"] * $itemInCart["price"];

						$this->session->replace("cart", $index, "total", $total);

						$this->session->replace("cart", $index, "quantity", $itemInCart["quantity"]);
						
						$this->availableInCart = true;
						
						return true;
					}
				}else
				{
					throw new \Exception("The keys price or quantity are missing on your item.");

					return false;
				}
			}
		}
		
	}
	
	public function remove(array $item)
	{
		if($this->session->get("cart") !== null)
		{
			foreach($this->session->get("cart") as $index => $itemInCart)
			{				
				if(isset($itemInCart["quantity"]) || isset($itemInCart["price"]))
				{
					$matches = array_diff($itemInCart, $item);

					if(isset($matches["quantity"])) unset($matches["quantity"]);
					if(isset($matches["total"])) 	unset($matches["total"]);

					if(!$matches)
					{
						$this->session->remove("cart", $index);						
						return true;
					}
				}else
				{
					throw new \Exception("The keys price or quantity are missing on your item.");

					return false;
				}
			}
		}
	}
	
	public function totalItems()
	{
		return count($this->get());
	}
	
	public function totalPrice()
	{
		return array_sum(array_column($this->get(), "total"));
	}
	
	public function get()
	{
		return $this->session->get("cart") ?? [];
	}
}