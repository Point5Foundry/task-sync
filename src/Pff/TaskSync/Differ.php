<?php

namespace Pff\TaskSync;

use Pff\TaskSync\Data\Workspace as Workspace;
use Pff\TaskSync\Data\Client as Client;
use Pff\TaskSync\Data\Project as Project;

class Differ
{
    /**
     * @param Data\Workspace $from
     * @param Data\Workspace $to
     * @return Data\Client[]
     */
    public function compare(Workspace $from, Workspace $to)
    {
        $diff = array();

        foreach($from->getClients() as $client)
        {
            $current_client = $to->findClientByName($client->getName());

            if ($current_client)
            {
                $change = $this->findChangedProjects($client, $current_client);
                if (count($change) > 0)
                {
                    $client->setSynced(false);
                    $client->setProjects($change);
                    $diff[] = $client;
                    continue;
                }
            } else {
                $client->setSynced(false);
                $diff[] = $client;
            }
        }

        return $diff;
    }

    /**
     * @param Data\Client $from
     * @param Data\Client $to
     * @return Data\Project[]
     */
    protected function findChangedProjects(Client $from, Client $to)
    {
        $diff = array();

        foreach($from->getProjects() as $project)
        {
            $current_project = $to->findProjectByName($project->getName());
            if ($current_project)
            {
                $change = $this->findChangedTasks($project, $current_project);
                if (count($change) > 0)
                {
                    $project->setTasks(array());
                    $project->setSynced(false);
                    $project->setTasks($change);
                    $diff[] = $project;
                }
            } else {
                $project->setSynced(false);
                $diff[] = $project;
            }
        }

        return $diff;
    }

    /**
     * @param Data\Project $from
     * @param Data\Project $to
     * @return Data\Task[]
     */
    protected function findChangedTasks(Project $from, Project $to)
    {
        $diff = array();

        foreach($from->getTasks() as $task)
        {
            $current_task = $to->findTaskByName($task->getName());
            if (!$current_task)
            {
                $task->setSynced(false);
                $diff[] = $task;
            }
        }

        return $diff;
    }
}