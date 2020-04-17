<?php

namespace App\Parse_mods\offence_stats;

use App\Parse_mods\ModParse;
use App\Parse_mods\Stat;

class Radius_of_Area_Skills extends Stat
{
	private $parentMod = '% increased Radius of Area Skills';
    public function parse($mod, $type)
    {
        $this->setType($type);
        $this->name ='% Radius of Area Skills';
        $modValue = (int) filter_var($mod, FILTER_SANITIZE_NUMBER_INT);
        $modName = preg_replace('/\d+/u', '#', $mod);

        // From items // tree // jews
        if ( $modName === '#% increased Radius of Area Skills') {
            $this->setVal($modValue);
            $this->setItemVal($modValue, $mod);
        }

    }
}