@extends('layouts.main')

@section('jsData')
<script type="text/javascript">
    window.PHP = {
        account: '',
        csrf_token: "{{ csrf_token() }}",
        poe_leagues: "{{ cache('current_leagues', config('app.poe_leagues')) }}"
    }
</script>
@endsection

@section('script')
<script type="text/javascript" src="{{ mix('/js/build/home.js') }}"></script>
<script type="text/javascript">
$('.show-tooltip').tooltip();
</script>
@endsection

@section('content')
<div class="container text-center home-container-bg" v-cloak>
    <div class="account" style="width:100%">


        <div class="inner cover">
            <p class="lead" style="text-align:center">
                Welcome to PoE-Profile.info, ultimate PoE profile Page. <br>
                Here you can see all of your characters
                from <a href="https://www.pathofexile.com/" class="about-link" >PathOfExile.com</a>
                with combined stats data from passive skill tree and items. <br>
                <a href="{{ url('/about') }}" class="btn btn-sm btn-outline-warning">Learn more</a> <br>
            </p>
            <br>
        </div>
        <form enctype="multipart/form-data" action="{{route('profile.post')}}" method="post">
            <div class="input-group " style="width:50%;margin-left:auto;margin-right:auto;background:#202624;">
              <input type="text" name="account" class="form-control"
                    style="border-color: #CCCCCC;" placeholder="Account Name...">
              <span class="input-group-btn">
              <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <button type="submit" class="btn btn-outline-warning" >View Profile!</button>
              </span>
            </div>
        </form>
        <div class="text-xs-center" style="margin-top: 20px;">
            You can test out with this popular streamer accounts
            <a href="/profile/ziggyd/" class="about-link">Ziggyd</a>,
            <a href="/profile/Zizaran/" class="about-link">Zizaran</a>,
            <a href="/profile/mathil/" class="about-link">Mathil</a>,
            <a href="/profile/nugiyen/" class="about-link">Nugiyen</a>,
            <a href="/profile/RaizQT/" class="about-link">RaizQt</a>
            <br>
            <a class="nav-link" href="{{ route('tutorial.profile') }}" style="color:lightblue">
                How to change your profile characters tab to public.
            </a>
        </div>

         @include('flash::message')
        <br>

       <div class="text-xs-center" style="padding-bottom:5px;">
            <div style="margin: 0 auto;height: 91px;width: 729px;border: 1px solid #FFF;">
                <script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
                <!-- exileMainAd -->
                <ins class="adsbygoogle"
                     style="display:inline-block;width:728px;height:90px"
                     data-ad-client="ca-pub-5347674045883414"
                     data-ad-slot="2036252705"></ins>
                <script>
                (adsbygoogle = window.adsbygoogle || []).push({});
                </script>
            </div>
        </div>

        <div class="navigation" style="padding-bottom: 0px;margin-top: 10px;">
            <ul class="nav nav-tabs poe-profile-menu" style="padding-left: 10px;background-color: #211F18;">
                <li class="nav-item">
                    <a class="nav-link active" data-toggle="tab" @click.prevent="getTwitch()"
                    href="#twitch" role="tab" aria-controls="twitch">Twitch</a>
        	    </li>
                <li class="nav-item">
                    <a class="nav-link" href="/ladders" >League Ladders</a>
        	    </li>
            </ul>
        </div>
        <div class="tab-content" style="background-color: #211F18;min-height:800px;">
            <twitch-page></twitch-page>
        </div>


    </div>
</div>

@endsection
