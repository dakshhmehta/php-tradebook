# php-tradebook

Example

```php
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
        $ledger = $tradebook->getTrades();
        echo $tradebook->getHolding();
```
