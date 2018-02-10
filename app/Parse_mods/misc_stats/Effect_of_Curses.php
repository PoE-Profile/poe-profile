<?php

namespace App\Parse_mods\misc_stats;

use App\Parse_mods\ModParse;
use App\Parse_mods\Stat;

class Effect_of_Curses extends Stat
{
	private $parentMod = '% increased Effect of your Curses';
    public function parse($mod, $type)
    {
        $this->setType($type);
        $this->name ='% Effect of Curses';

        $modValue = filter_var($mod, FILTER_SANITIZE_NUMBER_INT);
        $modName = preg_replace('/\d+/u', '#', $mod);

        if ( $modName === '#% increased Effect of your Curses') {
            $this->setVal($modValue);
            $this->setItemVal($modValue, $mod);
        }
    }

}