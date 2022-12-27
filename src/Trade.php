<?php

namespace Dakshhmehta\PhpTradebook;

class Trade
{
    public function __construct($data)
    {
        foreach ($data as $key => $val) {
            $this->{$key} = $val;
        }
    }
}
