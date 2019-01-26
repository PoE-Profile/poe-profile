@extends('layouts.main')

@section('jsData')
<script type="text/javascript">
    window.PHP = {
    }
</script>
@stop


@section('script')
<script type="text/javascript" src="{{ mix('/js/build/home.js') }}"></script>
@endsection


@section('styleSheets')
@endsection


@section('content')
<div class="container">
    <div class="row lead" style="padding: 20px;color:white;background: #190a09;">
        <h2>What is new <small>(version 2.4 - January 26 ,2019)</small> </h2>
        <ul>
            <li>Complete refactoring on Ladder page and UI changes
                <ul>
                    <li>Added compact/normal view</li>
                    <li>Added Exp per hour and rank changes</li>
                    <li>Visual indication if account is online</li>
                    <li>Delve solo/team ranks and ability to sort</li>
                    <li>Expirience progress bar</li>
                    <li>Added filters to Ladders URI,  now you are able to share links</li>
                    <li>Displaying private leagues</li>
                    <li>Added link to twitch if streaming</li>
                    <li>List support gems for skills on hover</li>
                </ul>
            </li>
            <br>
            <li>Added calculation for Fluid Motion Solution transform jewel</li>
        </ul>
        
        <h4>
            If you see problems or bugs with the site you can report on our <a href="https://www.pathofexile.com/forum/view-thread/1818424" target="_blank" class="about-link">forum</a>
            and <a href="https://www.reddit.com/r/pathofexile/comments/83oydq/tool_poeprofile_21_with_github/" class="about-link">reddit</a> posts.
        </h4>

        <br><br>
        <h1>Changes from older versions:</h1>
        <h2>What is new <small>(version 2.3 - December 7 ,2018)</small> </h2>
        <ul>
            <li>Passive skill Tree updated to 3.5</li>
            <li>Added images for new skills</li>
            <li>Fixed bug: props on flasks now display properly</li>
            <li>Fixed bug: after favoriting an account there is a link to Favorites Page</li>
            <li>More coming soon ....</li>
    	</ul>

        <h2>What is new <small>(version 2.2 - June 8 ,2018)</small> </h2>
        <ul>
            <li>Added images for new skills</li>
            <li>Passive skill Tree updated to 3.3</li>
            <li>Fixed bug with PoB Code not adding jewels</li>
            <li>Added new leagues to ladder</li>
            <li>Skill Tree versioning for snapshots/builds </li>
    	</ul>
        
        <h2>What is new <small>(version 2.1 - Mar 11 ,2018)</small> </h2>
        <ul>
            <li>Automatically make Snapshots of Twitch streamers</li>
            <li>Resistance in Stats tab show real value (-60% resistance)</li>
            <li>Added elder/shaper background to items</li>
            <li>Added Public Stashes (redirect to pathofexile.com/trade/)</li>
            <li>Added PoB Code in builds</li>
            <li>Added Snapshots tab in profile </li>
            <li>Added <a href="{{route('tutorial.build')}}" class="about-link">Tutorial</a> for Builds</li>
    	</ul>

        
        <h2>What is new <small>(version 2.0 - Mar 5 ,2018)</small> </h2>
        <ul>
            <li>Project updated to Laralve 5.5 using Vuejs 2.x</li>
            <li>We are now on <a href="https://github.com/PoE-Profile/poe-profile" target="_blank" class="about-link">GITHUB</a> if you wanna contribute you are welcome</li>
            <li>Added new feature Builds, you can save Snapshot of gear and skill tree (saved build can be shared)</li>
            <li>Added Ranks tab in Profile so you can see previous league Ranks </li>
            <li>Improvements on Ladders</li>
            <li>Fixed major bug. Since we moved to github two weeks ago, we were not able to load any new public accounts that are not already in our database.</li>
    	</ul>

        <br>

        <h2>What is new <small>(version 1.1 - Jan 12, 2018)</small> </h2>
        <ul>
            <li>Added jewels from items and their stats.</li>
            <li>Added button to copy Path of Building code for easy import.</li>
            <li>Abyss sockets display properly on items</li>
            <li>Remove Bug where abyss jewels display as Skills</li>
            <li>We are displaying character current rank in Ladder if in top 2000</li>
            <li>Public Stashes section is removed.</li>
            <li>PoE 3.1 changes
                <ul>
                    <li>Added images for new skills</li>
                    <li>Passive skill Tree changed</li>
                </ul>
            </li>
    	</ul>

        <br>

        <h2>What is new <small>(version 1.0 - Sep 4, 2017)</small> </h2>
        <ul>
            <li>Added Ladder page, where you can see public accounts and their characters from chosen league</li>
            <li>Added Twitch page, where you can see online streamers and their last played character.</li>
            <li>Added Favorites page now we show last played character</li>
            <li>Home page shows list of characters for ladder,twitch and favorites</li>
            <li>
	             Unfortunately we don't have enough space on our VPS, so from now on we will index only stashes from new leagues.
			</li>
            <li>
                PoE Expansion 3.0 changes
                <ul>
                    <li>Bandits sections now work with </li>
                    <li>Added images for new skills</li>
                    <li>Passive skill Tree changed</li>
                </ul>
            </li>
            <li>add Check for Passive skill Tree if it got reseted recently</li>
    	</ul>

        <br>

        <h2>What is new <small>(version 0.4 - Mar 25, 2017)</small> </h2>
        <ul>
            <li>
                Added Life and Mana globes
            </li>
            <li>
                Now you can select rewards from Bandit quests.
            </li>
            <li>
                Show Life and Mana auras reservation.
            </li>
            <li>
				Added Uniques with integrated gems
                <ul>
                    <li>Soul Mantle</li>
                    <li>Doryani's Catalyst</li>
                    <li>Kitava's Feas</li>
                    <li>Lioneye's Vision</li>
                    <li>Pledge of Hands</li>
                    <li>Heretic's Veil</li>
                    <li>Prism Guardian</li>
                </ul>
			</li>
            <li>
                Added Conversion from Geofri's Sanctuary and Shaper's Touch uniques
            </li>
            <li>
				Added Endurance, Frenzy and Power charges to Stats section
			</li>
            <li>
                Energy Shield now includes conversion from Ascendancy passive skill Radiant Faith (Guardian).
            </li>
        </ul>

        <br>

        <h2>What is new <small>(version 0.3 - Mar 11, 2017)</small> </h2>
        <ul>
            <li>
				New Jewel section
			</li>
            <li>
				When load an account, selects last played character
			</li>
    		<li>
				Stats section includes conversion stats from: Presence of Chayula,Sanctuary of Thought
			</li>
            <li>
				Stats section includes Conversion stats from Unique jewels:
                <ul>
                    <li>Energy From Within</li>
                    <li>Brute Force Solution</li>
                    <li>Careful Planning</li>
                    <li>Efficient Training</li>
                    <li>Fertile Mind</li>
                    <li>Inertia</li>
                </ul>
			</li>
            <li>
				Stats section, Attack Speed includes 10% more if dual wielding
			</li>
            <li>
                Skill section shows more uniques with integrated skill and supports gems
			</li>
            <li>
                Skills that use trigger gems now will show the skill that triggers them.
            </li>
            <li>
                Update to 2.6.0 skill tree data
			</li>

        </ul>
        <br>

        <h2>What is new <small>(version 0.2 (beta) - Jan 14, 2017)</small> </h2>
    	<ul>
    		<li>
				Added Misc tab to Stat Section with lots of stats related to it.
			</li>
            <li>
                Critical Strike Chance and Critical Strike Multiplier rework. <br>
                Added Attack Critical Chance/Multiplier it includes Global Critical Chance/Multiplier plus Critical with specific weapon. <br>
                Added Spell Critical Chance/Multiplier it includes Global Critical Chance/Multiplier plus Critical with spells.
            </li>
            <li>
                Attack Speed and Physical Damage now includes stats with specific weapons.
            </li>
            <li>
                Added Infused Shield(15% More ES) in calculation to Energy Shield,if taken. <br>
                Remove "with Chaos Inoculation" from Total Stats section
            </li>
            <li>
                Added "with Aura" to Total Stats for Energy Shield,Evasion and Armour . (note: Aura effectivess included in our calculations)
            </li>
            <li>
                Added Gem tooltip when hovering in item.
            </li>
            <li>
                You will no longer lose league selection after choosing a character.
            </li>
            <li>
                Fixed bug where in Skill section, sometimes support gems icons overlap.
            </li>
            <li>
                Added stats: Increased Area Effect, Area Damage, % Accuracy Rating, Wand Damage, Weapon Elemental Damage and more.
            </li>
    	</ul>


    </div>
</div>

@endsection
