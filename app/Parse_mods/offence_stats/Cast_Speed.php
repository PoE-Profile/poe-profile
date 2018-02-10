<?php

namespace App\Parse_mods\offence_stats;

use App\Parse_mods\ModParse;
use App\Parse_mods\Stat;

class Cast_Speed extends Stat
{
	private $parentMod = '% increased Cast Speed';
    public $note = '';
    public function parse($mod, $type)
    {
        $this->setType($type);
        $this->name ='% Cast Speed';
        $modValue = filter_var($mod, FILTER_SANITIZE_NUMBER_INT);
        $modName = preg_replace('/\d+/u', '#', $mod);

        if ( $modName === '#% increased Cast Speed') {
            $this->setVal($modValue);
            $this->setItemVal($modValue, $mod);
        }

        if ( $modName === '#% increased Attack and Cast Speed') {
            $this->setVal($modValue);
        }
    }
}