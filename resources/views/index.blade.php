@extends('layouts.main')

@section('metatags')
    <meta property="og:title" content="Ultimate PoE-profile Page"/>
    <meta property="og:type" content="website" />
    <meta property="og:site_name" content="PoE-profile" />
    <meta property="og:url" content="http://{{ $_SERVER['HTTP_HOST'] }}/" />
    <meta property="og:image" content="http://{{ $_SERVER['HTTP_HOST'] }}/imgs/icon.png"/>
    <meta property="og:description" content="Here you can see Skill Gems, Items and Combined Stats Data (from passive skill tree and items)."/>
@endsection

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
    <div style="width:100%;" class="pt-1">

        <div class="jumbotron pt-1 pb-0 mb-1" style="background-color: #0b0706;opacity: 0.85;margin-left: 6%;width:88%;">
            
        
            <div class="inner cover">
                <p class="lead text-xs-center">
                    Welcome to PoE-Profile.info, ultimate PoE profile Page. <br>
                    Here you can see all of your characters
                    from <a href="https://www.pathofexile.com/" class="about-link" >PathOfExile.com</a>
                    with combined stats data from passive skill tree and items. 
                    <a href="{{ url('/about') }}" class="btn btn-sm btn-outline-warning">Learn more</a> <br>
                </p>
            </div>
            <form enctype="multipart/form-data" action="{{route('profile.post')}}" method="post">
                <div style="text-align:center;">
                    <strong style="margin-right:20px;">
                        <span class="badge" style="background: green;">New</span>
                        Realm:
                    </strong>
                    <label class="custom-control custom-radio" :class="{'active':(realm=='pc')}">
                        <input id="radio1" name="realm" type="radio" value="pc" checked="checked" 
                                v-model="realm" class="custom-control-input">
                        <!-- <span class="custom-control-indicator realm-indicator"></span> -->
                        <span class="custom-control-description">
                            <span class="profile-icon platform-pc"></span> PC
                        </span>
                    </label>
                    <label class="custom-control custom-radio" :class="{'active':(realm=='xbox')}">
                        <input id="radio2" name="realm" type="radio" value="xbox" 
                               v-model="realm" class="custom-control-input">
                        <!-- <span class="custom-control-indicator realm-indicator"></span> -->
                        <span class="custom-control-description">
                            <span class="profile-icon platform-xbox"></span> XBOX
                        </span>
                    </label>
                    <label class="custom-control custom-radio" :class="{'active':(realm=='sony')}">
                        <input id="radio2" name="realm" type="radio" value="sony" 
                                v-model="realm" class="custom-control-input">
                         <!-- <span class="custom-control-indicator realm-indicator"></span> -->
                        <span class="custom-control-description">
                            <span class="profile-icon platform-sony"></span> PlayStation
                        </span>
                    </label>
                </div>
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
            <div>
                
            </div>
            <ladder-select class="mb-0" style=""
                :leagues="{{$current_leagues}}" 
                :realm="'pc'">
            </ladder-select>
        </div>


         @include('flash::message')
        
       <div class="text-xs-center" style="padding-bottom:5px;">
            <div style="margin: 0 auto;height: 91px;width: 729px;border: 1px solid orange;">
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
            <div class="nav nav-tabs poe-profile-menu pt-1" style="background-color: #211F18; opacity: 0.8;">
                <h4 class="text-xs-center">
                    <i aria-hidden="true" class="fa fa-twitch"></i> Livestreams :
                </h4>
                <!-- <li class="nav-item">
                    <a class="nav-link" href="/ladders" >League Ladders</a>
        	    </li> -->
            </div>
        </div>
        <div class="tab-content" style="background-color: #211F18;min-height:800px;">
            <twitch-page></twitch-page>
        </div>


    </div>
</div>

@endsection
