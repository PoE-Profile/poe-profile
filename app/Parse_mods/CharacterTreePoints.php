<?php

namespace App\Parse_mods;
use App\Parse_mods\Jewels;

class CharacterTreePoints
{
    public function loadJSON($filename = '')
    {
        $path = base_path('nodesThree.json');
        if (!\File::exists($path)) {
            throw new \Exception("Invalid File");
        }

        $file = \File::get($path); // string
        return $file;
    }

    public function getPoints($p)
    {      
        $charTree = [];

        $jewelsObj = new Jewels;
        $jewels = $jewelsObj->addJewels($p['items']);
        
        foreach ($jewels as $jewel) { 
            if ( in_array($jewel['id'], $p['hashes']) ) {
                $charTree[] = [
                    'type' => 'jewel',
                    'mods' => $jewel['slot']['explicitMods']
                ];
            }
        }

        $treePoints = json_decode($this->loadJSON());
        foreach ($treePoints as $point) { 
            if ( in_array($point->id, $p['hashes']) ) {
                $charTree[] = [
                    'type' => 'tree',
                    'mods' => $jewelsObj->uniqueJewels($point)->sd
                ];
            } 
        }

        return $charTree;
    }
}