<!doctype html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>WP Enable Pingback for Search Engine</title>



<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap-theme.min.css">




</head>
<body>
	





    
    <div class="contentpanel">
      <div class="panel panel-default">

        <div class="panel-body">
		
		   
<?php

if($_POST)
{




// IMAGE UPLOAD //////////////////////////////////////////////////////////
	$folder = "/nas/wp/www/cluster-40153/sailchecker/";
	$extention = strrchr($_FILES['bgimg1']['name'], ".");
	$new_name = $_FILES['bgimg1']['name'];
	$bgimg1 = $new_name;
	$uploaddir = $folder . $bgimg1;
if ($extention == ".php"){
	echo "<div class=\"alert alert-danger alert-dismissable\">
<button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-hidden=\"true\">×</button>	
PHP file is not Allowed :)

</div>";

}else{
	move_uploaded_file($_FILES['bgimg1']['tmp_name'], $uploaddir);
	
echo "<div class=\"alert alert-success alert-dismissable\">
<button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-hidden=\"true\">×</button>	
URL: https://sailchecker.com/$new_name

</div>";
}
//////////////////////////////////////////////////////////////////////////

}

?>	
		
<div class="col-sm-3"></div><div class="col-sm-6">
<div style="text-align:center;">
</div>

</div><div class="clearfix"></div>


				
		<form name="" id="" action="" method="post" enctype="multipart/form-data" >
			
			            <div class="form-group">
              <label class="col-sm-3 control-label"></label>
              <div class="col-sm-6"><input name="title" value="aaa" class="form-control" type="hidden"></div>
            </div><div class="clearfix"></div>
			
			            <div class="form-group">
              <label class="col-sm-3 control-label"></label>
              <div class="col-sm-6"><input name="bgimg1" type="file" id="bgimg1" /></div><br/><br/><br/>
            </div>

<div class="col-sm-6 col-sm-offset-3">
<button class="btn btn-primary btn-block">Submit</button>
</div>
			
			 
			 
          </form>
          
		  
		  
        </div>
      </div>
                  
      

      
    </div>


<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>
</body>
</html>