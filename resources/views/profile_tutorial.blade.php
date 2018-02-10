@extends('layouts.profile')

@section('jsData')
<script type="text/javascript">
    window.PHP = {
    }
</script>
@stop


@section('script')
<script type="text/javascript" src="/js/build/home.js"></script>
@endsection


@section('styleSheets')
@endsection

@section('content')
<div class="container">
    <div class="row lead" style="padding: 20px;color:white;background: #190a09;">

        <h4>How to change your profile characters tab to public</h3>
        <ol>
            <li style="margin: 10px">
                First go to: https://www.pathofexile.com/account/view-profile/{YourProfileName}
                
            </li>
            <li style="margin: 10px">
                Click on Privacy Settings
                <img src="/imgs/public_account/step1.jpg" alt="">
            </li>
            <li style="margin: 10px">
                Make sure your "Hide characters tab" is unchecked
                <img src="/imgs/public_account/step2.jpg" alt="">
            </li>
        </ol>
        
        

    </div>
</div>

@endsection
