<?php

namespace App\Parse_mods\misc_stats;

use App\Parse_mods\ModParse;
use App\Parse_mods\Stat;

class Quantity extends Stat
{
	private $parentMod = '% increased Quantity';
    public function parse($mod, $type)
    {
        $this->setType($type);
        $this->name ='% Quantity';
        $modValue = (int) filter_var($mod, FILTER_SANITIZE_NUMBER_INT);
        $modName = preg_replace('/\d+/u', '#', $mod);
        
        if ( $modName === '#% increased Quantity of Items found') {
            $this->setVal($modValue);
            $this->setItemVal($modValue, $mod);
        }

    }

}