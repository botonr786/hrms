<!doctype html>
<html lang="en">

	<head>
		<!-- Required meta tags -->
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

		<!-- Bootstrap CSS -->
		<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css"
			integrity="sha384-9aIt2nRpC12Uk9gS9baDl411NQApFmC26EwAOH8WgZl5MYYxFfc+NcPb1dKGj7Sk" crossorigin="anonymous">
		<link rel="stylesheet" href="{{ asset('css/style.css')}}">
		<link rel="stylesheet" href="{{ asset('css/line-awesome.min.css')}}">
		<style>
			body {
				background: #fff;
				position: relative;
			}

			.dash-body .col-lg-2.col-xl-2.col-md-4.col-sm-6.col-12 {
				padding-left: 0;
				padding-right: 0;
			}

			header {
				background: #fff;
			}

			footer {
				background: #3d3e3e;
			}

		</style>
		<link rel="icon" href="{{ asset('img/favicon.png')}}" type="image/x-icon" />
		<title>HRMS-CLIMBR</title>
	</head>

	<body>
		<header class="topbar">
			<div class="container-fluid">
				<div class="row">

					<div class="col-md-3 col-2">
						<a href="{{ url('employerdashboard')}}">
							<h2 style="color:#000;"><img src="{{ asset('img/logo.png')}}" alt="" width="240"
									style="    margin-top: 28px;"></h2>
						</a>
					</div>

					<div class="col-md-9 col-10">
						<ul class="right-optn" style="margin:0">
							<li>

								@if(Session::get('admin_userp_user_type')=='user')

								@else
								
								<a class="res-round"><img src="@if(Session::get('user_type')=='employer')
                            	
							{{ asset($Roledata->logo) }}
                            	@else
                                
							{{ asset($Roledata->profileimage) }}
                                @endif " alt="" style="width: 90px;border-radius: 50%; height:90px"></a>
								@endif


							</li>
							<li><a href="#">
									@if(Session::get('admin_userp_user_type')=='user')
									<?php
					 $email = Session::get('emp_email'); 
				 $Roledata = DB::table('registration')      
                 ->where('status', '=', 'active')
                  ->where('email','=',$email) 
                  ->first();
                  ?>
									<h3>{{$Roledata->f_name }} {{$Roledata->l_name }}</h3>
									<p>{{$email }}</p>
									<p>{{$Roledata->p_no }}</p>

									@elseif(Session::get('user_type')=='employer')
									<?php
				 $email = Session::get('emp_email'); 
				 $Roledata = DB::table('registration')      
                 ->where('status', '=', 'active')
                  ->where('email','=',$email) 
                  ->first();
                  ?>

									<h3>{{$Roledata->f_name }} {{$Roledata->l_name }}</h3>
									<p>{{$email }}</p>
									<p>{{$Roledata->p_no }}</p>

									@else

									<h3>{{$Roledata->emp_fname }} {{$Roledata->emp_mname }} {{$Roledata->emp_lname }}
									</h3>
									<p>{{$Roledata->em_email }}</p>
									<p>{{$Roledata->em_phone }}</p>
									@endif
								</a></li>

							@if(Session::get('admin_userp_user_type')=='user')
							<li><a href="{{url('mainuesrLogout')}}"><i class="las la-power-off"></i></a></li>
							@else
							<li><a href="{{url('mainLogout')}}"><i class="las la-power-off"></i></a></li>
							@endif
						</ul>
					</div>
				</div>
			</div>
		</header>


		<div class="dash-body">
			<div class="container-fluid">
				<div class="row"><?php //dd(Session::get('user_type')); ?>
					@if(Session::get('admin_userp_user_type')=='user')

					<?php
	
	 $member = Session::get('admin_userpp_member'); 
		
         $Roles_auth=DB::table('role_authorization_admin_emp')->where('member_id','=',$member)->get();
			$arrrole=array();
			foreach($Roles_auth as $valrol){
				$arrrole[]=$valrol->module_name;
			}


 $emaiggl = Session::get('emp_email'); 
				 $Roledataemil = DB::table('registration')      
                 
                  ->where('email','=',$emaiggl) 
                  ->where('status','=','active') 
                  ->first();

				  //dd($Roledataemil);
		?>


					<div class="col-lg-2 col-xl-2 col-md-3 col-sm-6 col-12 pl0 pr0">
						<?php 
if(in_array('8', $arrrole))
{
	
	?>
						<a href="{{url('companydashboard')}}">
							<?php
}else{
	?>
							<a href="#">
								<?php
}
?>

								<div class="dash-inr">



									<div class="dash-icon">
										<img src="{{ asset('img/company.png')}}" alt="" style="width:50px;">
									</div>


									<div class="dash-name">Organisation Profile</div>


								</div>

							</a>
					</div>


					<div class="col-lg-2 col-xl-2 col-md-3 col-sm-6 col-12 pl0 pr0">
						<?php 
				  if(!empty($Roledataemil->verify)){  if($Roledataemil->verify == "approved"){ 
if(in_array('7', $arrrole))
{
	
	?>
						<a href="{{url('settingdashboard')}}">
							<?php
}else{
	?>
							<a href="{{url('settingdashboard')}}">
								<?php
}
?>
								<?php }else{
				     
				     ?>
								<a href="{{url('settingdashboard')}}">
									<?php } } ?>

									<div class="dash-inr">


										<div class="dash-icon">
											<img src="{{ asset('img/settings.png')}}" alt="" style="width:50px;">
										</div>


										<div class="dash-name">Settings</div>


									</div>

								</a>
					</div>

					<div class="col-lg-2 col-xl-2 col-md-3 col-sm-6 col-12 pl0 pr0">
						<?php if(!empty($Roledataemil->verify)){  if($Roledataemil->verify == "approved"){ ?>
						<?php 
if(in_array('2', $arrrole))
{
	
	?>
						<a href="{{url('recruitmentdashboard')}}">
							<?php
}else{
	?>
							<a href="{{url('recruitmentdashboard')}}">
								<?php
}
?>
								<?php }else{
				     
				     ?>
								<a href="{{url('recruitmentdashboard')}}">
									<?php } } ?>

									<div class="dash-inr">

										<div class="dash-icon">
											<img src="{{ asset('img/recruitment.png')}}" alt="" width="50">
										</div>

										<div class="dash-name">Recruitment</div>


									</div>

								</a>
					</div>


					<div class="col-lg-2 col-xl-2 col-md-3 col-sm-6 col-12 pl0 pr0">
						<?php if(!empty($Roledataemil->verify)){  if($Roledataemil->verify == "approved"){ ?>
						<?php 
if(in_array('1', $arrrole))
{
	
	?>
						<a href="{{url('employee/dashboard')}}">
							<?php
}else{
	?>
							<a href="#">
								<?php
}
?>
								<?php }else{
				     
				     ?>
								<a href="#">
									<?php } } ?>


									<div class="dash-inr">



										<div class="dash-icon">
											<img src="{{ asset('img/employee.png')}}" alt="" style="width:50px;">
										</div>

										<div class="dash-name">Employee</div>

									</div>

								</a>
					</div>


					<div class="col-lg-2 col-xl-2 col-md-3 col-sm-6 col-12 pl0 pr0">
						<?php if(!empty($Roledataemil->verify)){  if($Roledataemil->verify == "approved"){ ?>
						<?php 
if(in_array('10', $arrrole))
{
	
	?>
						<a href="{{url('useraccessdashboard')}}">
							<?php
}else{
	?>
							<a href="#">
								<?php
}
?>
								<?php }else{
				     
				     ?>
								<a href="#">
									<?php } } ?>

									<div class="dash-inr">

										<div class="dash-icon">
											<img src="{{ asset('img/user-access.png')}}" alt="" style="width:50px;">
										</div>


										<div class="dash-name">User Access</div>

									</div>

								</a>
					</div>

					<div class="col-lg-2 col-xl-2 col-md-3 col-sm-6 col-12 pl0 pr0">
						<?php if(!empty($Roledataemil->verify)){  if($Roledataemil->verify == "approved" && $Roledataemil->licence == "yes"){ ?>
						<?php 
if(in_array('11', $arrrole))
{
	
	?>
						<a href="{{url('organogramdashboard')}}">
							<?php
}else{
	?>
							<a href="#">
								<?php
}
?>
								<?php }else{
				     
				     ?>
								<a href="#">
									<?php } } ?>


									<div class="dash-inr">


										<div class="dash-icon">
											<img src="{{ asset('img/chart.png')}}" alt="" style="width:50px;">
										</div>

										<div class="dash-name">Organogram Chart</div>
									</div>


								</a>
					</div>

					<div class="col-lg-2 col-xl-2 col-md-3 col-sm-6 col-12 pl0 pr0">
						<?php //if(!empty($Roledataemil->verify)){  if($Roledataemil->verify == "approved" && $Roledataemil->licence == "yes"){ ?>
						<?php if(!empty($Roledataemil->verify)){  if($Roledataemil->verify == "approved"){ ?>
						<?php 
if(in_array('4', $arrrole))
{
	
	?>
						<a href="{{url('holidaydashboard')}}">
							<?php
}else{
	?>
							<a href="{{url('holidaydashboard')}">
				<?php
}
?>
<?php }else{
				     
				     ?>
				     	<a href="{{url('holidaydashboard')}">
				     <?php } } ?>
					
						<div class="dash-inr">
						
						<div class="dash-icon">
							<img src="{{ asset('img/holiday.png')}}" style="width:50px;">
					</div>

					<div class="dash-name">Holiday Management</div>
				</div>

				</a>
			</div>
			<div class="col-lg-2 col-xl-2 col-md-3 col-sm-6 col-12 pl0 pr0">
				<?php if(!empty($Roledataemil->verify)){  if($Roledataemil->verify == "approved" && $Roledataemil->licence == "yes"){ ?>
				<?php 
if(in_array('3', $arrrole))
{
	
	?>
				<a href="{{url('leavedashboard')}}">
					<?php
}else{
	?>
					<a href="#">
						<?php
}
?>
						<?php }else{
				     
				     ?>
						<a href="#">
							<?php } } ?>

							<div class="dash-inr">


								<div class="dash-icon">
									<img src="{{ asset('img/leave.png')}}" style="width:50px;">
								</div>

								<div class="dash-name">Leave Management</div>
							</div>

						</a>
			</div>

			<div class="col-lg-2 col-xl-2 col-md-3 col-sm-6 col-12 pl0 pr0">
				<?php if(!empty($Roledataemil->verify)){  if($Roledataemil->verify == "approved" && $Roledataemil->licence == "yes"){ ?>
				<?php 
if(in_array('9', $arrrole))
{
	
	?>
				<a href="{{url('rotadashboard')}}">
					<?php
}else{
	?>
					<a href="#">
						<?php
}
?>
						<?php }else{
				     
				     ?>
						<a href="#">
							<?php } } ?>


							<div class="dash-inr">

								<div class="dash-icon">
									<img src="{{ asset('img/rota.png')}}" alt="" style="width:50px;">
								</div>

								<div class="dash-name">Rota</div>
							</div>

						</a>
			</div>
			<div class="col-lg-2 col-xl-2 col-md-3 col-sm-6 col-12 pl0 pr0">
				<?php if(!empty($Roledataemil->verify)){  if($Roledataemil->verify == "approved" && $Roledataemil->licence == "yes"){ ?>
				<?php 
if(in_array('6', $arrrole))
{
	
	?>
				<a href="{{url('attendancedashboard')}}">
					<?php
}else{
	?>
					<a href="#">
						<?php
}
?>
						<?php }else{
				     
				     ?>
						<a href="#">
							<?php } } ?>


							<div class="dash-inr">

								<div class="dash-icon">
									<img src="{{ asset('img/attendance.png')}}" alt="" style="width:50px;">
								</div>

								<div class="dash-name">Attendance</div>
							</div>

						</a>
			</div>
			<div class="col-lg-2 col-xl-2 col-md-3 col-sm-6 col-12 pl0 pr0">
				<?php if(!empty($Roledataemil->verify)){  if($Roledataemil->verify == "approved" && $Roledataemil->licence == "yes"){ ?>
				<?php 
if(in_array('5', $arrrole))
{
	
	?>
				<a href="{{url('leaveapprovedashboard')}}">
					<?php
}else{
	?>
					<a href="#">
						<?php
}
?>
						<?php }else{
				     
				     ?>
						<a href="#">
							<?php } } ?>
							<div class="dash-inr">

								<div class="dash-icon">
									<img src="{{ asset('img/approver.png')}}" alt="" style="width:50px;">
								</div>

								<div class="dash-name">Leave Approver</div>
							</div>

						</a>
			</div>









			<div class="col-lg-2 col-xl-2 col-md-3 col-sm-6 col-12 pl0 pr0">
				<a href="#">

					<div class="dash-inr">

						<div class="dash-icon">
							<img src="{{ asset('img/payroll.png')}}" alt="" style="width:50px;">
						</div>

						<div class="dash-name">Payroll</div>
					</div>


				</a>
			</div>


			<div class="col-lg-2 col-xl-2 col-md-3 col-sm-6 col-12 pl0 pr0">


				<?php
	  $email = Session::get('emp_email'); 
	  $ggRoledata = DB::table('registration')      
                 
                  ->where('email','=',$email) 
				  ->where('status','=','active') 
                  ->first();
	 $member = Session::get('admin_userpp_member'); 
		
         $Roles_hhauth=DB::table('tareq_app')->where('ref_id','=',$member)->where('emid','=',$ggRoledata->reg)->first();
       
         if(!empty($Roles_hhauth->invoice) && $Roles_hhauth->invoice=='Yes'){
         ?>

				<a href="{{url('billingdashboard')}}">
					<?php
         }else{
         ?>
					<a href="#">
						<?php
}
?>
						<div class="dash-inr">


							<div class="dash-icon">
								<img src="{{ asset('img/money.png')}}" alt="" style="width:50px;">
							</div>

							<div class="dash-name">Invoice</div>
						</div>


					</a>
			</div>



			<div class="col-lg-2 col-xl-2 col-md-3 col-sm-6 col-12 pl0 pr0">
				<?php if(!empty($Roledataemil->verify)){  if($Roledataemil->verify == "approved"){ ?>
				<?php 
if(in_array('12', $arrrole))
{
	
	?>
				<a href="{{url('documentsdashboard')}}">
					<?php
}else{
	?>
					<a href="#">
						<?php
}
?>
						<?php }else{
				     
				     ?>
						<a href="#">
							<?php } } ?>
							<div class="dash-inr">

								<div class="dash-icon">
									<img src="{{ asset('img/document.png')}}" alt="" style="width:50px;">
								</div>

								<div class="dash-name">Documents</div>
							</div>

						</a>
			</div>


			<div class="col-lg-2 col-xl-2 col-md-3 col-sm-6 col-12 pl0 pr0">
				<?php if(!empty($Roledataemil->verify)){  if($Roledataemil->verify == "approved"){ ?>
				<?php 
if(in_array('13', $arrrole))
{
	
	?>
				<a href="{{url('dashboarddetails')}}">
					<?php
}else{
	?>
					<a href="#">
						<?php
}
?>
						<?php }else{
				     
				     ?>
						<a href="#">
							<?php } } ?>
							<div class="dash-inr">

								<div class="dash-icon">
									<img src="{{ asset('img/document.png')}}" alt="" style="width:50px;">
								</div>

								<div class="dash-name">Sponsor Compliance</div>
							</div>


						</a>
			</div>
			<div class="col-lg-2 col-xl-2 col-md-3 col-sm-6 col-12 pl0 pr0">
				<a href="{{url('interroatadashboard')}}">
					<div class="dash-inr">


						<div class="dash-icon">
							<img src="{{ asset('img/rota.png')}}" alt="" style="width:50px;">
						</div>
						<div class="dash-name">Internal User Rota </div>
					</div>

				</a>
			</div>

			<div class="col-lg-2 col-xl-2 col-md-3 col-sm-6 col-12 pl0 pr0">
				<a href="{{url('organisationdashboard')}}">
					<div class="dash-inr">


						<div class="dash-icon">
							<img src="{{ asset('img/company.png')}}" alt="" style="width:50px;">
						</div>
						<div class="dash-name">Organisation Status</div>
					</div>

				</a>
			</div>






			<div class="col-lg-2 col-xl-2 col-md-3 col-sm-6 col-12 pl0 pr0">
				<a href="{{url('user-check-organisation')}}">
					<div class="dash-inr">


						<div class="dash-icon">
							<img src="{{ asset('img/employment-corner.png')}}" alt="" style="width:50px;">
						</div>
						<div class="dash-name">Organisation Assign</div>
					</div>

				</a>
			</div>




			<div class="col-lg-2 col-xl-2 col-md-3 col-sm-6 col-12 pl0 pr0">
				<a href="{{url('complaindashboard')}}">
					<div class="dash-inr">


						<div class="dash-icon">
							<img src="{{ asset('img/rota.png')}}" alt="" style="width:50px;">
						</div>

						<div class="dash-name">Complain </div>
					</div>


				</a>
			</div>



			<div class="col-lg-2 col-xl-2 col-md-3 col-sm-6 col-12 pl0 pr0">
				<a href="{{url('user-check-employee')}}">
					<div class="dash-inr">

						<div class="dash-icon">
							<img src="{{ asset('img/employment-corner.png')}}" alt="" style="width:50px;">
						</div>

						<div class="dash-name">Employee Corner</div>
					</div>

				</a>
			</div>


			<div class="col-lg-2 col-xl-2 col-md-3 col-sm-6 col-12 pl0 pr0">
				<a href="{{url('taskdashboard')}}">
					<div class="dash-inr">


						<div class="dash-icon">
							<img src="{{ asset('img/rota.png')}}" alt="" style="width:50px;">
						</div>
						<div class="dash-name">Tasks </div>
					</div>

				</a>
			</div>
			@else
			@if(Session::get('user_type')=='employer')<?php //dd($Roledata);?>
			<div class="col-lg-2 col-xl-2 col-md-3 col-sm-6 col-12 pl0 pr0">
				<a href="{{url('companydashboard')}}">
					<div class="dash-inr">

						<div class="dash-icon">
							<img src="{{ asset('img/company.png')}}" alt="" style="width:50px;">
						</div>

						<div class="dash-name">Organisation Profile</div>
					</div>

				</a>
			</div>
			<div class="col-lg-2 col-xl-2 col-md-3 col-sm-6 col-12 pl0 pr0">
				<?php if(!empty($Roledata->verify)){  if($Roledata->verify == "approved"){ ?>
				<a href="{{url('settingdashboard')}}">
					<?php }else{
				     
				     ?>
					<a href="{{url('settingdashboard')}}">
						<?php } } ?>

						<div class="dash-inr">

							<div class="dash-icon">
								<img src="{{ asset('img/settings.png')}}" alt="" style="width:50px;">
							</div>

							<div class="dash-name">Settings</div>
						</div>

					</a>
			</div>

			<div class="col-lg-2 col-xl-2 col-md-3 col-sm-6 col-12 pl0 pr0">
				<?php //if(!empty($Roledata->verify)){  if($Roledata->verify == "approved" && $Roledata->licence == "yes"){ ?>
				<?php if(!empty($Roledata->verify)){  if($Roledata->verify == "approved"){ ?>
				<a href="{{url('recruitmentdashboard')}}">
					<?php }else{
				     
				     ?>
					<a href="{{url('recruitmentdashboard')}}">
						<?php } } ?>
						<div class="dash-inr">

							<div class="dash-icon">
								<img src="{{ asset('img/recruitment.png')}}" alt="" width="50">
							</div>

							<div class="dash-name">Recruitment</div>
						</div>


					</a>
			</div>
			<div class="col-lg-2 col-xl-2 col-md-3 col-sm-6 col-12 pl0 pr0">

				<?php if(!empty($Roledata->verify)){  if($Roledata->verify == "approved"){ ?>
				<a href="{{url('employee/dashboard')}}">
					<?php }else{
				     
				     ?>
					<a href="{{url('employee/dashboard')}}">
						<?php } } ?>
						<div class="dash-inr">


							<div class="dash-icon">
								<img src="{{ asset('img/employee.png')}}" alt="" style="width:50px;">
							</div>

							<div class="dash-name">Employee</div>
						</div>

					</a>
			</div>
			<div class="col-lg-2 col-xl-2 col-md-3 col-sm-6 col-12 pl0 pr0">
				<?php if(!empty($Roledata->verify)){  if($Roledata->verify == "approved"){ ?>
				<a href="{{url('useraccessdashboard')}}">
					<?php }else{
				     
				     ?>
					<a href="{{url('useraccessdashboard')}}">
						<?php } } ?>


						<div class="dash-inr">

							<div class="dash-icon">
								<img src="{{ asset('img/user-access.png')}}" alt="" style="width:50px;">
							</div>

							<div class="dash-name">User Access</div>
						</div>


					</a>
			</div>

			<div class="col-lg-2 col-xl-2 col-md-3 col-sm-6 col-12 pl0 pr0">
				<?php //if(!empty($Roledata->verify)){  if($Roledata->verify == "approved" && $Roledata->licence == "yes"){ ?>
				<?php if(!empty($Roledata->verify)){  if($Roledata->verify == "approved"){ ?>
				<a href="{{url('organogramdashboard')}}">
					<?php }else{
				     
				     ?>
					<a href="#">
						<?php } } ?>
						<div class="dash-inr">

							<div class="dash-icon">
								<img src="{{ asset('img/chart.png')}}" alt="" style="width:50px;">
							</div>

							<div class="dash-name">Organogram Chart</div>
						</div>

					</a>
			</div>
			<div class="col-lg-2 col-xl-2 col-md-3 col-sm-6 col-12 pl0 pr0">
				<?php // if(!empty($Roledata->verify)){  if($Roledata->verify == "approved" && $Roledata->licence == "yes"){ ?>

				<?php if(!empty($Roledata->verify)){  if($Roledata->verify == "approved"){ ?>

				<a href="{{url('holidaydashboard')}}">
					<?php }else{
				     
				     ?>
					<a href="#">
						<?php } } ?>

						<div class="dash-inr">

							<div class="dash-icon">
								<img src="{{ asset('img/holiday.png')}}" style="width:50px;">
							</div>

							<div class="dash-name">Holiday Management</div>
						</div>

					</a>
			</div>
			<div class="col-lg-2 col-xl-2 col-md-3 col-sm-6 col-12 pl0 pr0">
				<?php //if(!empty($Roledata->verify)){  if($Roledata->verify == "approved" && $Roledata->licence == "yes"){ ?>
				<?php if(!empty($Roledata->verify)){  if($Roledata->verify == "approved"){ ?>
				<a href="{{url('leavedashboard')}}">
					<?php }else{
				     
				     ?>
					<a href="#">
						<?php } } ?>

						<div class="dash-inr">


							<div class="dash-icon">
								<img src="{{ asset('img/leave.png')}}" style="width:50px;">
							</div>

							<div class="dash-name">Leave Management</div>
						</div>

					</a>
			</div>

			<div class="col-lg-2 col-xl-2 col-md-3 col-sm-6 col-12 pl0 pr0">
				<?php //if(!empty($Roledata->verify)){  if($Roledata->verify == "approved" && $Roledata->licence == "yes"){ ?>
				<?php if(!empty($Roledata->verify)){  if($Roledata->verify == "approved"){ ?>
				<a href="{{url('rotadashboard')}}">
					<?php }else{
				     
				     ?>
					<a href="#">
						<?php } } ?>
						<div class="dash-inr">

							<div class="dash-icon">
								<img src="{{ asset('img/rota.png')}}" alt="" style="width:50px;">
							</div>

							<div class="dash-name">Rota</div>
						</div>


					</a>
			</div>

			<div class="col-lg-2 col-xl-2 col-md-3 col-sm-6 col-12 pl0 pr0">
				<?php //if(!empty($Roledata->verify)){  if($Roledata->verify == "approved" && $Roledata->licence == "yes"){ ?>
				<?php if(!empty($Roledata->verify)){  if($Roledata->verify == "approved"){ ?>
				<a href="{{url('attendance/dashboard')}}">
					<?php }else{
				     
				     ?>
					<a href="#">
						<?php } } ?>

						<div class="dash-inr">

							<div class="dash-icon">
								<img src="{{ asset('img/attendance.png')}}" alt="" style="width:50px;">
							</div>

							<div class="dash-name">Attendance</div>
						</div>

					</a>
			</div>
			<div class="col-lg-2 col-xl-2 col-md-3 col-sm-6 col-12 pl0 pr0">
				<?php //if(!empty($Roledata->verify)){  if($Roledata->verify == "approved" && $Roledata->licence == "yes"){ ?>
				<?php if(!empty($Roledata->verify)){  if($Roledata->verify == "approved"){ ?>
				<a href="{{url('leaveapprovedashboard')}}">
					<?php }else{
				     
				     ?>
					<a href="#">
						<?php } } ?>

						<div class="dash-inr">



							<div class="dash-icon">
								<img src="{{ asset('img/approver.png')}}" alt="" style="width:50px;">
							</div>

							<div class="dash-name">Leave Approver</div>
						</div>


					</a>
			</div>









			<div class="col-lg-2 col-xl-2 col-md-3 col-sm-6 col-12 pl0 pr0">
				<a href="{{url('payroll-home-dashboard')}}">
					<div class="dash-inr">

						<div class="dash-icon">
							<img src="{{ asset('img/payroll.png')}}" alt="" style="width:50px;">
						</div>

						<div class="dash-name">Payroll</div>
					</div>

				</a>
			</div>



			<div class="col-lg-2 col-xl-2 col-md-3 col-sm-6 col-12 pl0 pr0">
				<?php if(!empty($Roledata->verify)){  if($Roledata->verify == "approved"){ ?>
				<a href="{{url('billingorganizationdashboard')}}">
					<?php }else{
				     
				     ?>
					<a href="#">
						<?php } } ?>
						<div class="dash-inr">

							<div class="dash-icon">
								<img src="{{ asset('img/money.png')}}" alt="" style="width:50px;">
							</div>

							<div class="dash-name">Billing</div>
						</div>

					</a>
			</div>



			<div class="col-lg-2 col-xl-2 col-md-3 col-sm-6 col-12 pl0 pr0">
				<?php if(!empty($Roledata->verify)){  if($Roledata->verify == "approved"){ ?>
				<a href="{{url('documentsdashboard')}}">
					<?php }else{
				     
				     ?>
					<a href="#">
						<?php } } ?>
						<div class="dash-inr">

							<div class="dash-icon">
								<img src="{{ asset('img/document.png')}}" alt="" style="width:50px;">
							</div>

							<div class="dash-name">Documents</div>
						</div>

					</a>
			</div>
			<div class="col-lg-2 col-xl-2 col-md-3 col-sm-6 col-12 pl0 pr0">
				<?php if(!empty($Roledata->verify)){  if($Roledata->verify == "approved"){ ?>
				<a href="{{url('dashboarddetails')}}">
					<?php }else{
				     
				     ?>
					<a href="#">
						<?php } } ?>

						<div class="dash-inr">

							<div class="dash-icon">
								<img src="{{ asset('img/document.png')}}" alt="" style="width:50px;">
							</div>

							<div class="dash-name">Sponsor Compliance</div>
						</div>

					</a>
			</div>
			<div class="col-lg-2 col-xl-2 col-md-3 col-sm-6 col-12 pl0 pr0">
				<a href="{{url('user-check-employee')}}">
					<div class="dash-inr">

						<div class="dash-icon">
							<img src="{{ asset('img/employment-corner.png')}}" alt="" style="width:50px;">
						</div>

						<div class="dash-name">Employee Corner</div>
					</div>

				</a>
			</div>

			<div class="col-lg-2 col-xl-2 col-md-3 col-sm-6 col-12 pl0 pr0">
				<a href="{{url('taskdashboard')}}">
					<div class="dash-inr">


						<div class="dash-icon">
							<img src="{{ asset('img/rota.png')}}" alt="" style="width:50px;">
						</div>
						<div class="dash-name">Tasks </div>
					</div>

				</a>
			</div>
			@else
			<?php
			$arrrole=array();
			foreach($Roles_auth as $valrol){
				$arrrole[]=$valrol->module_name;
			}
;
		?>

			<?php if(!empty($Roledata->verify_status)){  if($Roledata->verify_status == "approved"){ ?>



			<div class="col-lg-2 col-xl-2 col-md-3 col-sm-6 col-12 pl0 pr0">
				<?php 
if(in_array('8', $arrrole))
{
	
	?>
				<a href="{{url('companydashboard')}}">
					<?php
}else{
	?>
					<a href="#">
						<?php
}
?>

						<div class="dash-inr">

							<div class="dash-icon">
								<img src="{{ asset('img/company.png')}}" alt="" style="width:50px;">
							</div>

							<div class="dash-name">Organisation Profile</div>

						</div>

					</a>
			</div>


			<div class="col-lg-2 col-xl-2 col-md-3 col-sm-6 col-12 pl0 pr0">
				<?php 
if(in_array('7', $arrrole))
{
	
	?>
				<a href="{{url('settingdashboard')}}">
					<?php
}else{
	?>
					<a href="{{url('settingdashboard')}}">
						<?php
}
?>

						<div class="dash-inr">

							<div class="dash-icon">
								<img src="{{ asset('img/settings.png')}}" alt="" style="width:50px;">
							</div>

							<div class="dash-name">Settings</div>
						</div>

					</a>
			</div>

			<div class="col-lg-2 col-xl-2 col-md-3 col-sm-6 col-12 pl0 pr0">
				<?php 
if(in_array('2', $arrrole))
{
	
	?>
				<a href="{{url('recruitmentdashboard')}}">
					<?php
}else{
	?>
					<a href="#">
						<?php
}
?>
						<div class="dash-inr">

							<div class="dash-icon">
								<img src="{{ asset('img/recruitment.png')}}" alt="" width="50">
							</div>

							<div class="dash-name">Recruitment</div>
						</div>


					</a>
			</div>


			<div class="col-lg-2 col-xl-2 col-md-3 col-sm-6 col-12 pl0 pr0">
				<?php 
if(in_array('1', $arrrole))
{
	
	?>
				<a href="{{url('employee/dashboard')}}">
					<?php
}else{
	?>
					<a href="#">
						<?php
}
?>

						<div class="dash-inr">

							<div class="dash-icon">
								<img src="{{ asset('img/employee.png')}}" alt="" style="width:50px;">
							</div>

							<div class="dash-name">Employee</div>
						</div>

					</a>
			</div>


			<div class="col-lg-2 col-xl-2 col-md-3 col-sm-6 col-12 pl0 pr0">
				<?php 
if(in_array('10', $arrrole))
{
	
	?>
				<a href="{{url('useraccessdashboard')}}">
					<?php
}else{
	?>
					<a href="#">
						<?php
}
?>

						<div class="dash-inr">

							<div class="dash-icon">
								<img src="{{ asset('img/user-access.png')}}" alt="" style="width:50px;">
							</div>

							<div class="dash-name">User Access</div>
						</div>

					</a>
			</div>

			<div class="col-lg-2 col-xl-2 col-md-3 col-sm-6 col-12 pl0 pr0">
				<?php 
if(in_array('11', $arrrole))
{
	
	?>
				<a href="{{url('organogramdashboard')}}">
					<?php
}else{
	?>
					<a href="#">
						<?php
}
?>

						<div class="dash-inr">

							<div class="dash-icon">
								<img src="{{ asset('img/chart.png')}}" alt="" style="width:50px;">
							</div>

							<div class="dash-name">Organogram Chart</div>
						</div>

					</a>
			</div>

			<div class="col-lg-2 col-xl-2 col-md-3 col-sm-6 col-12 pl0 pr0">
				<?php 
if(in_array('4', $arrrole))
{
	
	?>
				<a href="{{url('holidaydashboard')}}">
					<?php
}else{
	?>
					<a href="{{url('holidaydashboard')}}">
						<?php
}
?>

						<div class="dash-inr">

							<div class="dash-icon">
								<img src="{{ asset('img/holiday.png')}}" style="width:50px;">
							</div>

							<div class="dash-name">Holiday Management</div>
						</div>

					</a>
			</div>
			<div class="col-lg-2 col-xl-2 col-md-3 col-sm-6 col-12 pl0 pr0">
				<?php 
if(in_array('3', $arrrole))
{
	
	?>
				<a href="{{url('leavedashboard')}}">
					<?php
}else{
	?>
					<a href="#">
						<?php
}
?>

						<div class="dash-inr">

							<div class="dash-icon">
								<img src="{{ asset('img/leave.png')}}" style="width:50px;">
							</div>

							<div class="dash-name">Leave Management</div>
						</div>

					</a>
			</div>

			<div class="col-lg-2 col-xl-2 col-md-3 col-sm-6 col-12 pl0 pr0">
				<?php 
if(in_array('9', $arrrole))
{
	
	?>
				<a href="{{url('rotadashboard')}}">
					<?php
}else{
	?>
					<a href="#">
						<?php
}
?>

						<div class="dash-inr">

							<div class="dash-icon">
								<img src="{{ asset('img/rota.png')}}" alt="" style="width:50px;">
							</div>

							<div class="dash-name">Rota</div>
						</div>

					</a>
			</div>
			<div class="col-lg-2 col-xl-2 col-md-3 col-sm-6 col-12 pl0 pr0">
				<?php 
if(in_array('6', $arrrole))
{
	
	?>
				<a href="{{url('attendancedashboard')}}">
					<?php
}else{
	?>
					<a href="#">
						<?php
}
?>

						<div class="dash-inr">

							<div class="dash-icon">
								<img src="{{ asset('img/attendance.png')}}" alt="" style="width:50px;">
							</div>

							<div class="dash-name">Attendance</div>
						</div>

					</a>
			</div>
			<div class="col-lg-2 col-xl-2 col-md-3 col-sm-6 col-12 pl0 pr0">
				<?php 
if(in_array('5', $arrrole))
{
	
	?>
				<a href="{{url('leaveapprovedashboard')}}">
					<?php
}else{
	?>
					<a href="#">
						<?php
}
?>
						<div class="dash-inr">

							<div class="dash-icon">
								<img src="{{ asset('img/approver.png')}}" alt="" style="width:50px;">
							</div>

							<div class="dash-name">Leave Approver</div>
						</div>

					</a>
			</div>









			<div class="col-lg-2 col-xl-2 col-md-3 col-sm-6 col-12 pl0 pr0">
				<a href="#">

					<div class="dash-inr">

						<div class="dash-icon">
							<img src="{{ asset('img/payroll.png')}}" alt="" style="width:50px;">
						</div>

						<div class="dash-name">Payroll</div>
					</div>

				</a>
			</div>


			<div class="col-lg-2 col-xl-2 col-md-3 col-sm-6 col-12 pl0 pr0">



				<a href="#">
					<div class="dash-inr">

						<div class="dash-icon">
							<img src="{{ asset('img/money.png')}}" alt="" style="width:50px;">
						</div>

						<div class="dash-name">Billing</div>
					</div>

				</a>
			</div>



			<div class="col-lg-2 col-xl-2 col-md-3 col-sm-6 col-12 pl0 pr0">
				<?php 
if(in_array('12', $arrrole))
{
	
	?>
				<a href="{{url('documentsdashboard')}}">
					<?php
}else{
	?>
					<a href="#">
						<?php
}
?>
						<div class="dash-inr">


							<div class="dash-icon">
								<img src="{{ asset('img/document.png')}}" alt="" style="width:50px;">
							</div>
							<div class="dash-name">Documents</div>
						</div>

					</a>
			</div>


			<div class="col-lg-2 col-xl-2 col-md-3 col-sm-6 col-12 pl0 pr0">
				<?php 
if(in_array('13', $arrrole))
{
	
	?>
				<a href="{{url('dashboarddetails')}}">
					<?php
}else{
	?>
					<a href="#">
						<?php
}
?>
						<div class="dash-inr">


							<div class="dash-icon">
								<img src="{{ asset('img/document.png')}}" alt="" style="width:50px;">
							</div>

							<div class="dash-name">Sponsor Compliance</div>
						</div>

					</a>
			</div>





			<?php } } ?>

			<div class="col-lg-2 col-xl-2 col-md-3 col-sm-6 col-12 pl0 pr0">
				<a href="{{url('employeecornerdashboard')}}">
					<div class="dash-inr">

						<div class="dash-icon">
							<img src="{{ asset('img/employment-corner.png')}}" alt="" style="width:50px;">
						</div>

						<div class="dash-name">Employee Corner</div>
					</div>

				</a>
			</div>
			<?php if(!empty($Roledata->verify_status)){  if($Roledata->verify_status == "approved"){ ?>
			<div class="col-lg-2 col-xl-2 col-md-3 col-sm-6 col-12 pl0 pr0">
				<a href="{{url('taskdashboard')}}">
					<div class="dash-inr">


						<div class="dash-icon">
							<img src="{{ asset('img/rota.png')}}" alt="" style="width:50px;">
						</div>
						<div class="dash-name">Tasks </div>
					</div>

				</a>
			</div>
			<?php } } ?>

			@endif
			@endif
		</div>




		</div>
		</div>
		<footer>
			<div class="col-md-12">
				<p>&copy; 2023 CLIMBR - HRMS | All Right Reserved | Developed by <a href="https://eitpl.in/"
						target="_blank">E.I.T</a></p>
			</div>
		</footer> <!-- Optional JavaScript -->
		<!-- jQuery first, then Popper.js, then Bootstrap JS -->
		<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"
			integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj"
			crossorigin="anonymous"></script>
		<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js"
			integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo"
			crossorigin="anonymous"></script>
		<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js"
			integrity="sha384-OgVRvuATP1z7JjHLkuOU7Xw704+h835Lr+6QL9UvYjZE3Ipu6Tp75j7Bh/kR0JKI"
			crossorigin="anonymous"></script>
	</body>

</html>
