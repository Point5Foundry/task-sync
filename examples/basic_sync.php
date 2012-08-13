<?php

require dirname(__FILE__).'/../autoload.php';

$a = new Pff\TaskSync\Provider\Asana('api_key');
$b = new Pff\TaskSync\Provider\Toggl('api_key');

$d = new Pff\TaskSync\Differ();

$p = new Pff\TaskSync\Processor($a, $b, $d);

/** @var $diff \Pff\TaskSync\Data\Client[] */
$diff = $p->diffWorkspace('id1', 'id2');

$p->saveDiff('id2', $diff);
