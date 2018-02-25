@extends('layouts.profile')

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
<script type="text/javascript" src="/js/build/home.js"></script>
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
                You can browse into the public stashes from selected account. <br>
    			We are using Path of Exile's <a href="http://www.pathofexile.com/developer/docs/api-resource-public-stash-tabs" class="about-link">Public Stash Tab API</a>, to keep track of account public stashes.
    			<ul>
    				<li>
    					You can filter by multiple tags.
    				</li>
					<li>
						After the tag you can add ">{num}" and if the tag is found on item will combine numbers of all mods that contain tag and compare to initial {num}.
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
    	</ul>

		<br>

        <h2>Profile How does it work?</h2>
        <span>We are using data available from public accounts from  pathofexile.com API:</span>
        <ul>
             <li>
                 <code class="highlighter-rouge" style="font-size: 17px;background:lightblue;">
                         'POST','https://www.pathofexile.com/character-window/get-characters'
                 </code><br>
                 <code class="highlighter-rouge" style="font-size: 17px;background:lightblue;">
                         'form_params' => ['accountName' => [AccountName]]
                     <!-- https://www.pathofexile.com/character-window/get-characters?&accountName=[AccountName] -->
                 </code>
                 <br> returns all characters from [AcccountName]
             </li>
             <li>
                 <code class="highlighter-rouge" style="font-size: 17px;background:lightblue;">
                     'POST','https://www.pathofexile.com/character-window/get-items'
                 </code><br>
                 <code class="highlighter-rouge" style="font-size: 17px;background:lightblue;">
                     'form_params' => ['accountName' => [AccountName],'character' => [CharName]]
                     <!-- https://www.pathofexile.com/character-window/get-items?character=[Charname]&accountName=[AccountName] -->
                 </code>
                 <br> returns [CharName] items
             </li>
             <li>
                 <code class="highlighter-rouge" style="font-size: 15px;background:lightblue;">
                     'GET',https://www.pathofexile.com/character-window/get-passive-skills?character=[CharName]&accountName=[AccountName]
                 </code>
                 <br> returns [CharName] passive skill points and jewels
             </li>
        </ul>

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
			If you see problems or bugs with the site you can report on our
			<a href="https://www.reddit.com/r/pathofexile/comments/5nzuxa/tool_poeprofile_the_ultimate_profile_page/" target="_blank" class="about-link">reddit</a> or <a href="https://www.pathofexile.com/forum/view-thread/1818424" target="_blank" class="about-link">forum</a> post.
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
