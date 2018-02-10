<?php

namespace App\Parse_mods\misc_stats;

use App\Parse_mods\ModParse;
use App\Parse_mods\Stat;

class Totem_Physical_Reduction extends Stat
{
	private $parentMod = '% Totem additional Physical Damage Reduction';
    public function parse($mod, $type)
    {
        $this->setType($type);
        $this->name ='% Totem Physical Reduction';
        $modValue = filter_var($mod, FILTER_SANITIZE_NUMBER_INT);
        $modName = preg_replace('/\d+/u', '#', $mod);

        if ( $modName === 'Totems have #% additional Physical Damage Reduction') {
            $this->setVal($modValue);
            $this->setItemVal($modValue, $mod);
        }
    }
}