<?php

namespace App\Parse_mods\defense_stats;

use App\Parse_mods\ModParse;
use App\Parse_mods\Stat;

class Block extends Stat
{
    public $note = '';
    private $br = '';

    public $acrobatics = false;
	private $parentMod = '% Chance to Block';
    public function parse($mod, $type)
    {
        if ($this->note !== '') {
            $this->br = '<br>';
        }
        
        $this->setType($type);
        $this->name ='% Block Chance';
        $modValue = (int) filter_var($mod, FILTER_SANITIZE_NUMBER_INT);
        $modName = preg_replace('/\d+/u', '#', $mod);

        if (\Str::contains($mod, '50% less Armour and Energy Shield')) {
            $this->acrobatics = true;
            $this->note = $this->note . $this->br .'30% less Block from Acrobatics Keystone';
        }


        if ( $modName === '#% Chance to Block') {
            $this->setVal($modValue);
        }
    }

    public function finalCalcualtion($allStats){
        if ($this->acrobatics) {
            $this->total = $this->total - round($this->total*0.3);
        }
     }

}