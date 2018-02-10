<?php

namespace App\Parse_mods;


class TakeMods
{
    public $explMods = [];
    public $implMods = [];
    public $crafMods = [];
    public $props = ['Evasion Rating' => '', 'Energy Shield' => '' , 'Armour' => ''];
    public $banItems = ['Flask','Weapon2', 'Offhand2'];

    public function getMods($items)
    {
        foreach ($items as $item) {
            if (!in_array($item['inventoryId'], $this->banItems)) {

               if ( isset($item["explicitMods"]) ) {
                    foreach ($item['explicitMods'] as $mod) {
                        $val = filter_var($mod, FILTER_SANITIZE_NUMBER_INT);
                        $this->explMods[] = [
                           preg_replace('/\d+/u', '#', $mod) => $val,
                        ];
                    }
                }

                if ( isset($item['implicitMods']) ){
                    $impMod =preg_replace('/\d+/u', '#', $item['implicitMods'][0]);
                    $val = filter_var($item['implicitMods'][0], FILTER_SANITIZE_NUMBER_INT);
                    $this->implMods[$impMod] = $val;
                }

                if ( isset($item['craftedMods']) ){
                    $crfMod =preg_replace('/\d+/u', '#', $item['craftedMods'][0]);
                    $val = filter_var($item['craftedMods'][0], FILTER_SANITIZE_NUMBER_INT);
                    $this->crafMods[$crfMod] = $val;
                }

                if ( isset($item['properties']) ){
                    foreach ($item['properties'] as $p) {
                        if (isset($p['name'])) {
                            switch ($p['name']) {
                                case 'Evasion Rating':
                                    $this->props['Evasion Rating'] += $p['values'][0][0];
                                    break;
                                case 'Energy Shield':
                                    $this->props['Energy Shield'] += $p['values'][0][0];
                                    break;
                                case 'Armour':
                                    $this->props['Armour'] += $p['values'][0][0];
                                    break;

                                default:
                                    # code...
                                    break;
                            }
                        }
                    }
                }

            }
        }

        $mods=array();
        //add explicit mods
        foreach($this->explMods as $value)
        {
            foreach($value as $key=>$secondValue)
            {
               if(!isset($mods[$key]))
                {
                   $mods[$key]=0;
                }
                $mods[$key]+=$secondValue;
            }
        }
        //add implicit mods
        foreach($this->implMods as $key=>$val)
        {
           if(!isset($mods[$key]))
            {
               $mods[$key]=0;
            }
            $mods[$key]+=$val;
        }

        //add crafted mods
        foreach($this->crafMods as $key=>$val)
        {
           if(!isset($mods[$key]))
            {
               $mods[$key]=0;
            }
            $mods[$key]+=$val;
        }

        //add this->props mods
        foreach($this->props as $key=>$val)
        {
           if(!isset($mods[$key]))
            {
               $mods[$key]=0;
            }
            $mods[$key]+=$val;
        }

        //filter mods so we can add props to es/armor/evasion mods
        $mods = $this->filterProps($mods);
        // dd($props);

        return $mods;
    }

    public function filterProps($mods)
    {
        $newProps = [
           '+# to maximum Energy Shield' =>  'Energy Shield',
           '+# to Evasion Rating' => 'Evasion Rating',
           '+# to Armour Rating' => 'Armour'
        ];

        foreach ($newProps as $key => $value) {
            $mods = $this->replace_key_function($mods, $key, $value);
        }

        return $mods;
    }

    function replace_key_function($array, $key1, $key2)
    {
        $keys = array_keys($array);
        $index = array_search($key1, $keys);

        if ($index !== false) {
            $array[$key2] += $array[$key1];
            $keys[$index] = $key2;
            $array = array_combine($keys, $array);
        }

        return $array;
    }
}