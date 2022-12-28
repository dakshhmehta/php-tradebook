<?php

namespace Dakshhmehta\Tests\Unit;

use Dakshhmehta\PhpTradebook\Trade;
use Dakshhmehta\PhpTradebook\TradeBook;
use Dakshhmehta\Tests\TestCase;

class TradebookTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_trades_are_getting_set()
    {
        $tradebook = new TradeBook([
            new Trade([
                'id' => 1,
                'symbol' => 'TCS',
                'date' => '2022-12-10',
                'qty' => 10,
                'type' => 'buy',
            ]),
            new Trade([
                'id' => 2,
                'symbol' => 'TCS',
                'date' => '2022-12-11',
                'qty' => 10,
                'type' => 'buy',
            ]),
            new Trade([
                'id' => 3,
                'symbol' => 'TCS',
                'date' => '2022-12-12',
                'qty' => 5,
                'type' => 'sell',
            ]),
            new Trade([
                'id' => 4,
                'symbol' => 'TCS',
                'date' => '2022-12-24',
                'qty' => 10,
                'type' => 'sell',
            ]),
        ]);

        $this->assertEquals(4, count($tradebook->getTrades()));
    }

    public function test_it_can_set_sell_trades()
    {
        $tradebook = new TradeBook([
            new Trade([
                'id' => 1,
                'symbol' => 'TCS',
                'date' => '2022-12-10',
                'qty' => 10,
                'type' => 'buy',
            ]),
            new Trade([
                'id' => 2,
                'symbol' => 'TCS',
                'date' => '2022-12-11',
                'qty' => 10,
                'type' => 'buy',
            ]),
            new Trade([
                'id' => 4,
                'symbol' => 'TCS',
                'date' => '2022-12-24',
                'qty' => 10,
                'type' => 'sell',
            ]),
            new Trade([
                'id' => 3,
                'symbol' => 'TCS',
                'date' => '2022-12-12',
                'qty' => 5,
                'type' => 'sell',
            ]),
        ]);
        $ledger = $tradebook->getLedger();

        $this->assertEquals(3, count($ledger));

        $this->assertEquals(1, $ledger[0]['buy_id']);
        $this->assertEquals(1, $ledger[1]['buy_id']);
        $this->assertEquals(3, $ledger[0]['sell_id']);
        $this->assertEquals(4, $ledger[1]['sell_id']);
    }

    public function test_it_can_sort_the_trades_automatically()
    {
        $tradebook = new TradeBook([
            new Trade([
                'id' => 2,
                'symbol' => 'TCS',
                'date' => '2022-09-26',
                'qty' => 5,
                'type' => 'buy',
            ]),
            new Trade([
                'id' => 1,
                'symbol' => 'TCS',
                'date' => '2022-10-24',
                'qty' => 10,
                'type' => 'buy',
            ]),
            new Trade([
                'id' => 3,
                'symbol' => 'TCS',
                'date' => '2022-09-19',
                'qty' => 5,
                'type' => 'sell',
            ]),
        ]);

        $trades = $tradebook->getTrades();

        $this->assertEquals('2022-09-19', $trades[0]->date);
        $this->assertEquals('2022-10-24', $trades[count($trades) - 1]->date);
    }

    public function test_it_can_sort_dc_sample1()
    {
        $tradebook = new TradeBook([
            new Trade([
                'id' => 1,
                'symbol' => 'JB',
                'date' => '2022-10-24',
                'type' => 'buy',
                'qty' => 28,
            ]),
            new Trade([
                'id' => 2,
                'symbol' => 'JB',
                'date' => '2022-09-26',
                'type' => 'sell',
                'qty' => 27,
            ]),
            new Trade([
                'id' => 3,
                'symbol' => 'JB',
                'date' => '2022-09-19',
                'type' => 'buy',
                'qty' => 27,
            ]),
            new Trade([
                'id' => 4,
                'symbol' => 'JB',
                'date' => '2022-09-12',
                'type' => 'sell',
                'qty' => 12,
            ]),
            new Trade([
                'id' => 5,
                'symbol' => 'JB',
                'date' => '2022-08-15',
                'type' => 'buy',
                'qty' => 12,
            ]),
        ]);

        $trades = $tradebook->getTrades();

        $this->assertCount(5, $trades);

        $ledger = $tradebook->getLedger();

        $this->assertEquals(5, $ledger[0]['buy_id']);
        $this->assertEquals(4, $ledger[0]['sell_id']);
        $this->assertEquals(3, $ledger[1]['buy_id']);
        $this->assertEquals(2, $ledger[1]['sell_id']);

        $this->assertEquals(28, $tradebook->getHolding());
    }

    public function test_it_can_get_holding_for_short_sell()
    {
        $tradebook = new TradeBook([
            new Trade([
                'id' => 3,
                'symbol' => 'JB',
                'date' => '2022-09-17',
                'type' => 'sell',
                'qty' => 20,
            ]),
            new Trade([
                'id' => 4,
                'symbol' => 'JB',
                'date' => '2022-09-18',
                'type' => 'buy',
                'qty' => 10,
            ]),
        ]);

        $trades = $tradebook->getTrades();

        $this->assertEquals(-10, $tradebook->getHolding());
    }

    public function test_it_can_generate_tradebook_for_multiple_symbols()
    {
        $tradebook = new TradeBook([
            new Trade([
                'id' => 4,
                'symbol' => 'JB',
                'date' => '2022-09-17',
                'type' => 'sell',
                'qty' => 10,
            ]),
            new Trade([
                'id' => 3,
                'symbol' => 'JB',
                'date' => '2022-09-18',
                'type' => 'buy',
                'qty' => 10,
            ]),
            new Trade([
                'id' => 1,
                'symbol' => 'TCS',
                'date' => '2022-10-17',
                'type' => 'sell',
                'qty' => 10,
            ]),
            new Trade([
                'id' => 2,
                'symbol' => 'TCS',
                'date' => '2022-10-18',
                'type' => 'buy',
                'qty' => 10,
            ]),
        ]);

        $trades = $tradebook->getTrades();
        $ledger = $tradebook->getLedger();

        $this->assertCount(4, $trades);
        
        $this->assertEquals(3, $ledger[0]['buy_id']);
        $this->assertEquals(4, $ledger[0]['sell_id']);
        $this->assertEquals(2, $ledger[1]['buy_id']);
        $this->assertEquals(1, $ledger[1]['sell_id']);
    }
}
