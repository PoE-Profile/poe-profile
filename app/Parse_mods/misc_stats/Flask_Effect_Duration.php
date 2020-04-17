<?php

namespace App\Parse_mods\misc_stats;

use App\Parse_mods\ModParse;
use App\Parse_mods\Stat;

class Flask_Effect_Duration extends Stat
{
	private $parentMod = '% increased Flask effect duration';
    public function parse($mod, $type)
    {
        $this->setType($type);
        $this->name ='% Flask effect duration';

        $modValue = (int) filter_var($mod, FILTER_SANITIZE_NUMBER_INT);
        $modName = preg_replace('/\d+/u', '#', $mod);

        if ( $modName === '#% increased Flask effect duration') {
            $this->setVal($modValue);
            $this->setItemVal($modValue, $mod);
        }
    }

}