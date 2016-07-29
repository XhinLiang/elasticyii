<?php
namespace ElasticSearch\tests;

use mageekguy\atoum\test;

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/Helper.php';

class Base extends test
{
    protected function getTag()
    {
        return uniqid(getmypid());
    }
}
