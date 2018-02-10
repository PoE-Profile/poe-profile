<?php

namespace App\PoB;

class Skills
{
    public $xml = '';
    public $items = '';

    public function __construct($xml, $items)
    {
        $this->items = $items;
        $this->xml = $xml;

        $this->addSkills();
    }

    public function addSkills()
    {
        // Skills
        $skills = $this->xml->addChild('Skills');
        $skills->addAttribute('defaultGemQuality', 'nil');
        $skills->addAttribute('defaultGemLevel', 'nil');
        $skills->addAttribute('sortGemsByDPS', 'true');

        //Add Gems from Itemss
        $gear = $this->items;

        $gemsArr = [];
        $handle = fopen('../public/jsons/gems.txt', "r");
        while (($line = fgets($handle)) !== false) {
            $gemsArr[] = ['nameSpec' => explode("|", $line)[0], 'skillId' => explode("|", $line)[1] ];
        }
        fclose($handle);
        foreach ($gear as $item) {
            if (array_key_exists('socketedItems', $item)) {
                $skill = $skills->addChild('Skill');
                $skill->addAttribute('mainActiveSkillCalcs', 'nill');
                $skill->addAttribute('mainActiveSkill', 'nill');
                $skill->addAttribute('enabled', 'true');
                $skill->addAttribute('slot', $this->fixName($item['inventoryId']));

                foreach ($item['socketedItems'] as $g) {
                    $skillId = '';
                    $nameSpec = '';
                    foreach ($gemsArr as $gg) {
                        if (strpos($g['typeLine'], $gg['nameSpec']) !== false) {
                            $skillId = $gg['skillId'];
                            $nameSpec = $gg['nameSpec'];
                        }
                    }

                    $quality = '0';
                    $level = '1';
                    foreach ($g['properties'] as $prop) {
                        if ($prop['name'] == 'Level') {
                            $level = preg_replace("/[^0-9]/", "", $prop['values'][0][0]);
                        }
                        if ($prop['name'] == 'Quality') {
                            $level = preg_replace("/[^0-9]/", "", $prop['values'][0][0]);
                        }
                    }

                    $gem = $skill->addChild('Gem');
                    $gem->addAttribute('level', $level);
                    $gem->addAttribute('skillId', $skillId);
                    $gem->addAttribute('quality', $quality);
                    $gem->addAttribute('enabled', 'true');
                    $gem->addAttribute('nameSpec', $nameSpec);
                }
            }
        }
    }


    public function getXML()
    {
        return $this->xml;
    }

    public function fixName($name)
    {
        $arr = ['Weapon', 'Ring'];
        if (in_array($name, $arr)) {
            return $name.' 1';
        }

        if ($name == 'Weapon2') {
            return 'Weapon 2';
        }

        if ($name == 'Ring2') {
            return 'Ring 2';
        }

        if ($name == 'Offhand') {
            return 'Weapon 2';
        }

        if ($name == 'Offhand2') {
            return 'Weapon 2 Swap';
        }

        if ($name == 'Offhand2') {
            return 'Weapon 2 Swap';
        }

        if ($name == 'Helm') {
            return 'Helmet';
        }

        if ($name == 'BodyArmour') {
            return 'Body Armour';
        }
        return $name;
    }
}
