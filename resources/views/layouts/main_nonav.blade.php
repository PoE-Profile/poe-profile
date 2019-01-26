<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="csrf-token" content="{{ csrf_token() }}">
	 <title> @yield('title', 'PoE Profile Info')</title>

	<!-- Latest compiled and minified CSS -->
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.5/css/bootstrap.min.css"
        integrity="sha384-AysaV+vQoT3kOAXZkl02PThvDr8HYKPZhNT5h/CXfBThSRXQ6jW5DO2ekP5ViFdi" crossorigin="anonymous">
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" media="screen" title="no title">

	@routes
	<link rel="stylesheet" type="text/css" href="{{ URL::to('/') }}/css/main.css">
	<link rel="stylesheet" type="text/css" href="{{ URL::to('/') }}/css/profile.css">
	<link rel="stylesheet" type="text/css" href="{{ URL::to('/') }}/css/sockets.css">
	<link rel="stylesheet" type="text/css" href="{{ URL::to('/') }}/css/skills.css">
	{{-- <link rel="stylesheet" type="text/css" href="{{ URL::to('/') }}/css/vue-loading.css"> --}}
	@yield('styleSheets')
</head>
<body >
	<div class="container" id="app">
		<div>
			@yield('content')
		</div>
	</div>

	@yield('jsData')

	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js" integrity="sha384-3ceskX3iaEnIogmQchP8opvBy3Mi7Ce34nWjpBIwVTHfGYWQS9jwHDVRnpKKHJg7" crossorigin="anonymous"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/tether/1.3.7/js/tether.min.js" integrity="sha384-XTs3FgkjiBgo8qjEjBk0tGmf3wPrWtA6coPfQDfFEY8AnYJwjalXCiosYRBIBZX8" crossorigin="anonymous"></script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.5/js/bootstrap.min.js" integrity="sha384-BLiI7JTZm+JWlgKa0M0kGRpJbF2J8q+qreVrKBC47e3K6BW78kGLrCkeRX6I9RoK" crossorigin="anonymous"></script>
    <script src="/js/ads.js" type="text/javascript"></script>
    <script type="text/javascript">
    if(!document.getElementById('WAMhafvKNgVtphDM')){
        document.getElementById('iPZUemdYKrpcjyd').style.display='block';
    }
    </script>

	@yield('script')
	<script type="text/javascript">
		$('div.alert').not('.alert-important').delay(2500).fadeOut(350);
	</script>

	<script>
	  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
	  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
	  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
	  })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

	  ga('create', 'UA-90361425-1', 'auto');
	  ga('send', 'pageview');

	</script>
</body>
</html>
