<?php

namespace Pff\TaskSync\Data;

class Client
{
    /**
     * @var string
     */
    private $id;

    /**
     * @var string
     */
    private $name;

    /**
     * @var Project[]
     */
    private $projects;

    /**
     * @var bool
     */
    private $synced;

    public function __construct($id = null, $name = null, $synced = true)
    {
        $this->id = $id;
        $this->name = $name;
        $this->synced = $synced;
        $this->projects = array();
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setProjects($projects)
    {
        $this->projects = $projects;
    }

    public function getProjects()
    {
        return $this->projects;
    }

    public function addProject(Project $project)
    {
        $this->projects[] = $project;
    }

    public function __toString()
    {
        return $this->id.'-'.$this->name;
    }

    public function toArray()
    {
        $out = array(
            'id' => $this->id,
            'name' => $this->name,
            'projects' => array(),
        );

        foreach($this->projects as $project)
            $out['projects'][] = $project->toArray();

        return $out;
    }

    public function findProjectByName($name)
    {
        foreach($this->projects as $project)
        {
            if ($project->getName() == $name)
                return $project;
        }

        return false;
    }

    /**
     * @param boolean $synced
     */
    public function setSynced($synced)
    {
        $this->synced = $synced;
        if ($synced == false)
        {
            foreach($this->projects as $project)
                $project->setSynced($synced);
        }
    }

    /**
     * @return boolean
     */
    public function getSynced()
    {
        return $this->synced;
    }
}