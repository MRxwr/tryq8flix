<section>
    <div class="container">
      <div class="row">
        <div class="col-12">
          <h2 class="shoots-Head heading_right"><?php echo $lang['contact_us'] ?></h2>
        </div>
        <div class="col-lg-6">
          <ul class="list-unstyled contact-details">
            <li class="mb-3"><a href="#" class="theme-color h5"><i class="fas fa-map-marker-alt mr-2"></i> Kuwait City</a></li>
            <li class="mb-3"><a href="mailto:info@hbqphoto.com" class="theme-color h5"><i class="far fa-envelope mr-1"></i>info@hbqphoto.com</a></li>
            <li class="mb-3"><a href="https://api.whatsapp.com/send?phone=96566998258&text=Hello" class="theme-color h5"><i class="fab fa-whatsapp"></i><?=($_SESSION['lang']== 'en'?'+':'')?> 965-66998258<?=($_SESSION['lang']== 'ar'?'+':'')?> </a></li>
          </ul> 
        </div>
        <div class="col-lg-6 col-xl-5">
          <form class="contact-form" id="contactForm">
            <div class="form-group row">
              <div class="col-md-6 mb-3"><input type="text" class="form-control form-control-lg" placeholder="<?php echo $lang['placeholder_name'] ?>" required name="name" id="name"></div>
              <div class="col-md-6 mb-3"><input type="email" name="email" id="email" class="form-control form-control-lg" placeholder="<?php echo $lang['placeholder_email'] ?>" required></div>
            </div>
            <div class="form-group row">
              <div class="col-md-6 mb-3"><input type="text" name="phone" id="phone" class="form-control form-control-lg" placeholder="<?php echo $lang['placeholder_phone'] ?>"required></div>
              <div class="col-md-6 mb-3"><input type="text" name="subject" id="subject" class="form-control form-control-lg"  placeholder="<?php echo $lang['placeholder_subject'] ?>" required></div>
            </div>
            <div class="form-group row">
              <div class="col-md-12"><textarea class="form-control form-control-lg" id="message" name="message" rows="3" placeholder="<?php echo $lang['placeholder_message'] ?>"></textarea></div>
            </div>
            <div><button type="submit" class="btn btn-lg btn-outline-primary btn-block btn-rounded"><?php echo $lang['submit'] ?></button></div>
            <div id="bars1" style="display:none">
                    <span></span>
                    <span></span>
                    <span></span>
                    <span></span>
                    <span></span>
             </div>
            <div id="msgSubmit" class="alert alert-success text-center mt-4 d-none"><?php echo $lang['contact_alert'] ?></div>
          </form>
        </div>
      </div>
    </div>
  </section>
