<?php

namespace Pff\TaskSync;

interface ProviderInterface {
    /**
     * @abstract
     * @return \Pff\TaskSync\Data\Workspace[]
     */
    public function getWorkspaces();

    /**
     * @abstract
     * @param null|int $workspace_id
     * @return \Pff\TaskSync\Data\Client[]
     */
    public function getClients($workspace_id = null);

    /**
     * @abstract
     * @param null|int $client_id
     * @param null|int $workspace_id
     * @return \Pff\TaskSync\Data\Project[]
     */
    public function getProjects($client_id = null, $workspace_id = null);

    /**
     * @abstract
     * @param null|int $project_id
     * @param null|int $client_id
     * @param null|int $workspace_id
     * @return \Pff\TaskSync\Data\Task[]
     */
    public function getTasks($project_id = null, $client_id = null, $workspace_id = null);

    /**
     * @abstract
     * @param int $workspace_id
     * @return \Pff\TaskSync\Data\Workspace
     */
    public function getAllDataForWorkspace($workspace_id);

    /**
     * @abstract
     * @param $name
     * @return \Pff\TaskSync\Data\Workspace
     */
    public function createWorkspace($name);

    /**
     * @abstract
     * @param int $workspace_id
     * @param string $name
     * @param null|int $foreign_id
     * @return \Pff\TaskSync\Data\Client
     */
    public function createClient($workspace_id, $name, $foreign_id = null);

    /**
     * @abstract
     * @param int $workspace_id
     * @param int $client_id
     * @param string $name
     * @param null|int $foreign_id
     * @return \Pff\TaskSync\Data\Project
     */
    public function createProject($workspace_id, $client_id, $name, $foreign_id = null);

    /**
     * @abstract
     * @param int $workspace_id
     * @param int $client_id
     * @param int $project_id
     * @param string $name
     * @param null|int $foreign_id
     * @return \Pff\TaskSync\Data\Task
     */
    public function createTask($workspace_id, $client_id, $project_id, $name, $foreign_id = null);
}