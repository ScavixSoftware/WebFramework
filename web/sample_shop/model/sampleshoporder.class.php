<?php

class SampleShopOrder extends Model implements IShopOrder
{
	const UNKNOWN  = 0;
	const PENDING  = 10;
	const PAID     = 20;
	const FAILED   = 30;
	const REFUNDED = 40;
	
	public function GetTableName() { return 'orders'; }

	public function GetAddress()
	{
		$res = new ShopOrderAddress();
		$res->Firstname = $this->fname;
		$res->Lastname = $this->lname;
		$res->Address1 = $this->street;
		$res->Zip = $this->zip;
		$res->City = $this->city;
		$res->Email = $this->email;
		return $res;
	}

	public function GetCurrency() { return 'EUR'; }

	public function GetInvoiceId() { return "I".$this->id; }

	public function GetLocale() { return 'en-US'; }

	public function GetTotalPrice($price = false)
	{
		if( $price !== false )
			return $price * ( (1+$this->GetVatPercent()) / 100 );
		return $this->price_total * ( (1+$this->GetVatPercent()) / 100 );
	}

	public function GetTotalVat() { return $this->price_total * ($this->GetVatPercent()/100); }

	public function GetVatPercent() { return 19; }

	public function ListItems() { return SampleShopOrderItem::Make()->eq('order_id',$this->id)->orderBy('id'); }

	public function SetCurrency($currency_code) { /* we stay with EUR */ }

	public static function FromOrderId($order_id)
	{
		return SampleShopOrder::Make()->eq('id',$order_id)->current();
	}

	public function SetFailed($payment_provider_type, $transaction_id, $statusmsg = false)
	{
		$this->status = self::FAILED;
		$this->updated = $this->deleted = 'now()';
		$this->Save();
	}

	public function SetPaid($payment_provider_type, $transaction_id, $statusmsg = false)
	{
		$this->status = self::PAID;
		$this->updated = $this->completed = 'now()';
		$this->Save();
	}

	public function SetPending($payment_provider_type, $transaction_id, $statusmsg = false)
	{
		$this->status = self::PENDING;
		$this->updated = 'now()';
		$this->Save();
	}

	public function SetRefunded($payment_provider_type, $transaction_id, $statusmsg = false)
	{
		$this->status = self::REFUNDED;
		$this->updated = $this->deleted = 'now()';
		$this->Save();
	}

	public function DoAddVat() { return true; /* Let's assume normal VAT customers for now */ }
}