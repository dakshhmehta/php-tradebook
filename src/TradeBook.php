<?php

namespace Dakshhmehta\PhpTradebook;

use App\Models\Trade;
use Carbon\Carbon;

class TradeBook
{
    protected $trades = [];
    protected $ledger = [];

    protected $holdings = [];

    public function __construct($trades)
    {
        foreach ($trades as &$trade) {
            $trade->original_qty = $trade->qty;
        }

        $this->trades = $trades;

        $this->sortTrades();
        $this->process();
        $this->prepareHoldings();
    }

    public function prepareHoldings()
    {
        foreach($this->trades as &$trade){
            if(! isset($this->holdings[$trade->symbol])){
                $this->holdings[$trade->symbol] = [
                    'price' => 0,
                    'qty' => 0,
                ];
            }

            if($trade->type == 'buy'){
                // 1000 * 10 = 10000
                $purchaseValue = $trade->price * $trade->original_qty;
                // 10000 + 0
                $totalPurchaseValue = $purchaseValue + ($this->holdings[$trade->symbol]['price'] * $this->holdings[$trade->symbol]['qty']);

                $this->holdings[$trade->symbol]['qty'] += $trade->original_qty;

                // 10000/10 = 1000
                try {
                    $avgPrice = $totalPurchaseValue / $this->holdings[$trade->symbol]['qty'];
                    $this->holdings[$trade->symbol]['price'] = sprintf("%.4f", $avgPrice);
                }
                catch(\Exception $e){
                    // Short covering happened, so no calculation to make
                }
            }
            else {
                $this->holdings[$trade->symbol]['qty'] -= $trade->original_qty;
            }
        }
    }

    protected function sortTrades()
    {
        $this->trades = collect($this->trades)->sortBy('date')->values();
    }

    protected function process()
    {
        // dd($this->trades->toArray());
        foreach ($this->trades as $i => $trade) {
            // Trade is already proceesed
            if ($trade->qty == 0) continue;

            $previousTradeId = null;

            do {
                $nextTrade = null;
                if ($trade->type == 'buy') {
                    if ($previousTradeId == null) {
                        $nextTrade = $this->findNextSellTrade($i);
                    } else {
                        $nextTrade = $this->findNextSellTrade($previousTradeId);
                    }

                    if ($nextTrade === false) {
                        break;
                    }

                    $previousTradeId = $nextTrade;
                    $nextTrade = $this->trades[$nextTrade];

                    $qty = min($this->trades[$i]->qty, $nextTrade->qty);
                    $this->ledger[] = [
                        'buy_id' => $trade->id,
                        'sell_id' => $nextTrade->id,
                        'qty' => $qty,
                    ];

                    $this->trades[$i]->qty -= $qty;
                    $this->trades[$previousTradeId]->qty -= $qty;
                } else if ($trade->type == 'sell') {
                    if ($previousTradeId == null) {
                        $nextTrade = $this->findNextBuyTrade($i);
                    } else {
                        $nextTrade = $this->findNextBuyTrade($previousTradeId);
                    }

                    if ($nextTrade === false) {
                        break;
                    }

                    $previousTradeId = $nextTrade;
                    $nextTrade = $this->trades[$nextTrade];

                    $qty = min($this->trades[$i]->qty, $nextTrade->qty);
                    $this->ledger[] = [
                        'sell_id' => $trade->id,
                        'buy_id' => $nextTrade->id,
                        'qty' => $qty,
                    ];

                    $this->trades[$i]->qty -= $qty;
                    $this->trades[$previousTradeId]->qty -= $qty;
                }
            } while ($this->trades[$i]->qty > 0);
        }
    }

    // TODO: Test
    protected function findNextSellTrade($start)
    {
        for ($i = $start + 1; $i < count($this->trades); $i++) {
            if ($this->trades[$i]->symbol == $this->trades[$start]->symbol && $this->trades[$i]->type == 'sell' && $this->trades[$i]->qty > 0) {
                return $i;
            }
        }

        return false;
    }

    // TODO: Test
    protected function findNextBuyTrade($start)
    {
        for ($i = $start + 1; $i < count($this->trades); $i++) {
            if ($this->trades[$i]->symbol == $this->trades[$start]->symbol && $this->trades[$i]->type == 'buy' && $this->trades[$i]->qty > 0) {
                return $i;
            }
        }

        return false;
    }

    public function getTrades()
    {
        return $this->trades;
    }

    public function getLedger()
    {
        return $this->ledger;
    }

    public function getHoldings()
    {
        return $this->holdings;
    }
}
