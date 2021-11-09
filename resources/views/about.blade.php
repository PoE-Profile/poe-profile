@extends('layouts.main')

@section('jsData')
<script type="text/javascript">
    window.PHP = {
    }
</script>
@stop

@section('title')
   PoE Profile Info About
@endsection

@section('script')
<script type="text/javascript" src="{{ mix('/js/build/home.js') }}"></script>
@endsection

@section('styleSheets')
@endsection

{{-- Използваме даните които са достъпни за публик акаунти през не официалното(не е включено в api docs) API на pathofexile.com
https://www.pathofexile.com/character-window/get-characters?&accountName=[AccountName]

https://www.pathofexile.com/character-window/get-items?character=[Charname]&accountName=[AccountName]

https://www.pathofexile.com/character-window/get-passive-skills?character=[Charname]&accountName=[AccountName] --}}

@section('content')
<div class="container">
    <div class="row lead" style="padding: 20px;color:white;background: #190a09;">

        <h2>Features</h2>
    	<ul>
    		<li>
				Browse characters from PoE account.
			</li>
    		<li>
    			Combined stats data from the passive skill tree and items into easier to browse structure.
    			<ul>
    				<li>
    					Combined stat: from tree, from jewels, from items etc..
    				</li>
    				<li>
                        You can search and add favorite stats.
    				</li>
    			</ul>
    		</li>
    		<li>
                Add multiple favorite accounts and easily switch between them.
    		</li>
    		<li>
    			Integrated search for characters and separation by league.
			</li>
			<li>
				Visualization of skills and their support gems.
			</li>
            <li>Added Life and Mana globes , Show Life and Mana auras reservation</li>
            <li>Ladder page, where you can see public accounts and their characters from chosen league</li>
            <li>Twitch page, where you can see online streamers and their last played character</li>
            <li>Builds, you can save Snapshot of gear and skill tree (saved build can be shared)</li>

    	</ul>

		<br>

        <h2>Profile How does it work?</h2>
        <span>We are using data available from public accounts from  <a class="about-link" href="https://www.pathofexile.com/developer/docs/index">pathofexile.com API</a></span>


        <br>

		<!-- <h2>Things we are working on</h2>
		<ul>
			<li>
                Bonus stats from most of the unique jewels is not displayed.
			</li>
			<li>
				Some of Unique Items bonuses not displayed.
			</li>
            <li>
                Show/search microtransactions in all characters from given account.
            </li>
            <li>
                Challenges from poe.profile.
            </li>
            <li>
                Login/registration to save your favorite accounts and some extras :).
            </li>
		</ul> -->
		<br>
		<h4>
			If you see problems or bugs with the site you can report them on our 
			<a href="https://github.com/PoE-Profile/poe-profile/issues/" target="_blank" class="about-link">GitHub issues</a> or <a href="https://www.pathofexile.com/forum/view-thread/1818424" target="_blank" class="about-link">Forum post</a>.
		</h4>
		<br>
		<h4>You can support us by using the button below.</h4>

        <form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top">
        <input type="hidden" name="cmd" value="_s-xclick">
        <input type="hidden" name="hosted_button_id" value="XUWHT2H9SSMLE">
        <input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_donateCC_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
        <img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1">
        </form>

    </div>
</div>

@endsection
