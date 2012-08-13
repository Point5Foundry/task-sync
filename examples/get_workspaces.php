<?php

require dirname(__FILE__).'/../autoload.php';

$a = new Pff\TaskSync\Provider\Asana('api_key');
$b = new Pff\TaskSync\Provider\Toggl('api_key');

$d = new Pff\TaskSync\Differ();

$p = new Pff\TaskSync\Processor($a, $b, $d);

$p->listWorkspaces();
