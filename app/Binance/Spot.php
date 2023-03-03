<?php

namespace App\Binance;

use App\Binance\Spot\Fiat;
use App\Binance\Spot\Market;
use App\Binance\Spot\Stream;
use App\Binance\Spot\Trade;

class Spot extends ApiClient
{
    use Fiat;
    use Market;
    use Stream;
    use Trade;

    public function __construct(array $args = [])
    {
        $args['baseURL'] = $args['baseURL'] ?? 'https://api.binance.com';
        parent::__construct($args);
    }
}
