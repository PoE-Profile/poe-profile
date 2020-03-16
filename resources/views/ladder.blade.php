@extends('layouts.main')

@section('metatags')
<meta property="og:title" content="PoE-profile Ladders"/>
<meta property="og:type" content="website" />
<meta property="og:site_name" content="PoE-profile" />
<meta property="og:url" content="http://{{ $_SERVER['HTTP_HOST'] }}/ladders" />
<meta property="og:image" content="http://{{ $_SERVER['HTTP_HOST'] }}/imgs/icon.png"/>
<meta property="og:description" content="Here you can see Skill Gems, Items and Combined Stats Data (from passive skill tree and items)."/>
@endsection

@section('jsData')
<script type="text/javascript">
    window.PHP = {
        realm: '{!! $_GET["realm"] or "pc" !!}',
    }
</script>
@stop


@section('title')
    PoE Profile Info Ladder
@endsection

@section('script')
<script type="text/javascript" src="{{ mix('/js/build/home.js') }}"></script>
@endsection

@section('styleSheets')
@endsection

@section('content')

<div class="text-xs-center" style="padding-bottom:5px;">
    <div style="margin: 0 auto;">
        <script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
        <!-- exile_profile_big -->
        <ins class="adsbygoogle"
             style="display:inline-block;width:970px;height:90px"
             data-ad-client="ca-pub-5347674045883414"
             data-ad-slot="8430954096"></ins>
        <script>
        (adsbygoogle = window.adsbygoogle || []).push({});
        </script>
    </div>
</div>
<div class="container page-bg" v-cloak>
    @include('flash::message')
    <ladders-page :league="{{$league}}" :leagues='{{$current_leagues}}'></ladders-page>
</div>

@endsection
