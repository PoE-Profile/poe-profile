<?php

namespace App\Parse_mods\offence_stats;

use App\Parse_mods\ModParse;
use App\Parse_mods\Stat;

class Elemental_Damage extends Stat
{
    private $parentMod = '% increased Elemental Damage';
    private $grand_spectrum = [
        'count' => 0,
        'val' => 0
    ];

    public function parse($mod, $type)
    {
        $this->setType($type);
        $this->name ='% Elemental Damage';
        $modValue = filter_var($mod, FILTER_SANITIZE_NUMBER_INT);
        $modName = preg_replace('/\d+/u', '#', $mod);

        // check for other Grand Spectrum jewels
        if (strpos($mod, 'Grand Spectrum') !== false) {
            $this->grand_spectrum['count'] += 1;
        }

        if ($modName === '#% increased Elemental Damage per Grand Spectrum') {
            $this->grand_spectrum['val'] += $modValue;
        }

        // From items // tree // jews
        if ( $modName === '#% increased Elemental Damage') {
            $this->setVal($modValue);
            $this->setItemVal($modValue, $mod);
        }
        $this->setJewVal($this->grand_spectrum);
        $this->total = $this->itemVal + $this->treeVal + $this->jewVal;
    }

    public function setJewVal($gs)
    {
        $this->jewVal = $gs['val'] * $gs['count'];
    }

}