<?php

include_once(_lib_.'/mvc/lib.model.php');

class ProductLms extends Model
{
	protected $_table = 'learning_product';
	
	public function getProduct($product_id = 0)
	{
		$sql = "SELECT * FROM %lms_product WHERE idProduct=".$product_id;
		return $this->db->fetch_obj($this->db->query($sql));
	}
	
	public function getProducts($search = array())
	{
		$order = 'idProduct';
		$result = array();
		
		if(isset($search['order']))
		{
			$order = $search['order'];
			unset($search['order']);
		}
		
		$sql = "SELECT * FROM %lms_product WHERE 1";
		foreach($search as $key => $value) $sql .= 'AND '.$key.'="'.$value.'"';
		$sql .= ' ORDER BY '.$order;
		
		$list = $this->db->query($sql);
		
		while($row = $this->db->fetch_obj($list))
		{
			$result[] = $row;
		}
		
		return $result;
	}
	
	public static function getTTC($amount_ht = 0)
	{
		return round($amount_ht * 1.196, 2);
	}
	
	public static function priceFormat($price = 0)
	{
		return number_format($price, 2, ',', ' ');
	}
	
	public static function doDiscount($price, $discount = 0, $dif = false)
	{
	    $result = 0;
	    if($dif) $result = $price*$discount;
	    else $result = $price - ($price*$discount);
	    return round($result, 2);
	}
}