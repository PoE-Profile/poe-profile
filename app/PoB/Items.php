<?php

namespace App\PoB;

class Items
{
    public $xml = '';
    public $items = '';
    public $treeJewels = [];

    public function __construct($xml, $items)
    {
        $this->items = $items;
        $this->xml = $xml;

        $this->addItems();
    }

    public function addItems()
    {
        // Items
        if (isset($this->xml->Items)) {
            $Items = $this->xml->Items;
        } else {
            $Items = $this->xml->addChild('Items', PHP_EOL);
            $Items->addAttribute('activeItemSet', '1');
            $Items->addAttribute('useSecondWeaponSet', 'nil');
        }
        

        $rarity = ['NORMAL', 'MAGIC', 'RARE', 'UNIQUE'];
        //Add Gems from Items
        $gear = $this->items;
        $slots =[];
        $numberOfItems = count($gear);
        foreach ($gear as $key => $item) {
            
            $slots[] = $this->fixName($item);
            $itemString = "";
            $itemString = 'Rarity: '.$rarity[$item['frameType']].PHP_EOL;
            if ($item['name'] != '') {
                $itemString .= ltrim(str_replace('<<set:MS>><<set:M>><<set:S>>', "", $item['name'])).PHP_EOL;
            }
            if ($item['typeLine'] != '') {
                $itemString .= ltrim(str_replace('<<set:MS>><<set:M>><<set:S>>', "", $item['typeLine'])).PHP_EOL;
            }
            $itemString .= 'ID: '.$item['id'].PHP_EOL;

            if (array_key_exists('requirements', $item) && $item['requirements'][0]['name'] == 'Level') {
                $itemString .= 'Item Level: '.$item['requirements'][0]['values'][0][0].PHP_EOL;
            }

            if (array_key_exists('properties', $item) && $item['properties'][0]['name'] == 'Quality') {
                $testRep = array(
                    '%' => '',
                    '+' => ''
                );
                $itemString .= 'Quality: '. $this->strReplaceAssoc($testRep, $item['properties'][0]['values'][0][0]).PHP_EOL;
            }

            if (array_key_exists('sockets', $item)) {
                $colors = '';
                $lastElement = count($item['sockets']);

                foreach ($item['sockets'] as $k => $value) {
                    $between = ($lastElement !== $k+1) ? '-' : '';
                    $colors .= $value['sColour'].$between;
                }
                $itemString .= 'Sockets: '. $colors.PHP_EOL;
            }
            
            $countImplicts = (array_key_exists('implicitMods', $item)) ? count($item['implicitMods']) : 0;
            $itemString .= 'Implicits: '.$countImplicts.PHP_EOL;

            $itemString .= (array_key_exists('implicitMods', $item)) ? $item['implicitMods'][0].PHP_EOL : '';

            if (array_key_exists('explicitMods', $item)) {
                foreach ($item['explicitMods'] as $mod) {
                    $itemString .= $mod.PHP_EOL;
                }
            }

            if ((array_key_exists('craftedMods', $item))) {
                $itemString .= '{crafted}'.$item['craftedMods'][0].PHP_EOL;
            }

            $itt = $Items->addChild('Item', PHP_EOL.$itemString);
            $itt->addAttribute('id', $key+1);
        }
        $this->addAbyssJewels($Items);

        foreach ($slots as $key => $val) {
            if ($val == 'PassiveJewels') {
                continue;
            }

            $slot = $Items->addChild('Slot');
            $slot->addAttribute('name', $val);
            $slot->addAttribute('itemId', $key+1);
        }
    }

    private function addAbyssJewels($items)
    {
        $itemsWithAbyssSockes = ['BodyArmour', 'Belt', 'Helm', 'Boots', 'Gloves'];
        $filtered = array_filter($this->items, function ($val) use ($itemsWithAbyssSockes) {
            return in_array($val['inventoryId'], $itemsWithAbyssSockes);
        });

        $abyssArr = [];
        foreach ($filtered as $item) {
            if (array_key_exists('socketedItems', $item)) {
                $k = 0;
                foreach ($item['socketedItems'] as $i) {
                    if ($i['properties'][0]['name'] === 'Abyss') {
                        $k++;
                        $slotName = $this->fixName($item) . ' Abyssal Socket ' . $k;
                        $abyssArr[$slotName] = $i;
                    }
                }
            }
        }
        
        $rarity = ['NORMAL', 'MAGIC', 'RARE', 'UNIQUE'];
        $nextItem = count($items);
        foreach ($abyssArr as $slotName => $item) {
            $nextItem++;
            $itemString = "";
            $itemString = 'Rarity: '.$rarity[$item['frameType']].PHP_EOL;
            if ($item['name'] !== "") {
                $itemString .= ltrim(str_replace('<<set:MS>><<set:M>><<set:S>>', "", $item['name'])).PHP_EOL;
            }
            if ($item['typeLine'] !== "") {
                $itemString .= ltrim(str_replace('<<set:MS>><<set:M>><<set:S>>', "", $item['typeLine'])).PHP_EOL;
            }
            $itemString .= 'ID: '.$item['id'].PHP_EOL;

            if (array_key_exists('requirements', $item) && $item['requirements'][0]['name'] == 'Level') {
                $itemString .= 'Item Level: '.$item['requirements'][0]['values'][0][0].PHP_EOL;
            }

            if (array_key_exists('explicitMods', $item)) {
                foreach ($item['explicitMods'] as $mod) {
                    $itemString .= $mod.PHP_EOL;
                }
            }

            $itt = $items->addChild('Item', PHP_EOL.$itemString);
            $itt->addAttribute('id', $nextItem);
            $slot = $items->addChild('Slot');
            $slot->addAttribute('name', $slotName);
            $slot->addAttribute('itemId', $nextItem);
        }
    }

    public function getXML()
    {
        return $this->xml;
    }

    public function strReplaceAssoc(array $replace, $subject)
    {
        return str_replace(array_keys($replace), array_values($replace), $subject);
    }

    public function fixName($item)
    {
        $name = $item['inventoryId'];
        if ($name == 'Flask') {
            return $name . ' ' . (string)($item['x']+1);
        }

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
