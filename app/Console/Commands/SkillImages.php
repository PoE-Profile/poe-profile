<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Sunra\PhpSimple\HtmlDomParser;

class SkillImages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'poe:skills-images';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Download and save all Skill images';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        // Create DOM from URL
        $client = new \GuzzleHttp\Client([
            'base_uri' => 'https://pathofexile.gamepedia.com/',
            'timeout'  => 5.0,
        ]);
        $response = $client->request('GET', '/Category:Skill_icons')->getBody()->getContents();
        $responseTwo = $client->request('GET', 'index.php?title=Category:Skill_icons&filefrom=Tornado+Shot+skill+icon.png#mw-category-media')->getBody()->getContents();


        $firstPage = HtmlDomParser::str_get_html($response);
        $secondPage = HtmlDomParser::str_get_html($responseTwo);



        $galeryImages = $firstPage->find('a.image');

        $skillz = [];
        foreach ($galeryImages as $img) {
            foreach ($img->find('img') as $image) {
                $skillz[$this->getSkillName($image->src)] = $image->src;
            }
        }

        $galeryImages = $secondPage->find('a.image');
        foreach ($galeryImages as $img) {
            foreach ($img->find('img') as $image) {
                $skillz[$this->getSkillName($image->src)] = $image->src;
            }
        }

        $this->info('Images and they\'re names are taken!');

        $path = public_path('imgs\skills\\');
        if(!\File::exists($path)) {
            \File::makeDirectory($path, 0777, true, true);
        }

        $this->info('Create Folder "skills" in /public/imgs if not exist ');

        $imagesJson = [];
        foreach ($skillz as $skillName => $imgUrl) {
            // \Image::make($imgUrl)->save($path . $skillName .'.png');
            $noUnderscoreName = str_replace('_', ' ', $skillName);
            $imagesJson[$noUnderscoreName] = '/imgs/skills/' . $skillName .'.png';
            $this->info($skillName . ' image Created !');
        }
        \Storage::disk('public_folder')->put("/imgs/skills/skill_images.json", json_encode($imagesJson, JSON_PRETTY_PRINT));

        $this->info('image.json added to public/jsons folder');
        $this->info('All images are downloaded in /public/imgs/skills folder');
    }

    public function getSkillName($src){
        $imageName = explode('/', $src);
        $imageName = strtok($imageName[6], '.');
        $imageName = str_replace('%27', '\'', $imageName);
        $imageName = str_replace('_skill_icon', '', $imageName);
        return $imageName;
    }
}
