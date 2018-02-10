<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Foundation\Inspiring;
use App\Stash;

class Inspire extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'inspire';
    private $ids=[];
    private $json;
    private $api;

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Display an inspiring quote';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
      \Schema::table('items', function($table) {
            $table->softDeletes();
        });

      \Schema::table('mods', function($table) {
            $table->softDeletes();
      });

      $this->info('migration added collumns deleted_at');
      dd('done');
      ini_set('memory_limit', '-1');
        $this->comment(PHP_EOL.Inspiring::quote().PHP_EOL);
        $this->json = file_get_contents('http://www.pathofexile.com/api/public-stash-tabs?id=1735822-1818180-1713831-1978483-1875315');
        $this->api=json_decode($this->json);

        // proces json and add to db
        $this->parse($this->api);
        // dd($api->next_change_id);

        //get next id and redirect to slef
        // print_r($api->next_change_id);
        $this->comment($this->api->next_change_id);

        $this->log($this->api->next_change_id);
        $this->getNext($this->api->next_change_id);
    }

    private function getNext($id){
      $this->json = file_get_contents('http://www.pathofexile.com/api/public-stash-tabs?id='.$id);
      $this->api=json_decode($this->json);
      // proces json and add to db
      $this->parse($this->api);

      //get next id and redirect to slef
      //print_r($api->next_change_id);
      $this->comment($this->api->next_change_id);

      //$ids[]=$api->next_change_id;
      $this->log($this->api->next_change_id);
      $this->getNext($this->api->next_change_id);

      // unset($this->api);
      // unset($this->json);
    }

    private function log($id){
      $file = 'idLog.txt';
      // Open the file to get existing content
      $current = file_get_contents($file);
      // Append a new person to the file
      $current .= "id=$id\n";
      // Write the contents back to the file
      file_put_contents($file, $current);
    }

    private function parse($api){

      foreach ($api->stashes as $server_s) {
        $s = new Stash;
        if($server_s->accountName==null)
          continue;
        $s->accountName=$server_s->accountName;
        $s->lastCharacterName=$server_s->lastCharacterName;
        $s->stash=$server_s->stash;
        $s->poeStashId=$server_s->id;
        $s->save();
        //$s->addItems($server_s->items);
      }

    }

}
