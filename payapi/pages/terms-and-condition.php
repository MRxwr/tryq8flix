<section>
    <div class="container">
      <div class="row">
        <div class="col-12">
          <h2 class="shoots-Head">Terms & Condition</h2>
        </div>
        <div class="col-lg-12">
          <p>
                <?php 
               $tearm=get_page_details(9);
			   echo $tearm['description_'.$_SESSION['lang']]
               ?>
               
               </p>
        </div>
        
      </div>
    </div>
  </section>