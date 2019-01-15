@extends('layouts.main')

@section('jsData')
<script type="text/javascript">
    window.PHP = {
        account: '',
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
    <div class="container" style="background-color: #211F18;opacity: 0.85;">
        <ul class="nav nav-pills char-nav pull-right">
            @foreach ($current_leagues as $l)
            <li class="nav-item">
                <?php
                $isActiv=(trim($l)==$league->name)?'active':'';
                 ?>
                <a href="{{route('ladders.show',$l)}}" class="nav-link {{$isActiv}}">
                    {{$l}}
                </a>
            </li>
            @endforeach
        </ul>
        <h3 class="" style="padding:10px;">Ladders</h3>
    </div>
    <ladders-page :league="{{$league}}" ></ladders-page>
</div>

@endsection
