<?php

namespace App\Parse_mods;

class TreePoint 
{
    public $data;
    public $version;

    public $id;
    public $stats;
    public $keystone;

    public function __construct($data, $version)
    {
        $this->data = $data;
        $this->version = $version;
        $this->keystone = false;
        $this->apiChanges();
    }

    public function apiChanges() 
    {
        
        $version = explode('_', $this->version);
        // current main version is 3.X so we are looking at [1]
        if (in_array($version[1], range(2, 9))) {
            $this->id = $this->data->id;
            $this->stats = $this->data->sd;
            $this->keystone = $this->data->ks;
        } elseif ($version[1] >= 10) {
            $this->id = $this->data->skill;
            $this->stats = $this->data->stats;
            if (property_exists($this->data, 'isKeystone')) {
                $this->keystone = $this->data->isKeystone;
            }
        }
    }
}
