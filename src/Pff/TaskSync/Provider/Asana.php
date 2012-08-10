<?php

namespace Pff\TaskSync\Provider;

use Pff\TaskSync\ProviderInterface;
use Pff\TaskSync\Data as Data;

class Asana implements ProviderInterface
{
    const HTTP_GET = 1;
    const HTTP_POST = 2;
    const HTTP_PUT = 3;

    private $api_key;
    private $api_version;

    private $w_workspaces;
    private $w_projects;

    /** @var Data\Workspace[] */
    private $workspaces;

    /** @var Data\Client[] */
    private $clients;

    public function __construct($api_key, $api_version = '1.0')
    {
        $this->api_key = $api_key;
        $this->api_version = $api_version;
        $this->w_projects = null;
        $this->w_workspaces = null;
        $this->workspaces = null;
        $this->clients = null;
    }

    public function request($command, $data = array(), $action = self::HTTP_GET)
    {
        $url = 'https://app.asana.com/api/'.$this->api_version.'/'.$command;

        $ch = curl_init();

        if ($action == self::HTTP_POST) {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        } else if ($action == self::HTTP_PUT) {
            curl_setopt($ch, CURLOPT_PUT, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        } else {
            curl_setopt($ch, CURLOPT_HTTPGET, true);
            if (count($data) > 0)
            {
                $vars = array();
                foreach($data as $name => $val)
                    $vars[] = urlencode($name).'='.urlencode($val);
                $url .= '?'.implode('&', $vars);
            }
        }
        curl_setopt($ch, CURLOPT_URL, $url);

        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
        ));

        curl_setopt($ch, CURLOPT_USERPWD, $this->api_key.':');

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $content = curl_exec($ch);

        curl_close($ch);

        return json_decode($content);
    }

    /**
     * @return \Pff\TaskSync\Data\Workspace[]
     */
    public function getWorkspaces()
    {
        if ($this->workspaces == null)
        {
            $workspaces = array();
            $response = $this->request('workspaces');

            $this->w_workspaces = $response->data;

            foreach($response->data as $ws)
                $workspaces[] = new Data\Workspace($ws->id, $ws->name);

            $this->workspaces = $workspaces;
        }

        return $this->workspaces;
    }

    /**
     * @param null|int $workspace_id
     * @return \Pff\TaskSync\Data\Client[]
     */
    public function getClients($workspace_id = null)
    {
        if ($this->clients == null)
        {
            if (!isset($this->clients[$workspace_id]))
                $this->clients[$workspace_id] = array();

            $data = array('workspace' => $workspace_id);

            $response = $this->request('projects', $data);

            $this->w_projects = $response->data;

            $client_track = array();

            foreach($response->data as $idx => $project)
            {
                $info = $this->parseProject($project->name);
                $cname = $info['client'];
                $pname = $info['project'];
                if (!isset($client_track[$cname]))
                    $client_track[$cname] = new Data\Client($idx, $cname, false);
                $p = new Data\Project($project->id, $pname);
                $client_track[$cname]->addProject($p);

            }

            foreach($client_track as $client)
                $this->clients[$workspace_id][$client->getId()] = $client;
        }

        return $this->clients[$workspace_id];
    }

    /**
     * @param null|int $client_id
     * @param null|int $workspace_id
     * @return \Pff\TaskSync\Data\Project[]
     */
    public function getProjects($client_id = null, $workspace_id = null)
    {
        $clients = $this->getClients($workspace_id);

        if (!isset($clients[$client_id]))
            return false;

        return $clients[$client_id]->getProjects();
    }

    /**
     * @param null|int $project_id
     * @param null|int $client_id
     * @param null|int $workspace_id
     * @return \Pff\TaskSync\Data\Task[]
     */
    public function getTasks($project_id = null, $client_id = null, $workspace_id = null)
    {
        $data = array(
            'project' => $project_id,
        );

        $projects = $this->getProjects($client_id, $workspace_id);

        $project = null;

        foreach($projects as $p)
            if ($p->getId() == $project_id)
            {
                $project = $p;
                break;
            }

        if ($project == null)
            return false;

        if (count($project->getTasks()) == 0)
        {
            $tasks = array();

            $result = $this->request('tasks', $data);

            foreach($result->data as $t)
            {
                $task = new Data\Task($t->id, $t->name);
                $tasks[] = $task;
            }

            $project->setTasks($tasks);
        }

        return $project->getTasks();
    }

    /**
     * @param $name
     * @return \Pff\TaskSync\Data\Workspace
     */
    public function createWorkspace($name)
    {
        // TODO: Implement createWorkspace() method.
    }

    /**
     * @param int $workspace_id
     * @param string $name
     * @param null|int $foreign_id
     * @return \Pff\TaskSync\Data\Client
     */
    public function createClient($workspace_id, $name, $foreign_id = null)
    {
        // TODO: Implement createClient() method.
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
        // TODO: Implement createProject() method.
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
        // TODO: Implement createTask() method.
    }

    private function parseProject($name)
    {
        $pattern = '/^\[([^\]]+)\] (.*)$/';
        if (preg_match($pattern, $name, $matches))
        {
            return array('client' => trim($matches[1]), 'project' => trim($matches[2]));
        }

        return null;
    }

    /**
     * @param int $workspace_id
     * @return \Pff\TaskSync\Data\Workspace
     */
    public function getAllDataForWorkspace($workspace_id)
    {
        $workspaces = $this->getWorkspaces();

        foreach($workspaces as $workspace)
        {
            if ($workspace->getId() == $workspace_id)
            {
                $clients = $this->getClients($workspace_id);
                foreach($clients as $client)
                {
                    $projects = $this->getProjects($client->getId(), $workspace_id);
                    $client->setProjects($projects);
                }
                $workspace->setClients($clients);

                return $workspace;
            }
        }

        return false;
    }
}