<?php

namespace Pff\TaskSync\Provider;

use Pff\TaskSync\ProviderInterface;
use Pff\TaskSync\Data as Data;

class Toggl implements ProviderInterface
{
    const HTTP_GET = 1;
    const HTTP_POST = 2;
    const HTTP_PUT = 3;

    private $api_key;
    private $api_version;

    public function __construct($api_key, $api_version = 'v6')
    {
        $this->api_key = $api_key;
        $this->api_version = $api_version;
    }

    public function request($command, $data = array(), $action = self::HTTP_GET)
    {
        $url = 'https://www.toggl.com/api/'.$this->api_version.'/'.$command.'.json';

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE); // Needed since Toggl's SSL fails without this.

        if ($action == self::HTTP_POST) {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type' => 'application/json'));
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
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

        curl_setopt($ch, CURLOPT_USERPWD, $this->api_key.':api_token');

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $content = curl_exec($ch);

        if (!$content)
            return $content;

        curl_close($ch);

        return json_decode($content);
    }


    /**
     * @return \Pff\TaskSync\Data\Workspace[]
     */
    public function getWorkspaces()
    {
        $response = $this->request('workspaces');

        if (!$response)
            return false;

        $workspaces = array();

        foreach($response->data as $ws)
        {
            $workspaces[] = new Data\Workspace($ws->id, $ws->name);
        }

        return $workspaces;
    }

    /**
     * @param null|int $workspace_id
     * @return \Pff\TaskSync\Data\Client[]
     */
    public function getClients($workspace_id = null)
    {
        $response = $this->request('clients');

        if (!$response)
            return false;

        $workspaces = array();

        foreach($response->data as $cli)
        {
            $client = new Data\Client($cli->id, $cli->name);
            $ws_id = $cli->workspace->id;
            if (!isset($workspaces[$ws_id]))
            {
                $workspaces[$ws_id] = new Data\Workspace($cli->workspace->id, $cli->workspace->name);
            }
            $workspaces[$ws_id]->addClient($client);
        }

        foreach($workspaces as $workspace)
        {
            if ($workspace->getId() == $workspace_id)
                return $workspace->getClients();
        }

        if ($workspace_id == null)
            return $workspaces;

        return false;
    }

    /**
     * @param null|int $client_id
     * @param null|int $workspace_id
     * @return \Pff\TaskSync\Data\Project[]
     */
    public function getProjects($client_id = null, $workspace_id = null)
    {
        $result = $this->request('projects');

        if (!$result)
            return false;

        $workspaces = array();
        $clients = array();

        foreach($result->data as $proj)
        {
            $project = new Data\Project($proj->id, $proj->name);
            if (!isset($workspaces[$proj->workspace->id]))
                $workspaces[$proj->workspace->id] = new Data\Workspace($proj->workspace->id, $proj->workspace->name);

            if (!isset($clients[$proj->client->id]))
            {
                $clients[$proj->client->id] = new Data\Client($proj->client->id, $proj->client->name);
                $workspaces[$proj->workspace->id]->addClient($clients[$proj->client->id]);
            }

            $clients[$proj->client->id]->addProject($project);
        }

        return $workspaces;
    }

    /**
     * @param null|int $project_id
     * @param null|int $client_id
     * @param null|int $workspace_id
     * @return \Pff\TaskSync\Data\Task[]
     */
    public function getTasks($project_id = null, $client_id = null, $workspace_id = null)
    {
        // TODO: Implement getTasks() method.
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
        $data = array(
            'client' => array(
                'name' => $name,
                'workspace' => array(
                    'id' => $workspace_id,
                )
            )
        );
        $result = $this->request('clients', $data, self::HTTP_POST);

        $c = new Data\Client($result->data->id, $name);

        return $c;
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
        $data = array(
            'project' => array(
                'name' => $name,
                'workspace' => array(
                    'id' => $workspace_id,
                ),
                'client' => array(
                    'id' => $client_id,
                ),
                'billable' => false,
                'is_private' => false,
            )
        );

        $result = $this->request('projects', $data, self::HTTP_POST);

        $p = new Data\Project($result->data->id, $name);

        return $p;
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


    /**
     * @param int $workspace_id
     * @return \Pff\TaskSync\Data\Workspace
     */
    public function getAllDataForWorkspace($workspace_id)
    {
        $workspaces = $this->getProjects(null, null);
        foreach($workspaces as $workspace)
        {
            if ($workspace->getId() == $workspace_id)
                return $workspace;
        }

        return false;
    }
}