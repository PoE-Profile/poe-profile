<?php

namespace App\Parse_mods\offence_stats;

use App\Parse_mods\ModParse;
use App\Parse_mods\Stat;

class Trap_Throwing_Speed extends Stat
{
	private $parentMod = '% increased Trap Throwing Speed';
    public function parse($mod, $type)
    {
        $this->setType($type);
        $this->name ='% Trap Throwing Speed';
        $modValue = (int) filter_var($mod, FILTER_SANITIZE_NUMBER_INT);
        $modName = preg_replace('/\d+/u', '#', $mod);

        // From items // tree // jews
        if ( $modName === '#% increased Trap Throwing Speed') {
            $this->setVal($modValue);
            $this->setItemVal($modValue, $mod);
        }

    }

}