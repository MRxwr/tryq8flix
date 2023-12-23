<?php 
$data=array();
	if(isset($_POST['endpoint']) && !empty($_POST['endpoint']))
	{
		$page = $_POST['endpoint'];
		  if(isset($_POST['apikey'])){
		      $apikey = $_POST['apikey'];
		      $apidata=check_get_apikey($apikey);
    		      if(!empty($apidata)){
    		          //var_dump($apidata);
    		          	@include('endpoint/'.$page.'.php');
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
		$data['msg']='Api Endpoint is not exist!!';
		$data['data']=array();
    	echo json_encode($data);
		addApiHistories($_POST,$data);
	}
	@addApiHistories($_POST,$data);
?>