<!-- Title -->
<div class="row heading-bg">
<div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
<h5 class="txt-dark"><?php echo $lang['add_user'] ?></h5>

</div>

</div>
<!-- /Title -->

<?php 
if(isset($_SESSION['add']))
{
echo $_SESSION['add'];
unset($_SESSION['add']);
}
?>
<div class="row">
<div class="col-sm-12">
<div class="panel panel-default card-view">
<div class="panel-wrapper collapse in">
<div class="panel-body">
<div class="form-wrap">
<form method="post" action="">
<div class="form-group">
<div class="col-sm-8">
	<div class="form-group">
			<label class="control-label mb-10 text-left"><?php echo $lang['full_name'] ?></label>
			<input class="form-control" type="text" name="full_name" placeholder="Your Full Name." required="true">
	</div>
</div>
<div class="col-sm-8">
	<div class="form-group">
			<label class="control-label mb-10 text-left"><?php echo $lang['email'] ?></label>
			<input class="form-control" type="email" name="email" placeholder="Your Email." required="true">
	</div>
</div>

<div class="col-sm-8">
	<div class="form-group">
			<label class="control-label mb-10 text-left"><?php echo $lang['username'] ?></label>
			<input class="form-control" type="text" name="username" placeholder="Your Username" required="true">
	</div>
</div>

<div class="col-sm-8">
	<div class="form-group">
			<label class="control-label mb-10 text-left"><?php echo $lang['password'] ?></label>
			<input class="form-control" type="password" name="password" placeholder="Youre Secure Password" required="true">
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
				<input id="per_<?=$nav?>" type="checkbox" name="permission[]" value="<?=$nav?>"> <?php echo $lang[$nav] ?> 
			</lebel>
		<?php }
		}
		?>
	</div>
</div>

<div class="col-sm-8">
	<div class="form-group">
			<label class="control-label mb-10 text-left"><?php echo $lang['is_active'] ?></label>
			<input type="radio" name="is_active" value="Yes"> <?php echo $lang['yes'] ?> 
			<input type="radio" name="is_active" value="No"> <?php echo $lang['no'] ?>
	</div>
</div>
<div class="col-sm-8">
<div class="form-group">
<span class="input-label">
<input class="btn-primary btn-sm" type="submit" name="submit" value="<?php echo $lang['add_user'] ?>">
</span>
</div>
</div>
</div>
</form>
</div>
</div>
</div>
<?php 
if(isset($_POST['submit']))
{
$full_name = $obj->sanitize($conn,$_POST['full_name']);
$email = $obj->sanitize($conn,$_POST['email']);
$username = $obj->sanitize($conn,$_POST['username']);
$password = md5($obj->sanitize($conn,$_POST['password']));
$permission = implode(',',$_POST['permission']);
$is_active = $_POST['is_active'];
$created_at = date('Y-m-d H:i:s');

$data = "
full_name='$full_name',
email='$email',
username='$username',
password='$password',
permission='$permission',
is_active='$is_active',
created_at='$created_at'
";
$tbl_name='tbl_users';

$query = $obj->insert_data($tbl_name,$data);
$res = $obj->execute_query($conn,$query);

if($res==true)
{
$_SESSION['add'] = "<div class='success'>".$lang['add_success']."</div>";
header('location:'.SITEURL.'admin/index.php?page=users');
}
else
{
$_SESSION['add'] = "<div class='error'>".$lang['add_fail']."</div>";
header('location:'.SITEURL.'admin/index.php?page=add_user');
}
}
?>
</div>
</div>
</div>
<!-- /Row -->