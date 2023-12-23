<?php 
$data=array();
   
	if(isset($_POST['endpoint']) && !empty($_POST['endpoint']))
	{
	 	$page = $_POST['endpoint'];
		  if(isset($_POST['apikey'])){
		      $apikey = $_POST['apikey'];
		      $apidata=check_get_apikey($apikey);
    		      if(!empty($apidata)){
					  if($apidata['provider']=='myfatoorah'){
						@include('endpoint/myfatoorah/'.$page.'.php');
					  }else if($apidata['provider']=='upayment'){
						//@include('endpoint/upay/'.$page.'.php');
						@include('endpoint/upayments/'.$page.'.php');
					  }else if($apidata['provider']=='bookeey'){
						@include('endpoint/bookeey/'.$page.'.php');
					  }else{
						$data['status']=200;
						$data['type']='error';
						$data['msg']='Api provider not exist!!';
						$data['data']=array();
						echo json_encode($data);
						addApiHistories($_POST,$data);
					  }
    		          
    		      }else{
    		        $data['status']=200;
            		$data['type']='error';
            		$data['msg']='Api key is not valied!!';
            		$data['data']=array();
            	    echo json_encode($data);
					addApiHistories($_POST,$data);
    		      }
            }else{
                $data['status']=200;
        		$data['type']='error';
        		$data['msg']='Api key is not exist!!';
        		$data['data']=array();
        	   echo json_encode($data);
			   addApiHistories($_POST,$data);
            }
	
	}else{
		$data['status']=200;
		$data['type']='error';
		$data['msg']='Api Endpoint is empty';
		$data['data']=array();
    	echo json_encode($data);
		addApiHistories($_POST,$data);
	}
	@addApiHistories($_POST,$data);
?>