<?php

namespace App\Parse_mods;
use App\Parse_mods\Jewels;

class CharacterTreePoints
{
    public $allPoints = '';
    public $version = '';

    public function loadJSON($version)
    {
        $this->version = str_replace(".", "_", $version);
        $path = app_path('Parse_mods/tree_versions/'.$this->version.'.json');
        if (!\File::exists($path)) {
            throw new \Exception("Invalid File");
        }

        $file = \File::get($path); // string
        $this->allPoints = json_decode($file);
    }

    public function getPoints($tree)
    {      
        $charTree = [];

        $jewelsObj = new Jewels;
        $jewels = $jewelsObj->addJewels($tree['items']);
        

        foreach ($jewels as $jewel) { 
            if ( in_array($jewel['id'], $tree['hashes']) ) {
                $charTree[] = [
                    'type' => 'jewel',
                    'mods' => $jewel['slot']['explicitMods']
                ];
            }
        }


        $version = (float) str_replace("_", ".", $this->version);
        if ($version > 3.2) {
            foreach ($tree['hashes'] as $id) {
                if (!property_exists($this->allPoints, $id)) {
                    return [];
                }
                $charTree[] = [
                    'type' => 'tree',
                    'mods' => $jewelsObj->uniqueJewels($this->allPoints->{$id})->sd
                ];
            }
        } else {
            foreach ($this->allPoints as $point) {
                if (in_array($point->id, $tree['hashes'])) {
                    $charTree[] = [
                        'type' => 'tree',
                        'mods' => $jewelsObj->uniqueJewels($point)->sd
                    ];
                }
            }
        }
        
        

        return $charTree;
    }
}