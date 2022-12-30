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
        // foreach($trades as &$trade){
        //     $trade->date = Carbon::parse($trade->date);
        // }

        $this->trades = $trades;

        $this->sortTrades();
        $this->prepareHoldings();
        $this->process();
    }

    public function prepareHoldings()
    {
        $symbols = $this->trades->groupBy('symbol');
        foreach ($symbols as $symbol => $trades) {
            $buyQty = collect($trades)->filter(function ($trade) {
                return $trade->type == 'buy';
            })->sum('qty');

            $totalPurchaseValue = 0;
            collect($trades)
                ->filter(function ($trade) {
                    return $trade->type == 'buy';
                })
                ->each(function ($trade) use (&$totalPurchaseValue) {
                    $totalPurchaseValue += ($trade->qty * $trade->price);
                });

            $this->holdings[$symbol] = [
                'price' => sprintf("%.4f", $totalPurchaseValue / $buyQty),
                'qty' => $buyQty,
            ];
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
        $symbols = $this->trades->groupBy('symbol');
        foreach ($symbols as $symbol => $trades) {
            $buyQty = collect($trades)->filter(function ($trade) {
                return $trade->type == 'buy';
            })->sum('qty');

            $sellQty = collect($trades)->filter(function ($trade) {
                return $trade->type == 'sell';
            })->sum('qty');

            $this->holdings[$symbol]['qty'] = $buyQty - $sellQty;
        }

        return $this->holdings;
    }
}
