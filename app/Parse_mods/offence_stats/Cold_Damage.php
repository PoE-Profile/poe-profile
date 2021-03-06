<?php

namespace App\Parse_mods\offence_stats;


use App\Parse_mods\ModParse;
use App\Parse_mods\Stat;

class Cold_Damage extends Stat
{
	private $parentMod = '% increased Cold Damage';
    public function parse($mod, $type)
    {
        $this->setType($type);
        $this->name ='% Cold Damage';
        $modValue = (int) filter_var($mod, FILTER_SANITIZE_NUMBER_INT);
        $modName = preg_replace('/\d+/u', '#', $mod);

        // From items // tree // jews
        if ( $modName === '#% increased Cold Damage') {
            $this->setVal($modValue);
            $this->setItemVal($modValue, $mod);
        }

    }

}