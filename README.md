# php-tradebook

Example

```php
use Dakshhmehta\PhpTradebook\Trade;
use Dakshhmehta\PhpTradebook\TradeBook;

$tradebook = new TradeBook([
    new Trade([
        'id' => 2,
        'symbol' => 'TCS',
        'date' => '2022-09-26',
        'qty' => 5,
        'type' => 'buy',
        'price' => 200, // Price in USD
        'exchange_rate' => 84, // INR Price for USD
    ]),
    new Trade([
        'id' => 1,
        'symbol' => 'TCS',
        'date' => '2022-10-24',
        'qty' => 10,
        'type' => 'buy',
        'price' => 200, // Price in USD
        'exchange_rate' => 84, // INR Price for USD
    ]),
    new Trade([
        'id' => 3,
        'symbol' => 'TCS',
        'date' => '2022-09-19',
        'qty' => 5,
        'type' => 'sell',
        'price' => 200, // Price in USD
        'exchange_rate' => 84, // INR Price for USD
    ]),
]);

$trades = $tradebook->getTrades();
$ledger = $tradebook->getLedger();
echo $tradebook->getHolding();
```
