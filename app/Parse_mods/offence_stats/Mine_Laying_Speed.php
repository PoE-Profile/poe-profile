<?php

namespace App\Parse_mods\offence_stats;

use App\Parse_mods\ModParse;
use App\Parse_mods\Stat;

class Mine_Laying_Speed extends Stat
{
	private $parentMod = '% increased Mine Laying Speed';
    public function parse($mod, $type)
    {
        $this->setType($type);
        $this->name ='% Mine Laying Speed';
        $modValue = (int) filter_var($mod, FILTER_SANITIZE_NUMBER_INT);
        $modName = preg_replace('/\d+/u', '#', $mod);

        // From items // tree // jews
        if ( $modName === '#% increased Mine Laying Speed') {
            $this->setVal($modValue);
            $this->setItemVal($modValue, $mod);
        }

    }

}