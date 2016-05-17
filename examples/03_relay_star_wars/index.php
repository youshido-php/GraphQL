<?php

namespace Examples\StarWars;

use Youshido\GraphQL\Processor;
use Youshido\GraphQL\Schema;

require_once __DIR__ . '/schema-bootstrap.php';
/** @var Schema\AbstractSchema $schema */
$schema = new StarWarsRelaySchema();

$processor = new Processor();

$processor->setSchema($schema);
$payload = '{ factions { name } }';

$processor->processRequest($payload);
echo json_encode($processor->getResponseData()) . "\n";
