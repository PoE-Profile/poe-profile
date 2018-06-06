<?php

namespace App\Parse_mods\offence_stats;

use App\Parse_mods\ModParse;
use App\Parse_mods\Stat;

class Trap_Trigger_Area extends Stat
{
	private $parentMod = '% increased Trap Trigger Area of Effect';
    public function parse($mod, $type)
    {
        $this->setType($type);
        $this->name ='% Trap Trigger Area of Effect';
        $modValue = filter_var($mod, FILTER_SANITIZE_NUMBER_INT);
        $modName = preg_replace('/\d+/u', '#', $mod);

        // From items // tree // jews
        if ( $modName === '#% increased Trap Trigger Area of Effect') {
            $this->setVal($modValue);
            $this->setItemVal($modValue, $mod);
        }

    }

}