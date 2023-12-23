<?php 
	include('../languages/lang_config.php');
	include('config/apply.php');
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="UTF-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
		<title>Admin Login : Haya Photography</title>
		<meta name="description" content="Droopy is a Dashboard & Admin Site Responsive Template by hencework." />
		<meta name="keywords" content="admin, admin dashboard, admin template, cms, crm, Droopy Admin, Droopyadmin, premium admin templates, responsive admin, sass, panel, software, ui, visualization, web app, application" />
		<meta name="author" content="hencework"/>
		
		<!-- Favicon -->
		<link rel="shortcut icon" href="favicon.ico">
		<link rel="icon" href="favicon.ico" type="image/x-icon">
		
		<!-- vector map CSS -->
		<link href="<?php echo SITEURL; ?>admin/assets/vendors/bower_components/jasny-bootstrap/dist/css/jasny-bootstrap.min.css" rel="stylesheet" type="text/css"/>
		<!-- Custom CSS -->
		<link href="<?php echo SITEURL; ?>admin/assets/style/dist/css/style.css" rel="stylesheet" type="text/css">
	</head>
	<body>
		<!--Preloader-->
		<div class="preloader-it">
			<div class="la-anim-1"></div>
		</div>
		<!--/Preloader-->
		
		<div class="wrapper  pa-0">
			<header class="sp-header">
				<div class="sp-logo-wrap pull-left">
					<a href="index.html">
						<img class="brand-img mr-10" src="assets/img/logo.png" alt="brand"/>
						<span class="brand-text">Haya Photography</span>
					</a>
				</div>
				<!-- <div class="form-group mb-0 pull-right">
					<span class="inline-block pr-10">Don't have an account?</span>
					<a class="inline-block btn btn-success  btn-rounded btn-outline" href="signup.html">Sign Up</a>
				</div> -->
				<div class="clearfix"></div>
			</header>
			
			<!-- Main Content -->
			<div class="page-wrapper pa-0 ma-0 auth-page">
				<div class="container-fluid">
					<!-- Row -->
					<div class="table-struct full-width full-height">
						<div class="table-cell vertical-align-middle auth-form-wrap">
							<?php if(isset($_SESSION['login'])){
										echo $_SESSION['login'];
										unset($_SESSION['login']);
									}
								?>
							<div class="auth-form  ml-auto mr-auto no-float">
								<div class="row">
									<div class="col-sm-12 col-xs-12">
										<div class="mb-30">
											<h6 class="text-center nonecase-font txt-grey">You don't have permission access this page </h6>
										</div>	
									</div>	
								</div>
							</div>
						</div>
					</div>
					<!-- /Row -->	
				</div>
				
			</div>
			<!-- /Main Content -->
		<?php 
			if(isset($_POST['submit']))
			{
				//echo "Click";
				$username = $obj->sanitize($conn,$_POST['username']);
				$password = md5($obj->sanitize($conn,$_POST['password']));

				$tbl_name = "tbl_users";
				$where = "username='$username' && password='$password'";

				$query = $obj->select_data($tbl_name,$where);
				$res = $obj->execute_query($conn,$query);
				$count_rows = $obj->num_rows($res);
				if($count_rows>0)
				{
					$_SESSION['login'] = "<div class='success'>".$lang['login_success'].".</div>";
					$_SESSION['user'] = $username;
					header('location:'.SITEURL.'admin/');
				}
				else
				{
					$_SESSION['login'] = "<div class='error'>".$lang['login_fail']."</div>";
					header('location:'.SITEURL.'admin/login.php');
				}
			}
		?>
		</div>
		<!-- /#wrapper -->
		
		<!-- JavaScript -->
		
		<!-- jQuery -->
		<script src="<?php echo SITEURL; ?>admin/assets/vendors/bower_components/jquery/dist/jquery.min.js"></script>
		
		<!-- Bootstrap Core JavaScript -->
		<script src="<?php echo SITEURL; ?>admin/assets/vendors/bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
		<script src="<?php echo SITEURL; ?>admin/assets/vendors/bower_components/jasny-bootstrap/dist/js/jasny-bootstrap.min.js"></script>
		
		<!-- Slimscroll JavaScript -->
		<script src="<?php echo SITEURL; ?>admin/assets/style/dist/js/jquery.slimscroll.js"></script>
		
		<!-- Init JavaScript -->
		<script src="<?php echo SITEURL; ?>admin/assets/style/dist/js/init.js"></script>
	</body>
</html>
