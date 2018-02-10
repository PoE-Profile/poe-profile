<?php

namespace App\Parse_mods\offence_stats;

use App\Parse_mods\ModParse;
use App\Parse_mods\Stat;

class Accuracy_Rating extends Stat
{

	private $parentMod = '% increased Accuracy Rating';
    public function parse($mod, $type)
    {
        $this->setType($type);
        $this->name ='% Accuracy Rating';
        $modValue = filter_var($mod, FILTER_SANITIZE_NUMBER_INT);
        $modName = preg_replace('/\d+/u', '#', $mod);

        if ( $modName === '#% increased Accuracy Rating') {
            $this->setVal($modValue);
            $this->setItemVal($modValue, $mod);
        }

    }

}