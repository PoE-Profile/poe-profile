<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="csrf-token" content="{{ csrf_token() }}">
	
	 <title> @yield('title', 'PoE Profile Info')</title>

	<!-- Latest compiled and minified CSS -->
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.5/css/bootstrap.min.css" integrity="sha384-AysaV+vQoT3kOAXZkl02PThvDr8HYKPZhNT5h/CXfBThSRXQ6jW5DO2ekP5ViFdi" crossorigin="anonymous">

	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" media="screen" title="no title">

	<link rel="stylesheet" type="text/css" href="{{ URL::to('/') }}/css/main.css">
	<link rel="stylesheet" type="text/css" href="{{ URL::to('/') }}/css/profile.css">
	<link rel="stylesheet" type="text/css" href="{{ URL::to('/') }}/css/sockets.css">
	<link rel="stylesheet" type="text/css" href="{{ URL::to('/') }}/css/skills.css">
	{{-- <link rel="stylesheet" type="text/css" href="{{ URL::to('/') }}/css/vue-loading.css"> --}}
	@yield('styleSheets')
	<style type="text/css">
		/* always present */
		.expand-transition {
			transition: all .3s ease;
			height: 30px;
			padding: 10px;
			background-color: #eee;
			overflow: hidden;
		}
		/* .expand-enter defines the starting state for entering */
		/* .expand-leave defines the ending state for leaving */
		.expand-enter, .expand-leave {
			height: 0;
			padding: 0 10px;
			opacity: 0;
		}

	</style>
</head>
<body >
	<div class="container" id="app">
		<nav class="navbar navbar-inverse bg-inverse navbar-fixed-top" style="">
			<button class="btn btn-outline-warning pull-right hidden-lg-up"
            type="button"
            data-toggle="collapse"
			data-target="#navbarResponsive"
            aria-controls="navbarResponsive"
            aria-expanded="false"
            aria-label="Toggle navigation"
			style="">
                <i class="fa fa-bars" aria-hidden="true"></i>
			</button>
			<div class="collapse navbar-collapse navbar-toggleable-md" id="navbarResponsive">

				<ul class="nav navbar-nav">
					<li class="nav-item active">
                        <a class="nav-link navbar-brand1 " href="{{ url('/') }}">
            				<i class="fa fa-home" aria-hidden="true"></i> PoE-Profile.Info
            			</a>
					</li>
					<li class="nav-item">
						<a class="nav-link" href="{{ route('twitch') }}"><i class="fa fa-twitch" aria-hidden="true"></i> Twitch <span class="sr-only">(current)</span></a>
					</li>
					<li class="nav-item">
						<a class="nav-link" href="{{ route('ladders') }}"><i class="fa fa-list-ol" aria-hidden="true"></i> Ladder <span class="sr-only">(current)</span></a>
					</li>
					<li class="nav-item">
						<a class="nav-link" href="{{ route('index.builds') }}"><i class="fa fa-list-ol" aria-hidden="true"></i> Builds <span class="sr-only">(current)</span></a>
					</li>
					<li class="nav-item dropdown">
						<a class="nav-link" href="{{ route('favorites') }}">
					  		<i class="fa fa-star" aria-hidden="true"></i> Favorites
						</a>
					</li>
                    <li class="nav-item" v-if="favStore.favAcc.length>0">
                        <div class="" style="width:100%;color: white;">
                            <drop-down v-on:selected="goToAcc" :list="favStore.favAcc">
                                <i class="fa fa-star" aria-hidden="true"></i>
                            </drop-down>

                        </div>
                    </li>
					<li class="nav-item">
						<a class="nav-link" href="{{ url('/about') }}"><i class="fa fa-info" aria-hidden="true"></i> About<span class="sr-only">(current)</span></a>
					</li>
					<li class="nav-item">
						<a class="nav-link" href="{{ url('/update_notes') }}" style="color: orange">
							What is New v1.1
						</a>
					</li>
					<li class="nav-item float-lg-right">
						<ul class="nav navbar-nav">
							<form class="form-inline" action="{{route('view.post.profile')}}" method="post">
								<div class="input-group">
							      <input type="text" class="form-control"
								  style="border-color: #CCCCCC;width:145px;"
								  name="account"
								  placeholder="Account Name...">
								  <input type="hidden" name="_token" value="{{ csrf_token() }}" >
							      <span class="input-group-btn">
							        <button class="btn btn-outline-warning" type="submit">View!</button>
							      </span>
							    </div>
							</form>
							@if (Auth::guest())
							{{-- <li class="nav-item"><a class="nav-link" href="{{ url('/login') }}">Login</a></li> --}}
							{{-- <li class="nav-item"><a class="nav-link" href="{{ url('/register') }}">Register</a></li> --}}
							@else
							<li class="nav-item dropdown" style="padding-right: 55px">
								<a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
									<img src="/uploads/avatars/{{Auth::user()->avatar}}" style="width:25px; height:25px; float:left; border-radius:50%; margin-right:25px;" /><span class="caret"></span>
									{{-- {{ Auth::user()->name }}  --}}
								</a>
								<div class="dropdown-menu" aria-labelledby="responsiveNavbarDropdown">
									<a class="dropdown-item" href="{{ url('/logout') }}"><i class="fa fa-btn fa-sign-out"></i>Logout</a>
									<a class="dropdown-item" href="{{ url('/settings') }}"><i class="fa fa-btn fa-user"></i>Account Settings</a>
									<a class="dropdown-item" href="{{ url('/profile') }}"><i class="fa fa-btn fa-user"></i>Poe Profile</a>
								</div>
							</li>
							@endif
						</ul>
					</li>
					<li class="nav-item float-lg-right">
                        <form action="https://www.paypal.com/cgi-bin/webscr" method="post" class="form-inline" target="_top">
                        <input type="hidden" name="cmd" value="_s-xclick">
                        <input type="hidden" name="hosted_button_id" value="XUWHT2H9SSMLE">
                        <input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_donate_SM.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
                        <img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1">
                        </form>
					</li>
				</ul>

			</div>
		</nav>
		<div>
			@yield('content')
		</div>
	</div>

	@yield('jsData')

	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js" integrity="sha384-3ceskX3iaEnIogmQchP8opvBy3Mi7Ce34nWjpBIwVTHfGYWQS9jwHDVRnpKKHJg7" crossorigin="anonymous"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/tether/1.3.7/js/tether.min.js" integrity="sha384-XTs3FgkjiBgo8qjEjBk0tGmf3wPrWtA6coPfQDfFEY8AnYJwjalXCiosYRBIBZX8" crossorigin="anonymous"></script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.5/js/bootstrap.min.js" integrity="sha384-BLiI7JTZm+JWlgKa0M0kGRpJbF2J8q+qreVrKBC47e3K6BW78kGLrCkeRX6I9RoK" crossorigin="anonymous"></script>


	@yield('script')
	<script type="text/javascript">
		$('div.alert').not('.alert-important').delay(3000).fadeOut(350);
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
