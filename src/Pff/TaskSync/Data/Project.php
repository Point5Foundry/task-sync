<?php

namespace Pff\TaskSync\Data;

class Project
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
     * @var Task[]
     */
    private $tasks;

    /**
     * @var bool
     */
    private $synced;


    public function __construct($id = null, $name = null, $synced = true)
    {
        $this->id = $id;
        $this->name = $name;
        $this->synced = $synced;
        $this->tasks = array();
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

    public function setTasks($tasks)
    {
        $this->tasks = $tasks;
    }

    public function getTasks()
    {
        return $this->tasks;
    }

    public function addTask(Task $task)
    {
        $this->tasks[] = $task;
    }

    public function __toString()
    {
        return $this->id.'-'.$this->getName();
    }

    public function toArray()
    {
        $out = array(
            'id' => $this->id,
            'name' => $this->name,
            'tasks' => array(),
        );

        foreach($this->tasks as $task)
            $out['tasks'][] = $task->__toString();

        return $out;
    }

    public function findTaskByName($name)
    {
        foreach($this->tasks as $task)
        {
            if ($task->getName() == $name)
                return $task;
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
            foreach($this->tasks as $task)
                $task->setSynced($synced);
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