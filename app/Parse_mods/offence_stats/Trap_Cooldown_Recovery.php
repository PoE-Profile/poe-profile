<?php

namespace App\Parse_mods\offence_stats;

use App\Parse_mods\ModParse;
use App\Parse_mods\Stat;

class Trap_Cooldown_Recovery extends Stat
{
	private $parentMod = '% increased Cooldown Recovery Speed for throwing Traps';
    public function parse($mod, $type)
    {
        $this->setType($type);
        $this->name = '% Traps Recovery Speed';
        $modValue = filter_var($mod, FILTER_SANITIZE_NUMBER_INT);
        $modName = preg_replace('/\d+/u', '#', $mod);

        // From items // tree // jews
        if ( $modName === '#% increased Cooldown Recovery Speed for throwing Traps') {
            $this->setVal($modValue);
            $this->setItemVal($modValue, $mod);
        }

    }

}