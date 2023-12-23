		<?php 
		$page = "";
		if(isset($_GET['page']) && !empty($_GET['page']))
		{
			$page = $_GET['page'];	
		} 
		?>
    
    
    <div class="fixed-sidebar-left">
		<ul class="nav navbar-nav side-nav nicescroll-bar">
			<li class="navigation-header">
				<span>Main</span> 
				<i class="zmdi zmdi-more"></i>
			</li>
			<li>
				<a href="<?php echo SITEURL; ?>admin" data-toggle="collapse" data-target="#dashboard_dr">
					<div class="pull-left"><i class="zmdi zmdi-local-store mr-20"></i><span class="right-nav-text">Dashboard</span></div><div class="pull-right"></div><div class="clearfix"></div></a>
				</li>
				<li><hr class="light-grey-hr mb-10"/></li>
				<li class="navigation-header">
					<span>component</span> 
					<i class="zmdi zmdi-more"></i>
				</li>
			
				<?php $adss = array('appkey','add_appkey','apihistories');
				  if (!empty(array_intersect($adss, $user_permission))) { ?>
				<li>
					<a href="javascript:void(0);" data-toggle="collapse" data-target="#ads"><div class="pull-left"><i class="zmdi zmdi-apps mr-20"></i><span class="right-nav-text">Api Key</span></div><div class="pull-right"><i class="zmdi zmdi-caret-down"></i></div><div class="clearfix"></div></a>
					<ul id="ads" class="collapse collapse-level-1 two-col-list <?php if(in_array($page,$adss)){ ?> in <?php } ?>">
					<?php if(in_array('appkey',$user_permission)){ ?>
						<li>
							<a href="<?php echo SITEURL; ?>admin/index.php?page=appkey" <?php if($page == 'appkey'){ ?> class="active" <?php } ?>>Api key List</a>
						</li>
					<?php } ?>
					<?php if(in_array('add_appkey',$user_permission)){ ?>
						<li>
							<a href="<?php echo SITEURL; ?>admin/index.php?page=add_appkey" <?php if($page == 'add_appkey'){ ?> class="active" <?php } ?>>Add New Api key</a>
						</li>
					<?php } ?>
					<?php if(in_array('apihistories',$user_permission)){ ?>
						<li>
							<a href="<?php echo SITEURL; ?>admin/index.php?page=apihistories" <?php if($page == 'apihistories'){ ?> class="active" <?php } ?>>Api  Histories</a>
						</li>
					<?php } ?>
						<?php if(in_array('apihistories',$user_permission)){ ?>
						<li>
							<a href="<?php echo SITEURL; ?>admin/index.php?page=apihistories2" <?php if($page == 'apihistories2'){ ?> class="active" <?php } ?>>Api  Histories2</a>
						</li>
					<?php } ?>
					</ul>
				</li>
				<?php } ?>

			
				<?php $users = array('users','add_user');
				  if (!empty(array_intersect($users, $user_permission))) { ?>
				<li>
					<a href="javascript:void(0);" data-toggle="collapse" data-target="#user_nav"><div class="pull-left"><i class="zmdi zmdi-accounts-list mr-20"></i><span class="right-nav-text">Users</span></div><div class="pull-right"><i class="zmdi zmdi-caret-down"></i></div><div class="clearfix"></div></a>
					<ul id="user_nav" class="collapse collapse-level-1 two-col-list <?php if($page == 'users'){ ?> in <?php } ?>">
					<?php if(in_array('users',$user_permission)){ ?>
						<li>
							<a href="<?php echo SITEURL; ?>admin/index.php?page=users" <?php if($page == 'users'){ ?> class="active" <?php } ?>>User List</a>
						</li>
					<?php } ?>
					<?php if(in_array('add_user',$user_permission)){ ?>
						<li>
							<a href="<?php echo SITEURL; ?>admin/index.php?page=add_user">Add New</a>
						</li>
					<?php } ?>
					</ul>
				</li>
				<?php } ?>
				
				
				<?php if(in_array('settings',$user_permission)){ ?>
				<li>
					<a href="javascript:void(0);" data-toggle="collapse" data-target="#pages_dr"><div class="pull-left"><i class="zmdi zmdi-settings mr-20"></i><span class="right-nav-text">Settings</span></div><div class="pull-right"><i class="zmdi zmdi-caret-down"></i></div><div class="clearfix"></div></a>
					<ul id="pages_dr" class="collapse collapse-level-1 two-col-list <?php if($page == 'settings'){ ?> in <?php } ?>">
					<?php if(in_array('settings',$user_permission)){ ?>
						<li>
							<a href="<?php echo SITEURL; ?>admin/index.php?page=settings" <?php if($page == 'settings'){ ?> class="active" <?php } ?>>Site Settings</a>
						</li>
						<?php } ?>
					</ul>
				</li>
				<?php } ?>

			</ul>
		</div>