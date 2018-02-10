<?php

namespace App\Parse_mods\misc_stats;

use App\Parse_mods\ModParse;
use App\Parse_mods\Stat;

class Totem_Life extends Stat
{
	private $parentMod = '% increased Totem Life';
    public function parse($mod, $type)
    {
        $this->setType($type);
        $this->name ='% Totem Life';
        $modValue = filter_var($mod, FILTER_SANITIZE_NUMBER_INT);
        $modName = preg_replace('/\d+/u', '#', $mod);

        if ( $modName === '#% increased Totem Life') {
            $this->setVal($modValue);
            $this->setItemVal($modValue, $mod);
        }
    }
}