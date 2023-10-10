<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="mobile-web-app-capable" content="yes">

<meta name="apple-mobile-web-app-title" content="Add to Home">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css" integrity="sha384-9aIt2nRpC12Uk9gS9baDl411NQApFmC26EwAOH8WgZl5MYYxFfc+NcPb1dKGj7Sk" crossorigin="anonymous">
	<link rel="stylesheet" href="{{ asset('css/style.css')}}">
	<link rel="stylesheet" href="{{ asset('css/line-awesome.min.css')}}">
<script src='https://www.google.com/recaptcha/api.js'></script>
<link rel="icon" href="{{ asset('img/favicon.png')}}" type="image/x-icon"/>
    <title>HRMS | CLIMBR</title>
    <link rel="apple-touch-icon" sizes="57x57" href="{{ asset('img/apple-icon-57x57.png')}}">
<link rel="apple-touch-icon" sizes="60x60" href="{{ asset('img/apple-icon-60x60.png')}}">
<link rel="apple-touch-icon" sizes="72x72" href="{{ asset('img/apple-icon-72x72.png')}}">
<link rel="apple-touch-icon" sizes="76x76" href="{{ asset('img/apple-icon-76x76.png')}}">
<link rel="apple-touch-icon" sizes="114x114" href="{{ asset('img/apple-icon-114x114.png')}}">
<link rel="apple-touch-icon" sizes="120x120" href="{{ asset('img/apple-icon-120x120.png')}}">
<link rel="apple-touch-icon" sizes="144x144" href="{{ asset('img/apple-icon-144x144.png')}}">
<link rel="apple-touch-icon" sizes="152x152" href="{{ asset('img/apple-icon-152x152.png')}}">
<link rel="apple-touch-icon" sizes="180x180" href="{{ asset('img/apple-icon-180x180.png')}}">
<link rel="icon" type="image/png" sizes="192x192"  href="{{ asset('img/android-icon-192x192.png')}}">
<link rel="icon" type="image/png" sizes="32x32" href="{{ asset('img/favicon-32x32.png')}}">
<link rel="icon" type="image/png" sizes="96x96" href="{{ asset('img/favicon-96x96.png')}}">
<link rel="icon" type="image/png" sizes="16x16" href="{{ asset('img/favicon-16x16.png')}}">
<link rel="shortcut icon" sizes="16x16" href="{{ asset('img/icon-16x16.png')}}">
<link rel="shortcut icon" sizes="196x196" href="{{ asset('img/icon-196x196.png')}}">

<link rel="apple-touch-icon-precomposed" href="{{ asset('img/icon-152x152.png')}}">
<link rel="manifest" href="{{ asset('js/manifest.json')}}">
<meta name="msapplication-TileColor" content="#ffffff">
<meta name="msapplication-TileImage" content="{{ asset('img/ms-icon-144x144.png')}}">
<meta name="theme-color" content="#ffffff">
<link rel="stylesheet" type="text/css" href="{{ asset('addtohomescreen/addtohomescreen.css')}}">
<style type="text/css">
	body{    background-color: #fef2f2;}header{height: 68px;}

</style>
  </head>
  <body>

  	<header>
  		<div class="wrapper">
  			<div class="row">
  				<div class="col-lg-4 col-md-4 col-12 col-xl-4">
  					<div class="logo">
  						<img src="{{ asset('img/logo.png')}}" alt="">
  					</div>
  				</div>
  			</div>
  		</div>
  	</header>
    <div class="form-body">
		<div class="wrapper">
			<div class="row">
			    <div class="text-center col-lg-8 col-md-6">
			    	<h1>CLIMBR</h1>
			    	<h3>Your Virtual HR Manager</h3>
			    	<div class="vdo">
			    	<!--<a href="#" data-toggle="modal" data-target="#video2">Watch Tutorial Video</a>-->


			    	<!-----------video-modal------------------>
<div class="modal fade" id="video2" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true"
    style="display: none;margin-top: 66px;">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content" style="background: none;border: none;">
            <div class="modal-header" style="border-bottom: none;">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true"><img src="{{ asset('img/close.png')}}"
                            style="width: 20px;height: 20px;"></span></button>

            </div>
            <div class="modal-body modalVideo" style="padding: 0;">
                <video width="100%" height="450px" id="video-2" controls="" class="embed-responsive-item">
                    <source src="{{ asset('public/img/how-to-create-an-organisation-profile-on-hrms.mp4')}}" type="video/mp4">
                </video>
            </div>
        </div>
    </div>
</div>
<!---------------------------------->
			    </div>


<div class="text-center manual">
	<!--<a href="https://climbr.co.in/frontend/document/wpc-user-manual.pdf" target="_blank">-->

	<!--	Download HRMS Software Manual-->
	<!--</a>-->
</div>
			    	<img src="{{ asset('img/hiring.png')}}" alt="" style="width: 100%;margin-top: 15px;">
			    </div>

				<div class="col-lg-4 col-md-6">
					<div class="emp">
						<h4>Login</h4>
						<form action=""  method="post" >
						{{csrf_field()}}
						 @if(Session::has('message'))
			<div class="alert alert-danger" style="text-align:center;"><span class="glyphicon glyphicon-ok" ></span><em > {{ Session::get('message') }}</em></div>
			@endif
							<div class="row form-group">
							<div class="col-md-12">
								<input type="email" class="form-control" placeholder="Email"   name="email">
								<span class="form-ico"><i class="las la-user-circle"></i></span>
								 @if ($errors->has('email'))
        <div class="error" style="color:red;">{{ $errors->first('email') }}</div>
@endif
								</div>
							</div>

							<div class="row form-group">
							<div class="col-md-12">
								<input type="password" class="form-control" placeholder="Password" name="psw">
								<span class="form-ico pass"><i class="las la-lock"></i></span>
								 @if ($errors->has('psw'))
        <div class="error" style="color:red;">{{ $errors->first('psw') }}</div>
@endif
								</div>
							</div>
							
							<div class="row form-group">

							<div class="col-md-12">
								<button class="btn btn-default" type="submit">LOGIN</button>
								<div class="forgot"><a href="{{ url('forgot-password') }}">Forgot Password?</a> <span><a href="{{ url('register') }}">Register Now</a></span></div>
							</div>
							</div>
						</form>
						<div class="row google">
						<div class="col-md-12">
						
							</div>
						</div>

					</div>
				</div>


			</div>
		</div>
	</div>

 
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
  	<script src="{{ asset('employeeassets/js/core/jquery.3.2.1.min.js')}}"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js" integrity="sha384-OgVRvuATP1z7JjHLkuOU7Xw704+h835Lr+6QL9UvYjZE3Ipu6Tp75j7Bh/kR0JKI" crossorigin="anonymous"></script>
  <script src="{{ asset('addtohomescreen/addtohomescreen.min.js')}}"></script>
 <script>
addToHomescreen();
</script>
  

  </body>
</html>
