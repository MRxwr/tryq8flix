
	<?php 
		if(isset($_SESSION['edit']))
		{
			echo $_SESSION['edit'];
			unset($_SESSION['edit']);
		}

		if (isset($_GET['id']) && !empty($_GET['id'])) {
			$id = $_GET['id'];

			$tbl_name = 'tbl_users';
			$where = "id='$id'";

			$query = $obj->select_data($tbl_name,$where);
			$res = $obj->execute_query($conn,$query);
			if($res==true)
			{
				$count_rows = $obj->num_rows($res);
				if($count_rows==1)
				{
					$row = $obj->fetch_data($res);
					$full_name = $row['full_name'];
					$email = $row['email'];
					$username = $row['username'];
					$password = $row['password'];
					$permission = explode(',',$row['permission']);

					$is_active = $row['is_active'];
					//var_dump($permission);
				}
			}
		}
		else
		{
			header('location:'.SITEURL.'admin/index.php?page=users');
		}
	?>

	<!-- Title -->
    <div class="row heading-bg">
        <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
          <h5 class="txt-dark"><?php echo $lang['edit_user'] ?></h5>

        </div>
    </div>
    <!-- /Title -->
	<!-- Row -->
    <div class="row">
        <div class="col-sm-12">
            <div class="panel panel-default card-view">
                <div class="panel-wrapper collapse in">
                    <div class="panel-body">
                        <div class="form-wrap">
							<form method="post" action="">

								<div class="col-sm-8">
									<div class="form-group">
											<span class="input-label"><?php echo $lang['full_name'] ?></span>
											<input class="form-control" type="text" name="full_name" value="<?php echo $full_name; ?>" required="true">
									</div>
								</div>

								<div class="col-sm-8">
									<div class="form-group">
											<span class="input-label"><?php echo $lang['email'] ?></span>
											<input class="form-control" type="email" name="email" value="<?php echo $email; ?>" required="true">
									</div>
								</div>

								<div class="col-sm-8">
									<div class="form-group">
											<span class="input-label"><?php echo $lang['username'] ?></span>
											<input class="form-control" type="text" name="username" value="<?php echo $username; ?>" required="true">
									</div>
								</div>

								<div class="col-sm-8">
									<div class="form-group">
											<span class="input-label"><?php echo $lang['password'] ?></span>
											<input class="form-control" type="password" name="password" value="">
									</div>
								</div>

								<div class="col-sm-8">
									<div class="form-group">
									   <lebel class="col-sm-12"><?php echo $lang['permission'] ?></lebel>
										<?php
										//print_r($admin_nav);
										if(!empty($admin_nav)){
											foreach($admin_nav as $key=>$nav){ ?>
											<lebel for="per_<?=$nav?>" class="col-sm-6" >
												<input id="per_<?=$nav?>" <?php if(in_array($nav,$permission)){echo "checked='checked'";} ?> type="checkbox" name="permission[]" value="<?=$nav?>"> <?php echo $lang[$nav] ?> 
											</lebel>
										<?php }
										}
										?>
									</div>
								</div>


								<div class="col-sm-8">
									<div class="form-group">
											<span class="input-label"><?php echo $lang['is_active'] ?></span>
											<input <?php if($is_active=='Yes'){echo "checked='checked'";} ?> type="radio" name="is_active" value="Yes"> <?php echo $lang['yes'] ?> 
											<input <?php if($is_active=='No'){echo "checked='checked'";} ?> type="radio" name="is_active" value="No"> <?php echo $lang['no'] ?>
									</div>
								</div>

								<div class="col-sm-8">
									<div class="form-group">
											<span class="input-label">
												<input type="hidden" name="id" value="<?php echo $id; ?>">
												<input class="btn-primary btn-sm" type="submit" name="submit" value="<?php echo $lang['edit_user'] ?>">
											</span>
										</div>
								</div>
							</form>
							</div>
					</div>
				</div>
            </div>
		</div>
	</div>
					<!-- /Row -->	
	<?php 
		if(isset($_POST['submit']))
		{
			$id = $_POST['id'];
			$full_name = $obj->sanitize($conn,$_POST['full_name']);
			$email = $obj->sanitize($conn,$_POST['email']);
			$username = $obj->sanitize($conn,$_POST['username']);
			$password = md5($obj->sanitize($conn,$_POST['password']));
			$permission = implode(',',$_POST['permission']);
			$is_active = $_POST['is_active'];
			$created_at = date('Y-m-d H:i:s');
			//var_dump($permission);
			//exit;
			$data = "
				full_name='$full_name',
				email='$email',
				username='$username',
				permission='$permission',
				is_active='$is_active'
			";
			$tbl_name='tbl_users';
			$where = "id='$id'";
			$query = $obj->update_data($tbl_name,$data,$where);
			$res = $obj->execute_query($conn,$query);

			if($res==true)
			{
				$_SESSION['edit'] = "<div class='success'>".$lang['edit_success']."</div>";
				header('location:'.SITEURL.'admin/index.php?page=users');
			}
			else
			{
				$_SESSION['edit'] = "<div class='error'>".$lang['edit_fail']."</div>";
				header('location:'.SITEURL.'admin/index.php?page=edit_user&id='.$id);
			}
		}
	?>
