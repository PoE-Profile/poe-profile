<?php

namespace App\Parse_mods\base_stats;

use App\Parse_mods\ModParse;
use App\Parse_mods\Stat;


class Intelligence extends Stat
{
    public $baseVal = 0;
    private $parentMod = ' to Intelligence';
    public $note = '';

    private $noteBase = '10 intelligence grants an additional 5 mana<br>10 intelligence grants 2% maximum energy shield';
    private $tempNote = '';
    private $intPercent = 0;
    private $associatedMods= [
        '+# to Intelligence',
        '+# to Strength and Intelligence',
        '+# to Dexterity and Intelligence',
        '+# to all Attributes'
    ];

    public function parse($mod, $type)
    {
        $this->setType($type);
        $this->name ='Intelligence';
        if ($this->note == '') {
            $this->note =  $this->noteBase;
        }
        $modValue = (int) filter_var($mod, FILTER_SANITIZE_NUMBER_INT);
        $modName = preg_replace('/\d+/u', '#', $mod);


        if ($this->currentType === 'base' && $modName === '+# to Intelligence') {
           $this->baseVal += $modValue;
        }

        if ( in_array($modName, $this->associatedMods) ) {
            $this->setVal($modValue);
            $this->setItemVal($modValue, $mod);
        }

        //if Percent Strength on items / tree
        if ($modName === '#% increased Intelligence' || $modName === '#% reduced Intelligence' || $modName === '#% increased Attributes') {
            $this->intPercent += \Str::contains($modName, 'reduced') ? $modValue*-1 : $modValue;
            $this->tempNote = '<br>Stat include ' . $this->intPercent . '% Intelligence';
            $this->note = $this->noteBase . $this->tempNote;
        }

        $this->total = $this->treeVal + $this->itemVal + $this->jewVal + $this->baseVal;
        $this->total = round((($this->total * $this->intPercent) / 100)) + $this->total;
    }

}