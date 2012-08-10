<?php

namespace Pff\TaskSync\Provider;

use Pff\TaskSync\ProviderInterface;
use Pff\TaskSync\Data as Data;

class EchoDebug implements ProviderInterface
{
    private $id;

    public function __construct($id)
    {
        $this->id = $id;
    }

    /**
     * @return \Pff\TaskSync\Data\Workspace[]
     */
    public function getWorkspaces()
    {
        $this->output('getWorkspaces');
    }

    /**
     * @param null|int $workspace_id
     * @return \Pff\TaskSync\Data\Client[]
     */
    public function getClients($workspace_id = null)
    {
        $this->output('getClients(workspace_id: '.$workspace_id.')');
    }

    /**
     * @param null|int $client_id
     * @param null|int $workspace_id
     * @return \Pff\TaskSync\Data\Project[]
     */
    public function getProjects($client_id = null, $workspace_id = null)
    {
        $this->output('getProjects(client_id: '.$client_id.', workspace_id: '.$workspace_id.')');
    }

    /**
     * @param null|int $project_id
     * @param null|int $client_id
     * @param null|int $workspace_id
     * @return \Pff\TaskSync\Data\Task[]
     */
    public function getTasks($project_id = null, $client_id = null, $workspace_id = null)
    {
        $this->output('getTasks(project_id: '.$project_id.', client_id: '.$client_id.', workspace_id: '.$workspace_id.')');
    }

    /**
     * @param int $workspace_id
     * @return \Pff\TaskSync\Data\Workspace
     */
    public function getAllDataForWorkspace($workspace_id)
    {
        $this->output('getAllDataForWorkspace('.$workspace_id.')');
    }

    /**
     * @param $name
     * @return \Pff\TaskSync\Data\Workspace
     */
    public function createWorkspace($name)
    {
        $this->output('createWorkspace(name: '.$name.')');
        return new Data\Workspace(rand(1, 100), $name);
    }

    /**
     * @param int $workspace_id
     * @param string $name
     * @param null|int $foreign_id
     * @return \Pff\TaskSync\Data\Client
     */
    public function createClient($workspace_id, $name, $foreign_id = null)
    {
        $this->output('createClient(workspace_id: '.$workspace_id.', name: '.$name.', foreign_id: '.$foreign_id.')');
        return new Data\Client(rand(1, 100), $name);
    }

    /**
     * @param int $workspace_id
     * @param int $client_id
     * @param string $name
     * @param null|int $foreign_id
     * @return \Pff\TaskSync\Data\Project
     */
    public function createProject($workspace_id, $client_id, $name, $foreign_id = null)
    {
        $this->output('createProject(workspace_id: '.$workspace_id.', client_id: '.$client_id.', name: '.$name.', foreign_id: '.$foreign_id.')');
        return new Data\Project(rand(1, 100), $name);
    }

    /**
     * @param int $workspace_id
     * @param int $client_id
     * @param int $project_id
     * @param string $name
     * @param null|int $foreign_id
     * @return \Pff\TaskSync\Data\Task
     */
    public function createTask($workspace_id, $client_id, $project_id, $name, $foreign_id = null)
    {
        $this->output('createTask(workspace_id: '.$workspace_id.', client_id: '.$client_id.', project_id: '.$project_id.', name: '.$name.', foreign_id: '.$foreign_id.')');
        return new Data\Task(rand(1, 100), $name);
    }

    private function output($string)
    {
        echo $this->id.': '.$string."\n";
    }
}