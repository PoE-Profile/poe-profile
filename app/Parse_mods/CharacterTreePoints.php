<?php

namespace App\Parse_mods;
use App\Parse_mods\Jewels;
use App\Parse_mods\TreePoint;

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
        $this->apiChanges();
    }

    public function getPoints($tree)
    {
        $charTree = [];
        $jewelsObj = new Jewels;
        $jewels = $jewelsObj->addJewels($tree['items']);

        foreach ($jewels as $jewel) { 
            if (str_contains($jewel['slot']['typeLine'], 'Cluster') || !array_key_exists('id',$jewel)) {
                continue;
            }
            if ( in_array($jewel['id'], $tree['hashes']) ) {
                $charTree[] = [
                    'type' => 'jewel',
                    'mods' => $jewel['slot']['explicitMods']
                ];
            }
        }

        foreach ($tree['hashes'] as $id) {
            if (!property_exists($this->allPoints, $id)) {
                return [];
            }

            $treePoint = new TreePoint($this->allPoints->{$id}, $this->version);

            $charTree[] = [
                'type' => 'tree',
                'mods' => $jewelsObj->uniqueJewels($treePoint)
            ];
        }

        return $charTree;
    }

    public function apiChanges()
    {
        if ($this->version == '3_2') {
            $newPoints = (object) [];
            foreach ($this->allPoints as $point) {
                $newPoints->{$point->id} = $point;
            }
            $this->allPoints = $newPoints;
        }
    }
}