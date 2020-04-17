<?php

namespace App\Parse_mods\misc_stats;

use App\Parse_mods\ModParse;
use App\Parse_mods\Stat;

class Flask_Charges_Gained extends Stat
{
	private $parentMod = '% increased Flask Charges gained';
    public function parse($mod, $type)
    {
        $this->setType($type);
        $this->name ='% Flask Charges gained';

        $modValue = (int) filter_var($mod, FILTER_SANITIZE_NUMBER_INT);
        $modName = preg_replace('/\d+/u', '#', $mod);

        if ( $modName === '#% increased Flask Charges gained') {
            $this->setVal($modValue);
            $this->setItemVal($modValue, $mod);
        }
    }

}