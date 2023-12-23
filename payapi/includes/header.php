<!DOCTYPE html>
<html <?php if(isset($_SESSION['lang']) && $_SESSION['lang']=='ar'){ ?>dir="rtl" lang="ar"<?php }else{ ?>lang="en"<?php } ?> >
<head>
  <meta charset="utf8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<meta title="description" content="<?php echo $lang['meta_description'] ?>">
	<meta title="keywords" content="<?php echo $lang['meta_keywords'] ?>">
	<meta title="Author" content="<?php echo $lang['author'] ?>">
    <title><?php echo $lang['meta_title'] ?></title>


      <!-- Bootstrap core CSS -->
  <link href="<?php echo SITEURL; ?>assets/vendor/bootstrap/css/bootstrap.css" rel="stylesheet">

  <!-- Custom fonts for this template -->
  <link href="<?php echo SITEURL; ?>assets/vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.2/css/all.min.css" integrity="sha512-HK5fgLBL+xu6dm/Ii3z4xhlSUyZgTT9tuc/hSrtw6uzJOvgRr2a9jyxxT1ely+B+xFAmJKVSTbpM/CuL7qxO8w==" crossorigin="anonymous" />
  <link href="<?php echo SITEURL; ?>assets/vendor/simple-line-icons/css/simple-line-icons.css" rel="stylesheet" type="text/css">

  <link href="https://fonts.googleapis.com/css2?family=Almarai&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/flexslider/2.7.0/flexslider.min.css">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.5.8/slick.min.css" rel="stylesheet" />
  <link href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.5.8/slick-theme.min.css" rel="stylesheet" />
    <!--Lightbox gallery-->
    <link rel="stylesheet" href="<?php echo SITEURL; ?>assets/css/lightbox.min.css">
  <!-- Custom styles for this template -->
  <link href="<?php echo SITEURL; ?>assets/css/landing-page.css?v=61" rel="stylesheet">
  
  <?php if(isset($_SESSION['lang']) && $_SESSION['lang']=='ar'){ ?>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-rtl/3.4.0/css/bootstrap-rtl.css?v=1" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo SITEURL; ?>assets/css/style-nrtl.css?az=11">
  <?php } ?>
  
 		<!-- Data table CSS -->
	<link href="<?php echo SITEURL; ?>admin/assets/vendors/bower_components/datatables/media/css/jquery.dataTables.min.css" rel="stylesheet" type="text/css"/>	
  
 <style>
  td.disabled.day {
   color: #e7888c!important; 
  }
  td.today.disabled {
   color: #000!important; 
  }
  td.disabled.day:nth-of-type(6) {
  color: cornflowerblue!important;
}
.above-logo {
    position: absolute;
    top: 25%;
    margin: 0 auto;
    left: 40%;
    width: 25%;
}
.above-logo img {
 width:100%;
}
.carousel-indicators {
    display: none;
}
 </style>
 <!-- Google Analytics -->
<script>
(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
})(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

ga('create', 'G-8GF8RDGQPM', 'auto');
ga('send', 'pageview');
</script>
<!-- End Google Analytics -->


</head>

<body>

  <!-- Navigation -->
  <nav class="navbar fixed-top navbar-expand-md navbar-light bg-white"> 
    <div class="container">
   <!-- <a class="navbar-brand d-lg-none" href="<?php echo SITEURL; ?>"><img src="<?php echo SITEURL; ?>assets/img/logo.png" width="168"></a>-->
    <a class="navbar-brand" href="<?php echo SITEURL; ?>"><img src="<?php echo SITEURL; ?>assets/images/logo.png" width="168"></a>
    
      <button class="<?=($_SESSION['lang']=='ar'?'mr-auto':'ml-auto');?> mr-3 navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarSupportedContent">
      <ul class="navbar-nav <?=($_SESSION['lang']=='ar'?'mr-auto':'ml-auto');?> mx-md-auto">
        <li class="nav-item active">
          <a class="nav-link" href="<?php echo SITEURL; ?>"><?php echo $lang['home'] ?></a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="<?php echo SITEURL; ?>index.php?page=galleries"><?php echo $lang['gallery'] ?></a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="<?php echo SITEURL; ?>index.php?page=reservations-check"><?php echo $lang['reservation'] ?></a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="<?php echo SITEURL; ?>index.php?page=contact-us"><?php echo $lang['ContactUs'] ?></a>
        </li>
        <li class="nav-item book_now">
          <a class="nav-link " href="<?php echo SITEURL; ?>index.php?page=categories"><?php echo $lang['book_now'] ?></a>
        </li>
        <li class="nav-item dropdown d-block d-lg-none">
          <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          <?//=$lang[$_SESSION['lang']]?>
          <?=($_SESSION['lang']=='en'?'Ar':'En')?>
          </a>
          <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
          <a class="dropdown-item" href="<?php echo SITEURL; ?>index.php?lang=ar"><?php echo $lang['arabic'] ?></a>
          <a class="dropdown-item" href="<?php echo SITEURL; ?>index.php?lang=en"><?php echo $lang['english'] ?></a>
            
          </div>
        </li>
      </ul>
    </div>
    
    <div class="d-none d-lg-block">
      <ul class="navbar-nav mr-auto">
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          <?=($_SESSION['lang']=='en'?'Ar':'En')?>
          </a>
          <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
          <a class="dropdown-item ar" id="lang_ar" href="<?php echo SITEURL; ?>index.php?lang=ar"><?php echo $lang['arabic'] ?></a>
          <a class="dropdown-item en" id="lang_en" href="<?php echo SITEURL; ?>index.php?lang=en"><?php echo $lang['english'] ?></a>
          </div>
        </li>
      </ul>
    </div>
    </div>
  </nav>
  

  <!-- Masthead -->
  <header class="masthead text-white text-center">
    <div class="container-fluid p-0">
    	<!-- <img src="<?php echo SITEURL; ?>assets/images/banner.jpg" width="100%"> -->
      <div class="row no-gutters align-items-center">
       
        <div class="col-md-12">
          <div id="demo" class="carousel slide" data-ride="carousel">

            <!-- Indicators -->
            <ul class="carousel-indicators">
            <?php 
            $banners=get_banners();
            $i = 0;
            foreach($banners as $key=>$banner){ ?>
            <li data-target="#demo" data-slide-to="<?php echo $i; ?>" <?php if($i ==0){ ?> class="active" <?php } ?>></li>
            <?php $i = $i+1;
            } ?>
            </ul>
          
            <!-- The slideshow -->
            <div class="carousel-inner">
			<?php 
            $banners=get_banners();
			    $j = 0;
            foreach($banners as $key=>$banner){
            ?>
              <div class="carousel-item  <?php if($j ==0){ ?> active <?php } ?>">
                <img src="<?php echo "uploads/images/".$banner['image']; ?>" class="img-fluid d-block mx-auto" alt="">
              </div>
              <?php 
			      $j = $j+1;
			  } ?>
            </div>
          
            <!-- Left and right controls -->
            <a class="carousel-control-prev" href="#demo" data-slide="prev">
              <span class="carousel-control-prev-icon"></span>
            </a>
            <a class="carousel-control-next" href="#demo" data-slide="next">
              <span class="carousel-control-next-icon"></span>
            </a>
            <div class="above-logo">
                <img src="<?php echo SITEURL; ?>assets/images/banner-logo.png">
            </div>
          </div>
        </div>
      </div>
    </div>
  </header>
