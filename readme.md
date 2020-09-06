### Features

- Browse characters from PoE account.
- Combined stats data from the passive skill tree and items into easier to browse structure.
- Combined stat: from tree, from jewels, from items etc..
- You can search and add favorite stats.
- Add multiple favorite accounts and easily switch between them.
- Integrated search for characters and separation by league.
- Visualization of skills and their support gems.
- Added Life and Mana globes , Show Life and Mana auras reservation
- Ladder page, where you can see public accounts and their characters from chosen league
- Twitch page, where you can see online streamers and their last played character
- Builds, you can save Snapshot of gear and skill tree (saved build can be shared)

### Profile How does it work?

#### We are using data available from public accounts from pathofexile.com API:

- 'POST','https://www.pathofexile.com/character-window/get-characters' 'form_params' => ['accountName' => [AccountName]]
  returns all characters from [AcccountName]
- 'POST','https://www.pathofexile.com/character-window/get-items' 'form_params' => ['accountName' => [AccountName],'character' => [CharName]]
  returns [CharName] items
- 'GET',https://www.pathofexile.com/character-window/get-passive-skills?character=[CharName]&accountName=[AccountName]
  returns [CharName] passive skill points and jewels

### How to run our project

- clone the project from github
- run "composer install" and "npm install" in console so you have all required dependecys, look at composer.json and package.json
- rename .env.example file to .env
- run 'php artisan key:generate'
- add database config in '.env' file (DB_DATABASE, DB_USERNAME, DB_PASSWORD)
- migrate database using "php artisan migrate"
- run "npm run watch" to compile all javascript

### At the start of every new league

- run "php artisan poe:update --leagues"
- run "php artisan poe:update --league_table"

### update skill tree

- update or add new version of skill tree in "public/js/st_us{version}.js" in var

```
var passiveSkillTreeData = {json_skill_tree_data};
```

- take {nodes} from passiveSkillTreeData and create new "app\Parse_mods\tree_versions\{version}.json"

- update .env with the new version
