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
                'price' => 1,
                'type' => 'buy',
            ]),
            new Trade([
                'id' => 2,
                'symbol' => 'TCS',
                'date' => '2022-12-11',
                'qty' => 10,
                'price' => 1,

                'type' => 'buy',
            ]),
            new Trade([
                'id' => 3,
                'symbol' => 'TCS',
                'date' => '2022-12-12',
                'qty' => 5,
                'price' => 1,

                'type' => 'sell',
            ]),
            new Trade([
                'id' => 4,
                'symbol' => 'TCS',
                'date' => '2022-12-24',
                'qty' => 10,
                'price' => 1,

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
                'price' => 1,
            ]),
            new Trade([
                'id' => 2,
                'symbol' => 'TCS',
                'date' => '2022-12-11',
                'qty' => 10,
                'type' => 'buy',
                'price' => 1,
            ]),
            new Trade([
                'id' => 4,
                'symbol' => 'TCS',
                'date' => '2022-12-24',
                'qty' => 10,
                'type' => 'sell',
                'price' => 1,
            ]),
            new Trade([
                'id' => 3,
                'symbol' => 'TCS',
                'date' => '2022-12-12',
                'qty' => 5,
                'type' => 'sell',
                'price' => 1,
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
                'price' => 100,
            ]),
            new Trade([
                'id' => 1,
                'symbol' => 'TCS',
                'date' => '2022-10-24',
                'qty' => 10,
                'type' => 'buy',
                'price' => 100,
            ]),
            new Trade([
                'id' => 3,
                'symbol' => 'TCS',
                'date' => '2022-09-19',
                'qty' => 5,
                'type' => 'sell',
                'price' => 1,
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
                'price' => 1,
            ]),
            new Trade([
                'id' => 2,
                'symbol' => 'JB',
                'date' => '2022-09-26',
                'type' => 'sell',
                'qty' => 27,
                'price' => 1,
            ]),
            new Trade([
                'id' => 3,
                'symbol' => 'JB',
                'date' => '2022-09-19',
                'type' => 'buy',
                'qty' => 27,
                'price' => 1,
            ]),
            new Trade([
                'id' => 4,
                'symbol' => 'JB',
                'date' => '2022-09-12',
                'type' => 'sell',
                'qty' => 12,
                'price' => 1,
            ]),
            new Trade([
                'id' => 5,
                'symbol' => 'JB',
                'date' => '2022-08-15',
                'type' => 'buy',
                'qty' => 12,
                'price' => 1,
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
                'price' => 100,
            ]),
            new Trade([
                'id' => 4,
                'symbol' => 'JB',
                'date' => '2022-09-18',
                'type' => 'buy',
                'qty' => 10,
                'price' => 100,
            ]),
        ]);

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
                'price' => 1,
            ]),
            new Trade([
                'id' => 3,
                'symbol' => 'JB',
                'date' => '2022-09-18',
                'type' => 'buy',
                'qty' => 10,
                'price' => 1,
            ]),
            new Trade([
                'id' => 1,
                'symbol' => 'TCS',
                'date' => '2022-10-17',
                'type' => 'sell',
                'qty' => 10,
                'price' => 1,
            ]),
            new Trade([
                'id' => 2,
                'symbol' => 'TCS',
                'date' => '2022-10-18',
                'type' => 'buy',
                'qty' => 10,
                'price' => 1,
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
                'price' => 1,
            ]),
            new Trade([
                'id' => 10,
                'symbol' => 'RELIANCE',
                'date' => '2021-08-23',
                'type' => 'buy',
                'qty' => 139,
                'price' => 1,
            ]),
            new Trade([
                'id' => 9,
                'symbol' => 'RELIANCE',
                'date' => '2021-08-02',
                'type' => 'sell',
                'qty' => 136,
                'price' => 1,
            ]),
            new Trade([
                'id' => 8,
                'symbol' => 'RELIANCE',
                'date' => '2021-06-07',
                'type' => 'buy',
                'qty' => 136,
                'price' => 1,
            ]),
            new Trade([
                'id' => 7,
                'symbol' => 'RELIANCE',
                'date' => '2021-10-04',
                'type' => 'sell',
                'qty' => 150,
                'price' => 1,
            ]),
            new Trade([
                'id' => 6,
                'symbol' => 'RELIANCE',
                'date' => '2021-05-24',
                'type' => 'buy',
                'qty' => 150,
                'price' => 1,
            ]),
            new Trade([
                'id' => 5,
                'symbol' => 'RELIANCE',
                'date' => '2022-12-12',
                'type' => 'sell',
                'qty' => 166,
                'price' => 1,
            ]),
            new Trade([
                'id' => 4,
                'symbol' => 'RELIANCE',
                'date' => '2022-10-09',
                'type' => 'buy',
                'qty' => 166,
                'price' => 1,
            ]),
            new Trade([
                'id' => 3,
                'symbol' => 'RELIANCE',
                'date' => '2022-11-07',
                'type' => 'buy',
                'qty' => 77,
                'price' => 1,
            ]),
            new Trade([
                'id' => 2,
                'symbol' => 'RELIANCE',
                'date' => '2022-09-05',
                'type' => 'sell',
                'qty' => 76,
                'price' => 1,
            ]),
            new Trade([
                'id' => 1,
                'symbol' => 'RELIANCE',
                'date' => '2022-08-29',
                'type' => 'buy',
                'qty' => 76,
                'price' => 1,
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
                'price' => 1,
            ]),
            new Trade([
                'id' => 2,
                'symbol' => 'RELIANCE',
                'date' => '2022-08-23',
                'type' => 'sell',
                'qty' => 30,
                'price' => 1,
            ]),
            new Trade([
                'id' => 3,
                'symbol' => 'TCS',
                'date' => '2022-08-02',
                'type' => 'buy',
                'qty' => 10,
                'price' => 1,
            ]),
            new Trade([
                'id' => 4,
                'symbol' => 'TCS',
                'date' => '2022-09-05',
                'type' => 'sell',
                'qty' => 10,
                'price' => 1,
            ]),
            new Trade([
                'id' => 5,
                'symbol' => 'DMART',
                'date' => '2022-08-29',
                'type' => 'buy',
                'qty' => 5,
                'price' => 1,
            ]),
        ]);

        $holdings = $tradebook->getHoldings();
        $this->assertEquals(5, $holdings['DMART']['qty']);
        $this->assertEquals(20, $holdings['RELIANCE']['qty']);
    }

    public function test_it_can_calculate_avg_price_purchase()
    {
        $tradebook = new TradeBook([
            new Trade([
                'id' => 1,
                'symbol' => 'RELIANCE',
                'date' => '2022-06-27',
                'type' => 'buy',
                'qty' => 10,
                'price' => 1000,
            ]),
            new Trade([
                'id' => 2,
                'symbol' => 'RELIANCE',
                'date' => '2022-08-23',
                'type' => 'buy',
                'qty' => 5,
                'price' => 1200,
            ]),
            new Trade([
                'id' => 3,
                'symbol' => 'RELIANCE',
                'date' => '2022-08-30',
                'type' => 'sell',
                'qty' => 10,
                'price' => 1500,
            ]),
        ]);

        $holdings = $tradebook->getHoldings();

        $this->assertEquals(5, $holdings['RELIANCE']['qty']);
        $this->assertEquals(1200, $holdings['RELIANCE']['price']);
    }

    public function test_it_can_calculate_avg_price_purchase_between_sales_CCRI_sample()
    {
        $tradebook = new TradeBook([
            new Trade([
                'id' => 1,
                'symbol' => 'CCRI',
                'date' => '2022-06-13',
                'type' => 'buy',
                'qty' => 156,
                'price' => 638.42,
            ]),
            new Trade([
                'id' => 2,
                'symbol' => 'CCRI',
                'date' => '2022-06-20',
                'type' => 'sell',
                'qty' => 156,
                'price' => 608.93,
            ]),
            new Trade([
                'id' => 3,
                'symbol' => 'CCRI',
                'date' => '2022-07-18',
                'type' => 'buy',
                'qty' => 149,
                'price' => 670.35,
            ]),
        ]);

        $holdings = $tradebook->getHoldings();

        $this->assertEquals(149, $holdings['CCRI']['qty']);
        $this->assertEquals(670.35, $holdings['CCRI']['price']);
    }

    // TODO: Complete this testcase
    public function it_can_validate_zerodha_avg_price_scenario()
    {
        // Test data as found on : https://support.zerodha.com/category/q-backoffice/portfolio/articles/how-is-the-buy-average-calculated-in-q
        $tradebook = new TradeBook([
            new Trade([
                'id' => 1,
                'symbol' => 'ITC',
                'date' => '2018-02-16',
                'type' => 'buy',
                'qty' => 50,
                'price' => 260,
            ]),
            new Trade([
                'id' => 2,
                'symbol' => 'ITC',
                'date' => '2018-02-19',
                'type' => 'buy',
                'qty' => 30,
                'price' => 256,
            ]),
        ]);
    }

    public function test_it_can_avg_price_in_fifo_manner()
    {
        $tradebook = new TradeBook([
            new Trade([
                'id' => 1,
                'symbol' => 'RELIANCE',
                'date' => '2022-12-13',
                'type' => 'buy',
                'qty' => 5,
                'price' => 1000,
            ]),
            new Trade([
                'id' => 2,
                'symbol' => 'RELIANCE',
                'date' => '2022-12-15',
                'type' => 'buy',
                'qty' => 20,
                'price' => 1100,
            ]),
            new Trade([
                'id' => 4,
                'symbol' => 'RELIANCE',
                'date' => '2022-12-17',
                'type' => 'buy',
                'qty' => 15,
                'price' => 900,
            ]),
            new Trade([
                'id' => 2,
                'symbol' => 'RELIANCE',
                'date' => '2022-12-18',
                'type' => 'sell',
                'qty' => 10,
                'price' => 1200,
            ]),
        ]);

        $holdings = $tradebook->getHoldings();

        $this->assertEquals(30, $holdings['RELIANCE']['qty']);
        $this->assertEquals(1000, $holdings['RELIANCE']['price']);
    }

    public function test_it_has_no_key_if_no_holdings()
    {
        $tradebook = new TradeBook([
            new Trade([
                'id' => 1,
                'symbol' => 'reliance',
                'date' => '2022-12-13',
                'type' => 'Buy',
                'qty' => 5,
                'price' => 1000,
            ]),
            new Trade([
                'id' => 2,
                'symbol' => 'RELIANCE',
                'date' => '2022-12-15',
                'type' => 'sell',
                'qty' => 5,
                'price' => 1100,
            ]),
        ]);

        $holdings = $tradebook->getHoldings();

        $this->assertTrue(!isset($holdings['RELIANCE']));
    }
}
