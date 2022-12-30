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

        $this->assertEquals(28, $tradebook->getHoldings()['JB']['qty']);
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

        $this->assertEquals(-10, $tradebook->getHoldings()['JB']['qty']);
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

    public function test_sample2_reliance_matches_properly()
    {
        $tradebook = new TradeBook([
            new Trade([
                'id' => 11,
                'symbol' => 'RELIANCE',
                'date' => '2022-06-27',
                'type' => 'sell',
                'qty' => 139,
            ]),
            new Trade([
                'id' => 10,
                'symbol' => 'RELIANCE',
                'date' => '2021-08-23',
                'type' => 'buy',
                'qty' => 139,
            ]),
            new Trade([
                'id' => 9,
                'symbol' => 'RELIANCE',
                'date' => '2021-08-02',
                'type' => 'sell',
                'qty' => 136,
            ]),
            new Trade([
                'id' => 8,
                'symbol' => 'RELIANCE',
                'date' => '2021-06-07',
                'type' => 'buy',
                'qty' => 136,
            ]),
            new Trade([
                'id' => 7,
                'symbol' => 'RELIANCE',
                'date' => '2021-10-04',
                'type' => 'sell',
                'qty' => 150,
            ]),
            new Trade([
                'id' => 6,
                'symbol' => 'RELIANCE',
                'date' => '2021-05-24',
                'type' => 'buy',
                'qty' => 150,
            ]),
            new Trade([
                'id' => 5,
                'symbol' => 'RELIANCE',
                'date' => '2022-12-12',
                'type' => 'sell',
                'qty' => 166,
            ]),
            new Trade([
                'id' => 4,
                'symbol' => 'RELIANCE',
                'date' => '2022-10-09',
                'type' => 'buy',
                'qty' => 166,
            ]),
            new Trade([
                'id' => 3,
                'symbol' => 'RELIANCE',
                'date' => '2022-11-07',
                'type' => 'buy',
                'qty' => 77,
            ]),
            new Trade([
                'id' => 2,
                'symbol' => 'RELIANCE',
                'date' => '2022-09-05',
                'type' => 'sell',
                'qty' => 76,
            ]),
            new Trade([
                'id' => 1,
                'symbol' => 'RELIANCE',
                'date' => '2022-08-29',
                'type' => 'buy',
                'qty' => 76,
            ]),
        ]);

        $trades = $tradebook->getTrades();
        $ledger = $tradebook->getLedger();

        $this->assertCount(6, $ledger);
        $this->assertEquals(77, $tradebook->getHoldings()['RELIANCE']['qty']);
    }

    public function test_it_can_fetch_multiple_symbol_holdings()
    {
        $tradebook = new TradeBook([
            new Trade([
                'id' => 1,
                'symbol' => 'RELIANCE',
                'date' => '2022-06-27',
                'type' => 'buy',
                'qty' => 50,
            ]),
            new Trade([
                'id' => 2,
                'symbol' => 'RELIANCE',
                'date' => '2022-08-23',
                'type' => 'sell',
                'qty' => 30,
            ]),
            new Trade([
                'id' => 3,
                'symbol' => 'TCS',
                'date' => '2022-08-02',
                'type' => 'buy',
                'qty' => 10,
            ]),
            new Trade([
                'id' => 4,
                'symbol' => 'TCS',
                'date' => '2022-09-05',
                'type' => 'sell',
                'qty' => 10,
            ]),
            new Trade([
                'id' => 5,
                'symbol' => 'DMART',
                'date' => '2022-08-29',
                'type' => 'buy',
                'qty' => 5,
            ]),
        ]);

        $holdings = $tradebook->getHoldings();
        $this->assertEquals(5, $holdings['DMART']['qty']);
        $this->assertEquals(0, $holdings['TCS']['qty']);
        $this->assertEquals(20, $holdings['RELIANCE']['qty']);
    }
}
