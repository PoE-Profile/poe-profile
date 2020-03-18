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

       
        foreach ($gear as $item) {
            if (array_key_exists('socketedItems', $item)) {
                $skill = $skills->addChild('Skill');
                $skill->addAttribute('mainActiveSkillCalcs', 'nill');
                $skill->addAttribute('mainActiveSkill', 'nill');
                $skill->addAttribute('enabled', 'true');
                $skill->addAttribute('slot', $this->fixName($item['inventoryId']));

                foreach ($item['socketedItems'] as $g) {
                    if ($g['frameType'] != 4) {// skip if not Gems
                        continue;
                    }

                    $gemName = $g['typeLine'];
                    if (str_contains($gemName, 'Support')) {
                        $gemName = 'Support ' . str_replace(' Support', '', $gemName);

                        $skillId = str_replace(' ', '', $gemName);
                        $nameSpec = str_replace('Support ', '', $gemName);
                    } else {
                        $skillId = str_replace(' ', '', $gemName);
                        $nameSpec = $gemName;
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
                // dd();
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
            return 'Weapon 1 Swap';
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
