<?php

use Pff\TaskSync\Data\Workspace;
use Pff\TaskSync\Data\Client;
use Pff\TaskSync\Data\Project;
use Pff\TaskSync\Data\Task;
use Pff\TaskSync\Differ;

class DifferTest extends PHPUnit_Framework_TestCase
{
    /** @var Pff\TaskSync\Differ */
    private $differ;

    public function setUp()
    {
        $this->differ = new Differ();
    }

    public function testClientDiff()
    {
        $a = new Workspace();
        $b = new Workspace();

        $client = new Client('Test');

        $a->addClient($client);

        $actual = $this->differ->compare($a, $b);

        $this->assertEquals(1, count($actual), 'Returns a client');
        $this->assertEquals(array($client), $actual, 'Returns right client');
    }

    public function testComplexClientDiff()
    {
        $a = new Workspace();
        $b = new Workspace();

        $client = new Client(1, 'Test');
        $client2 = new Client(2, 'Client 2');
        $client3 = new Client(3, 'client 3');

        $a->addClient($client);
        $a->addClient($client2);
        $b->addClient($client2);
        $b->addClient($client3);

        $actual = $this->differ->compare($a, $b);

        $this->assertEquals(array($client), $actual, 'Returns right client');
        $this->assertEquals(1, count($actual), 'Returns a client');
    }

    public function testProjectDiff()
    {
        $c1 = new Client(1, 'test client');
        $c2 = new Client(3, 'test client');
        $p1 = new Project(1, 'test project');
        $c1->addProject($p1);

        $w1 = new Workspace();
        $w2 = new Workspace();

        $w1->addClient($c1);
        $w2->addClient($c2);

        $expected = array();
        $expected[0] = new Client(1, 'test client', false);
        $expected[0]->addProject(new Project(1, 'test project', false));

        $actual = $this->differ->compare($w1, $w2);

        $this->assertEquals($expected, $actual);
    }
}