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
                First visit any profile page: https://www.poe-profile.info/profile/{pickAccount}
                
            </li>
            <li style="margin: 10px">
                Click on Save Build/Snapshot
                <img src="/imgs/public_account/step1.jpg" alt="">
            </li>
            <li style="margin: 10px">
                Pick a name for your snapshot/build
                <img src="/imgs/public_account/step2.jpg" alt="">
            </li>
        </ol>
        
        

    </div>
</div>

@endsection
