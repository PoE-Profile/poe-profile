<?php
namespace App\Helpers;

use Sunra\PhpSimple\HtmlDomParser;

class LadderStats
{
    private $ladder_char;
    private $records=null;
    public function __construct($char)
    {
        $this->ladder_char=$char;
        if($char->stats!=null)
            $this->records=collect($char->stats['records']);

    }

    public static function char($char)
    {
        return (new static($char));
    }

    public function getData($new_char_info)
    {
        if ($this->ladder_char->online) {
            $this->clearOldRecords();
            $this->addNewRecord($new_char_info);

            $xph = $this->records->last()['exp'] - $this->records->first()['exp'];
            $ranks = $this->records->last()['rank'] - $this->records->first()['rank'];
            return [
                'records' => $this->records,
                'xph' => $this->number_format_short($xph),
                'ranks' => $this->ranks_format($ranks),
            ];
        }
        return $this->ladder_char->stats;
    }

    private function clearOldRecords(){
        if(!$this->records){
            return;
        }
        $this->records = $this->records->filter(function ($item) {
            return $item['time'] > now()->subMinutes(62)->timestamp;
        });
    }

    private function addNewRecord($new_record){
        $new = [
            'exp' => $new_record['experience'],
            'rank' => $new_record['rank'],
            'time' => now()->timestamp
        ];
        if(!$this->records){
            $this->records = collect([$new]);
            return;
        }
        $this->records->push($new);
    }

    function ranks_format($rank)
    {
        if ($rank === 0) {
            return '';
        }
        return $rank < 0 ? ''.$rank : '+'.$rank;
    }

    function number_format_short($n, $precision = 2)
    {
        if ($n < 900) {
        // 0 - 900
            $n_format = number_format($n, $precision);
            $suffix = '';
        } else if ($n < 900000) {
        // 0.9k-850k
            $n_format = number_format($n / 1000, $precision);
            $suffix = 'K';
        } else if ($n < 900000000) {
        // 0.9m-850m
            $n_format = number_format($n / 1000000, $precision);
            $suffix = 'M';
        } else if ($n < 900000000000) {
        // 0.9b-850b
            $n_format = number_format($n / 1000000000, $precision);
            $suffix = 'B';
        } else {
        // 0.9t+
            $n_format = number_format($n / 1000000000000, $precision);
            $suffix = 'T';
        }
        // Remove unecessary zeroes after decimal. "1.0" -> "1"; "1.00" -> "1"
        // Intentionally does not affect partials, eg "1.50" -> "1.50"
        if ($precision > 0) {
            $dotzero = '.' . str_repeat('0', $precision);
            $n_format = str_replace($dotzero, '', $n_format);
        }
        return $n_format . $suffix;
    }
}
