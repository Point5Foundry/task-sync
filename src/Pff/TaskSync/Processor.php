<?php

namespace Pff\TaskSync;

class Processor
{
    /** @var ProviderInterface */
    private $from;

    /** @var ProviderInterface */
    private $to;

    /** @var Differ */
    private $differ;

    public function __construct(ProviderInterface $from, ProviderInterface $to, Differ $differ)
    {
        $this->from = $from;
        $this->to = $to;
        $this->differ = $differ;
    }

    public function setFrom(ProviderInterface $from)
    {
        $this->from = $from;
    }

    public function setTo(ProviderInterface $to)
    {
        $this->to = $to;
    }

    public function diffWorkspace($from_workspace_id, $to_workspace_id)
    {
        $from = $this->from->getAllDataForWorkspace($from_workspace_id);
        $to = $this->to->getAllDataForWorkspace($to_workspace_id);

        $diff = $this->differ->compare($from, $to);

        return $diff;
    }

    public function saveDiff($workspace_id, $diff)
    {
        /** @var $client Data\Client */
        foreach($diff as $client)
        {
            $client_id = $client->getId();
            if ($client->getSynced() == false)
            {
                $new_client = $this->to->createClient($workspace_id, $client->getName(), $client->getId());
                while ($new_client == false)
                {
                    $new_client = $this->to->createClient($workspace_id, $client->getName(), $client->getId());
                }
                $client_id = $new_client->getId();
            }

            foreach($client->getProjects() as $project)
            {
                $new_project = $this->to->createProject($workspace_id, $client_id, $project->getName(), $project->getId());
                while ($new_project == false)
                {
                    $new_project = $this->to->createProject($workspace_id, $client_id, $project->getName(), $project->getId());
                }
            }
        }
    }

    public function listWorkspaces()
    {
        $this->displayWorkspaces('From', $this->from->getWorkspaces());
        $this->displayWorkspaces('To', $this->to->getWorkspaces());
    }

    public function displayWorkspaces($name, $workspaces)
    {
        echo $name.':'."\n";
        foreach($workspaces as $workspace)
            echo '['.$workspace->getId().'] '.$workspace->getName()."\n";
        echo "\n\n";
    }
}