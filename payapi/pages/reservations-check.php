<section>
    <div class="container">
      <div class="row">
          <div class="col-12">
          
           <h2 class="shoots-Head heading_right"><?php echo $lang['reservation_number'] ?></h2>
          </div>
          <div class="col-md-10 col-sm-10">
              <div class="row">
                <div for="" class="col-sm-5 col-md-4">
                <div class="form-group">
                  <input type="text" class="form-control form-control-lg" id="bookingid" value="">
                </div>
                </div>
                <div class="col-sm-6 col-md-5">
                <div class="form-group">
                  <button class="btn btn-lg btn-outline-primary btn-block" style="padding: .75rem;" id="book-btn"><?php echo $lang['submit'] ?></button>
                </div>
                </div>
              </div>
            <div class="row mt-5">
              <div class="col-12">
                <ul class="list-unstyled h5">
                  <li class="theme-color"><?php echo $lang['reservation_number2'] ?></li>
                </ul>
                <p>if you don’t have a reservation please click here </p>            
                <a class="book-now-link " href="<?php echo SITEURL; ?>index.php?page=categories"><?php echo $lang['book_now'] ?></a>
              </div>
            </div>
          </div>
        </div>
      <!-- Row -->
				<div class="row">
					<div class="col-sm-12" > 
                 <div id="bars1" style="display:none">
                    <span></span>
                    <span></span>
                    <span></span>
                    <span></span>
                    <span></span>
                </div>
							<div  id="bookingDataDiv"></div>
					</div>
				</div>
				<!-- /Row -->
    </div>
  </section>
