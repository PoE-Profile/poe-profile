@extends('layouts.main')

@section('jsData')
<script type="text/javascript">
    window.PHP = {
    }
</script>
@stop

@section('title')
   PoE Profile Info Tutorial
@endsection

@section('script')
<script type="text/javascript" src="{{ mix('/js/build/home.js') }}"></script>
@endsection

@section('styleSheets')
@endsection

@section('content')
<div class="container">
    <div class="row lead" style="padding: 20px;color:white;background: #190a09;">

        <h4>How to save a build</h3>
        <ol>
            <li style="margin: 10px">
                First visit any public profile character page: https://www.poe-profile.info/profile/{account}/{character}

            </li>
            <li style="margin: 10px">
                Click on Save Build/Snapshot
                <img src="/imgs/build_tutorial/build_step1.png" style="width:80%" alt="">
            </li>
            <li style="margin: 10px"><br>
                <img src="/imgs/build_tutorial/build_step2.png" style="width:80%" alt="">
            </li>
            <li>After save you will be redirected to "My Builds"</li>
        </ol>



    </div>
</div>

@endsection
