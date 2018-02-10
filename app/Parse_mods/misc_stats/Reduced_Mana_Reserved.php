<?php

namespace App\Parse_mods\misc_stats;

use App\Parse_mods\ModParse;
use App\Parse_mods\Stat;

class Reduced_Mana_Reserved extends Stat
{
    public $note = '';
    public $mortalConviction = false;
	private $parentMod = '% reduced Mana Reserved';
    public function parse($mod, $type)
    {
        $this->setType($type);
        $this->name ='% reduced Mana Reserved';

        $modValue = filter_var($mod, FILTER_SANITIZE_NUMBER_INT);
        $modName = preg_replace('/\d+/u', '#', $mod);

        if ( $modName === '#% reduced Mana Reserved') {
            $this->setVal($modValue);
            $this->setItemVal($modValue, $mod);
        }

        //if Chaos Inoculation
        if ( $modName === '#% less Mana Reserved') {
            $this->mortalConviction = true;
            $this->note = '50% less Mana Reserved from Mortal Conviction passive tree point<br>';
        }
    }

}