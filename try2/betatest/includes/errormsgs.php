<?php

if ( !isset ($_GET["error"]) )
  {
	  $errormsg = "";
  }
  elseif ( $_GET["error"] == "register" )
  {
	  $errormsg = "You can't register because you are already logged in.";
  }
  elseif ( $_GET["error"] == "deletecat" )
  {
	  $errormsg = "Category has been deleted.";
  }
  elseif ( $_GET["error"] == "deletepost" )
  {
	  $errormsg = "Post has been deleted.";
  }
  elseif ( $_GET["error"] == "editcategory" )
  {
	  $errormsg = "You are not allowed to edit a category.";
  }
  elseif ( $_GET["error"] == "user" )
  {
	  $errormsg = "You have to login to see your profile.";
  }
  elseif ( $_GET["error"] == "login" )
  {
	  $errormsg = "You are already logged in.";
  }
  elseif ( $_GET["error"] == "post" )
  {
	  $errormsg = "You are not allowed to add new posts.";
  }
  elseif ( $_GET["error"] == "category" )
  {
	  $errormsg = "You are not allowed to add new categories.";
  }
  elseif ( $_GET["error"] == "logout" )
  {
	  $errormsg = "You have been logged out Successfully.";
  }
  elseif ( $_GET["error"] == "addcategory" )
  {
	  $errormsg = "New category has been added.";
  }
  elseif ( $_GET["error"] == "addpost" )
  {
	  $errormsg = "New post has been added.";
  }
  elseif ( $_GET["error"] == "editprofile" )
  {
	  $errormsg = "You have to be logged in to edit your profile.";
  }
  else
  {
	$errormsg = "";
  }
  

?>