<?
  if($_SERVER['REQUEST_METHOD']=="POST")
  {

  	
  	 
  	 require_once('db.php');   
     $db=connect_database();
	   if($db)
	   {
		   //get the account num
  	   //database operation 2
  	   $sql2='select count(1) from user where is_admin=0'; 
  	   $query2=mysql_query($sql2);
  	   $nums=0;
  	   if(mysql_affected_rows()>0)
  	   {
  	   	 $result2=mysql_fetch_array($query2);  	   	 
  	   	 $nums=$result2[0];
  	   }
  	   $account_str.='<font color="#color"><b>Accounts</b></font>';
  	   if($nums==0)
  	   {
  	   	$account_str.='<div class="outdivborder" style="height:108px"><div class="divborder" style="height:100px">';
  	   }
  	   else
  	   {
  	   	 $height=($nums+3)*30;
  	   	 $account_str.='<div class="outdivborder" style="height:'.$height.'px"><div class="divborder" style="height:'.$height.'px">';
  	   }
  	   $account_str.='<table ><tr height="30px"></tr><tr class="fon4"><td width="100px"></td><td width="175px">UserName</td><td width="250px">Permissions</td><td width="100px">delete</td></tr>';
		   //get the account info 
		   //database operation 1
		   $sql1='select username,upload,edit,del from user where is_admin=0';
		   $query1=mysql_query($sql1);
		   if(mysql_affected_rows()>0)
		   {

   	      while($result1=mysql_fetch_array($query1))
   	      {
   	 	      $account_str.='<tr class="fon5" readonly="read-only">';
   	 	      $account_str.='<td width="100px"></td><td>'.$result1['username'].'</td><td>';
   	 	      if($result1['upload']==1)
   	 	      {
   	 	 	     $account_str.='<Input type="checkbox" checked disabled>Upload &nbsp;&nbsp;&nbsp;';
   	 	      }
   	 	      else
   	 	      {
   	 	 	     $account_str.='<Input type="checkbox" disabled>Upload &nbsp;&nbsp;&nbsp;';
   	 	      }
   	 	      if($result1['edit']==1)
   	 	      {
   	 	 	     $account_str.='<Input type="checkbox" checked disabled>Edit &nbsp;&nbsp;&nbsp;';
   	 	      }
   	 	      else
   	 	      {
   	 	 	     $account_str.='<Input type="checkbox" disabled>Edit &nbsp;&nbsp;&nbsp;';
   	 	      }
   	 	      if($result1['del']==1)
   	 	      {
   	 	 	     $account_str.='<Input type="checkbox" checked disabled>Delete &nbsp;&nbsp;&nbsp;';
   	 	      }
   	 	      else
   	 	      {
   	 	 	     $account_str.='<Input type="checkbox" disabled>Delete </td>';
   	 	      } 
   	 	      $account_str.='<td><img src="pic/delete2.png" width="15px" height="15px" onclick="del(\''.$result1['username'].'\',\''.$result1['password'].'\')"></td>';   	 	   	 	 
   	 	      $account_str.='</tr>';
   	      }
		   }
	   }
	    $account_str.='</table>';
	    //$account_str.='<table align="center"><tr height="10px"></tr><tr align="center"><td id="edit" align="center"><input type="button" value="Edit" style="background-color:#3B5998;color:#FFFFFF;font-weight:bold;" onclick="Edit()"></td></tr></table>';
	    $account_str.='</div></div>';
	    echo $account_str;
  }
?>
