<?php

class SampleShopOrderItem extends Model implements IShopOrderItem
{
	public function GetTableName() { return 'items'; }

	public function GetAmount($currency) { return $this->price; }

	public function GetDiscount() { return 0; }

	public function GetHandling() { return 0; }

	public function GetName() { return $this->title; }

	public function GetQuantity() { return $this->amount; }

	public function GetShipping() { return 0; }
}