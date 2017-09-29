<?php

namespace unapi\def\tele2;

use GuzzleHttp\Client;

class Tele2Client extends Client
{
    /**
     * @param array $config
     */
    public function __construct(array $config = [])
    {
        $config['base_uri'] = 'http://mnp.tele2.ru/';
        $config['cookies'] = true;

        parent::__construct($config);
    }
}