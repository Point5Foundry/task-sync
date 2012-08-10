<?php

namespace Pff\TaskSync\Data;

class Task
{
    private $id;
    private $name;
    private $completed;
    private $synced;

    public function __construct($id = null, $name = null, $completed = false, $synced = true)
    {
        $this->id = $id;
        $this->name = $name;
        $this->synced = $synced;
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

    public function __toString()
    {
        return $this->id.'-'.$this->name;
    }

    public function setSynced($synced)
    {
        $this->synced = $synced;
    }

    public function getSynced()
    {
        return $this->synced;
    }

    public function setCompleted($completed)
    {
        $this->completed = $completed;
    }

    public function getCompleted()
    {
        return $this->completed;
    }
}