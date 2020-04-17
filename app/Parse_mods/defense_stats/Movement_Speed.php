<?php

namespace App\Parse_mods\defense_stats;

use App\Parse_mods\ModParse;
use App\Parse_mods\Stat;

class Movement_Speed extends Stat
{
	private $parentMod = '% increased Movement Speed';
    public function parse($mod, $type)
    {
        $this->setType($type);
        $this->name ='% Movement Speed';
        $modValue = (int) filter_var($mod, FILTER_SANITIZE_NUMBER_INT);
        $modName = preg_replace('/\d+/u', '#', $mod);

        if ( $modName === '#% increased Movement Speed') {
            $this->setVal($modValue);
            $this->setItemVal($modValue, $mod);
        }

    }

}