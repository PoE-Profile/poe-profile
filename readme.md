### Features

- Browse characters from PoE account.
- Combined stats data from the passive skill tree and items into easier to browse structure.
- Combined stat: from tree, from jewels, from items etc..
- You can search and add favorite stats.
- You can browse into the public stashes from selected account. 
- We are using Path of Exile's Public Stash Tab API, to keep track of account public stashes.
- You can filter by multiple tags.
- After the tag you can add ">{num}" and if the tag is found on item will combine numbers of all mods that contain tag and compare to initial {num}.
- Add multiple favorite accounts and easily switch between them.
- Integrated search for characters and separation by league.
- Visualization of skills and their support gems.

###Profile How does it work?

####We are using data available from public accounts from pathofexile.com API:
- 'POST','https://www.pathofexile.com/character-window/get-characters' 
- 'form_params' => ['accountName' => [AccountName]] 
- returns all characters from [AcccountName]
- 'POST','https://www.pathofexile.com/character-window/get-items' 
- 'form_params' => ['accountName' => [AccountName],'character' => [CharName]] 
- returns [CharName] items
- 'GET',https://www.pathofexile.com/character-window/get-passive-skills?character=[CharName]&accountName=[AccountName] 
- returns [CharName] passive skill points and jewels

### How to run our poject localy
- clone the project from github
- run "composer install" and "npm install" in console so you have all required dependecys, look at composer.json and package.json
- migrate database using "php artisan migrate"
- run "npm run watch" to compile all javascript