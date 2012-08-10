<?php

namespace Pff\TaskSync\Data;

class Workspace
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
     * @var bool
     */
    private $synced;

    /**
     * @var Client[]
     */
    private $clients;

    public function __construct($id = null, $name = null, $synced = true)
    {
        $this->id = $id;
        $this->name = $name;
        $this->synced = $synced;
        $this->clients = array();
    }

    public function setClients($clients)
    {
        $this->clients = $clients;
    }

    public function getClients()
    {
        return $this->clients;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getId()
    {
        return $this->id;
    }

    public function addClient(Client $client)
    {
        $this->clients[] = $client;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
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
            'clients' => array(),
        );

        foreach($this->clients as $client)
            $out['clients'][] = $client->toArray();

        return $out;
    }

    public function findClientByName($name)
    {
        foreach($this->clients as $client)
        {
            if ($client->getName() == $name)
                return $client;
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
            foreach($this->clients as $client)
                $client->setSynced($synced);
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