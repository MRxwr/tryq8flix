
		<!-- Main Content -->
			<div class="page-wrapper">
				<div class="container-fluid">
			<?php 
		if(isset($_GET['page']) && !empty($_GET['page']))
		{
			$page = $_GET['page'];
			if($page=='logout')
			{
				unset($_SESSION['user']);
				header('location:'.SITEURL.'admin/login.php');
				die();
			}
			//include('moduls/'.$page.'/'.$page.'.php');
			switch ($page) {
				case 'pages':
					include('moduls/'.$page.'.php');
					break;
	
				case 'add_page':
					include('moduls/'.$page.'.php');
					break;
	
				case 'edit_page':
					include('moduls/'.$page.'.php');
					break;
				case 'categories':
					include('moduls/'.$page.'.php');
					break;
	
				case 'add_category':
					include('moduls/'.$page.'.php');
					break;
	
				case 'edit_category':
					include('moduls/'.$page.'.php');
					break;
					
	
				case 'posts':
					include('moduls/'.$page.'.php');
					break;
	
				case 'add_post':
					include('moduls/'.$page.'.php');
					break;
	
				case 'edit_post':
					include('moduls/'.$page.'.php');
					break;
				
				case 'logout':
					session_destroy();
					header('location:'.SITEURL.'admin');
					break;
				
				default:
					include('moduls/'.$page.'.php');
					break;
			}
		}
		else
		{
			$page="home";
			include('moduls/'.$page.'.php');
		}
		
		
		/*
		switch ($page) {
			case 'categories':
				include('pages/'.$page.'.php');
				break;

			case 'add_category':
				include('pages/'.$page.'.php');
				break;

			case 'edit_category':
				include('pages/'.$page.'.php');
				break;

			case 'posts':
				include('pages/'.$page.'.php');
				break;

			case 'add_post':
				include('pages/'.$page.'.php');
				break;

			case 'edit_post':
				include('pages/'.$page.'.php');
				break;
			
			case 'logout':
				session_destroy();
				header('location:'.SITEURL.'admin');
				break;
			
			default:
				include('pages/'.$page.'.php');
				break;
		}
		*/
	?>
    </div>        
			
			<!-- Footer -->
			<footer class="footer container-fluid pl-30 pr-30">
				<div class="row">
					<div class="col-sm-12">
						<p>2018 &copy; Droopy. Pampered by Hencework</p>
					</div>
				</div>
			</footer>
			<!-- /Footer -->
			
        </div>
        <!-- /Main Content -->

    





