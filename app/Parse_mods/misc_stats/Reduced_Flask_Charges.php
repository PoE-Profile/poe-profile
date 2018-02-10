<?php

namespace App\Parse_mods\misc_stats;

use App\Parse_mods\ModParse;
use App\Parse_mods\Stat;

class Reduced_Flask_Charges extends Stat
{
	private $parentMod = '% reduced Flask Charges used';
    public function parse($mod, $type)
    {
        $this->setType($type);
        $this->name ='% reduced Flask Charges';

        $modValue = filter_var($mod, FILTER_SANITIZE_NUMBER_INT);
        $modName = preg_replace('/\d+/u', '#', $mod);

        if ( $modName === '#% reduced Flask Charges used') {
            $this->setVal($modValue);
            $this->setItemVal($modValue, $mod);
        }
    }

}