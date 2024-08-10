<?php

namespace Dakshhmehta\PhpTradebook;

class Trade
{
    public $purchase_secondary_currency;
    public $purchase;
    public $original_qty;
    public $exchange_rate;
    public $type;
    public $price;
    public $qty;
    public $date;
    public $symbol;
    public $id;

    public function __construct($data)
    {
        foreach ($data as $key => $val) {
            $this->{$key} = $val;
        }
    }
}
