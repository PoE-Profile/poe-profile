<?php

namespace App\Parse_mods\base_stats;

use App\Parse_mods\ModParse;
use App\Parse_mods\Stat;


class Strength extends Stat
{
    public $baseVal = 0;
    private $parentMod = ' to Strength';
    public $note = '';
    private $noteBase = '10 strength grants an additional 5 life<br>10 strength grants 2% melee physical damage';
    private $tempNote = 0;
    private $strPercent = 0;
    private $associatedMods= [
        '+# to Strength',
        '+# to Strength and Dexterity',
        '+# to Strength and Intelligence',
        '+# to all Attributes',
    ];

    public function parse($mod, $type)
    {
        $this->setType($type);
        $this->name ='Strength';
        if ($this->note == '') {
            $this->note =  $this->noteBase;
        }
        $modValue = (int) filter_var($mod, FILTER_SANITIZE_NUMBER_INT);
        $modName = preg_replace('/\d+/u', '#', $mod);

        if ($this->currentType === 'base' && $modName === '+# to Strength') {
           $this->baseVal += $modValue;
        }

        if (in_array($modName , $this->associatedMods)) {
            $this->setVal($modValue);
            $this->setItemVal($modValue, $mod);
        }

        //if Percent Strength on items / tree
        if ($modName === '#% increased Strength' || $modName === '#% reduced Strength' || $modName === '#% increased Attributes') {
            $this->strPercent += $modValue;
            $this->tempNote = '<br>Stat include ' . $this->strPercent . '% Strength';
            $this->note = $this->noteBase . $this->tempNote;
        }

        $this->total = $this->treeVal + $this->itemVal + $this->jewVal + $this->baseVal;
        $this->total = round((($this->total * $this->strPercent) / 100)) + $this->total;
    }
}