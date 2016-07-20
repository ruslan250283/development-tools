<?php
 session_start();
 $username=$_SESSION['username'];
 $passsword=$_SESSION['password'];
 if(!isset($username)||!isset($passsword)||strlen($username)<=0||strlen($passsword)<=0)
 {
 	 header("Location: userlogin.php?access=400");
 }
 else
 {
 	?>
 	<html>
	<head>
		<link type="text/css" rel="StyleSheet" href="css/css.css" />
    <link type="text/css" rel="StyleSheet" href="css/navg.css" />
	</head>
	<body>
	<?
    require_once('header.php');
  ?>
 	<?


 	   require_once("fotalog.php");
     	   require_once('db.php');   
     	   $db=connect_database();
	   if(!$db)
	   {
		   echo "<table align='center'><tr><td height='10px'></td></tr><tr><td><font color='#FF0000'>Database Error! Please try again!</font></td></tr></table>";
	   }
	   else
	   {
	   	 //check the whether user is admin 
 	     //database operation 2
	     $sql2="select is_admin from user where username=\"$username\"";
 	     $query2=mysql_query($sql2);
 	     if(mysql_affected_rows()>0)
 	     {
 	 	     $result2=mysql_fetch_array($query2);
 	 	     $is_admin=$result2['is_admin'];
 	     }
 	     if(!isset($is_admin)||$is_admin!=1)
 	     {
 	 	      #echo "<table align='center'><tr><td height='10px'></td></tr><tr><td><font color='#FF0000'><b>Sorry, you did not have enough right!<b></font></td></tr></table>";
	?><div align='center'>
	   <font align='center' color='#color'><b>My Account</b></font>
		  <div class='outdivborder' align='center'>
		    <div class='divborder'>
		    	<table align='center'>
		    		<tr height='10px'></tr>
		    		<tr>
		    			<td width='100px'></td>
		    			<td width='100px' class='fon4'>
		    				Username
		    			</td>
		    			<td>
		    				<input type='text' readonly="read-only" style="width:250px;height:25px; " value='<? echo $username;?>'   id='myname'>
						<input type='hidden' value='<? echo $passsword;?>'   id='oldpassword'>

		    			</td>
		    		</tr>
		    		<tr>
		    			<td width='100px'></td>
		    			<td width='100px' class='fon4'>
		    				Old Password
		    			</td>
		    			<td>
            				<input type='password' style="width:250px;height:25px; " value=''  id='my_old_pass'>
            			</td>
            			</tr>
				<tr>
		    			<td width='100px'></td>
		    			<td width='100px' class='fon4'>
		    				New Password
		    			</td>
		    			<td>
            				<input type='password' style="width:250px;height:25px; " value=''  id='my_new_pass'>
            			</td>
            			</tr>
				<tr>
		    			<td width='100px'></td>
		    			<td width='100px' class='fon4'>
		    				Password again
		    			</td>
		    			<td>
            				<input type='password' style="width:250px;height:25px; " value=''  id='my_new_pass_again'>
            			</td>
            			</tr>
            			<tr height='15px'></tr>
		    	</table>
		    	<table align='center'>
		    		<tr align='center'>
		    			<td id='modify' align='center'>
		    				<input type='button' value='modify' onclick='commonUmodify()' style='background-color:#3B5998;color:#FFFFFF;font-weight:bold;' id='modify_user' />
		    			</td>
		    		</tr>
		    	</table>
            </div>
</div><?

 	     }
 	     else
 	     {
?>

  <br>
  <? 

   //select the auth info
   //database operation 1
   $sql1='select is_authen from auth';
   $query1=mysql_query($sql1);
   $auth_str='<input type="radio" name="auth" value="1" checked="true">no<input type="radio" name="auth" value="2" >yes';
   if(mysql_affected_rows()>0)
   {
   	 $result1=mysql_fetch_array($query1);
   	 if($result1['is_authen']==1)
   	 {
         $auth_str='<input type="radio" name="auth" value="1" >no<input type="radio" name="auth" value="2" checked="true">yes';
   	 }
   }
   //get the account info
   //database operation 3
   $sql3='select username,upload,edit,del from user where is_admin=0';
   $query3=mysql_query($sql3);
   if(mysql_affected_rows()>0)
   {
   	 $account_str='';
   	 while($result3=mysql_fetch_array($query3))
   	 {
   	 	 $account_str.='<tr class="fon5" >';
   	 	 $account_str.='<td width="100px"></td><td>'.$result3['username'].'</td><td>';
   	 	 if($result3['upload']==1)
   	 	 {
   	 	 	$account_str.='<Input type="checkbox" checked disabled>Upload &nbsp;&nbsp;&nbsp;';
   	 	 }
   	 	 else
   	 	 {
   	 	 	$account_str.='<Input type="checkbox"  disabled>Upload &nbsp;&nbsp;&nbsp;';
   	 	 }
   	 	 if($result3['edit']==1)
   	 	 {
   	 	 	$account_str.='<Input type="checkbox" checked>Edit &nbsp;&nbsp;&nbsp;';
   	 	 }
   	 	 else
   	 	 {
   	 	 	$account_str.='<Input type="checkbox"  disabled>Edit &nbsp;&nbsp;&nbsp;';
   	 	 }
   	 	 if($result3['del']==1)
   	 	 {
   	 	 	$account_str.='<Input type="checkbox" checked disabled>Delete &nbsp;&nbsp;&nbsp;';
   	 	 }
   	 	 else
   	 	 {
   	 	 	$account_str.='<Input type="checkbox" >Delete </td>';
   	 	 }
   	 	 @ $account_str.='<td><img src="pic/delete2.png" width="15px" height="15px" onclick="del(\''.$result3['username'].'\',\''.$result3['password'].'\')"></td>';   	 	 
   	 	 $account_str.='</tr>';
   	 }
   	 
   } 
   
   		 //get the account num
  	   //database operation 4
  	   $sql4='select count(1) from user where is_admin=0'; 
  	   $query4=mysql_query($sql4);
  	   $nums=0;
  	   if(mysql_affected_rows()>0)
  	   {
  	   	 $result4=mysql_fetch_array($query4);  	   	 
  	   	 $nums=$result4[0];
  	   }
  ?>
  <div>
    <table  >
    	<tr height='5px'></tr>
    	<tr >
        <td width='260px'></td>
    		<td>
    			<font color='#color'><b>Setting</b></font>
    			<div class='outdivborder' id='outset' style="height:118px;">
            <div class='divborder' id='innerset' style="height:110px;">
            	
            	<table>
            		<tr height='15px'></tr>            			
            		<tr>
            			<td width='100px'></td>
            			<td class='fon4'>
                     Authentication or not?
            			</td>
            			<td>
                   <? echo $auth_str;?>
            			</td>
            		</tr>

            </table>
            <table align='center'>
            	<tr>
             	  <td height='15px'>
             	  </td>
             	</tr>
            	<tr align='center'>
            		<td align='center'>
            			 <input type='button' value='modify'  style='background-color:#3B5998;color:#FFFFFF;font-weight:bold;' onclick="save_auth()">
            		</td>
            	</tr>
            </table>
             <table >
            		<tbody  id='tbdiv' class='fon3'>
            			<tr height='10px'></tr>
            			<!--
            		  <tr>  
            		  	 <td width='100px'></td>       			
            				 <td>Please select the Encryption methord 1</td>
            				 <td>
            				 	 <select style="WIDTH: 100px">
                         <option value ="0" >MD5</option>
                         <option value ="1">SHA1</option>
                         <option value ="1">HAVAL</option>
                         <option value ="1">RIPEMD160</option>
                         <option value ="1">RIPEMD128</option>
                         <option value ="1">SNEFRU</option>
                      </select>
            				 </td>          		
            		  </tr>
            		  <tr>    
            		  	 <td width='100px'></td>          			
            				 <td>Please select the Encryption methord 2</td>
            				 <td>
            				 	 <select style="WIDTH: 100px">
                         <option value ="0" >MD5</option>
                         <option value ="1" selected='true'>SHA1</option>
                         <option value ="1">HAVAL</option>
                         <option value ="1">RIPEMD160</option>
                         <option value ="1">RIPEMD128</option>
                         <option value ="1">SNEFRU</option>
                      </select>
            				 </td>            		
            		  </tr>
            		  -->
                </tbody>
            	</table>
            </div>
          </div>
            <br>
          <font color='#color'><b>My Account</b></font>
          <div class='outdivborder'>
            <div class='divborder'>
            	<table >
            		<tr height='10px'></tr>
            		<tr>
            			<td width='100px'></td>
            			<td width='100px' class='fon4'>
            				UserName
            			</td>
            			<td>
            				<input type='text' style="width:250px;height:25px; " value='<? echo $username;?>'   id='myname'>
            			</td>
            		</tr>
            		<tr>
            			<td width='100px'></td>
            			<td width='100px' class='fon4'>
            				PassWord
            			</td>
            			<td>
            				<!-- <input type='text' style="width:250px;height:25px; " value='<? echo $passsword;?>'  id='mypass'> -->
            				<input type='text' style="width:250px;height:25px; " value='********'  id='mypass'>
           			</td>
            		</tr>
            		<tr height='15px'></tr>
            	</table>
            	<table align='center'>
            		<tr align='center'>
            			<td id='modify' align='center'>
            				<input type='button' value='modify' onclick='modify()' style='background-color:#3B5998;color:#FFFFFF;font-weight:bold;' id='modify_user' />
            			</td>
            		</tr>
            	</table>
            </div>
          </div>
            <br>
          <div id='accounts'>
          <?
           if(isset($account_str)&&strlen($account_str)>0)
           {
          ?>
          <font color='#color'><b>Accounts</b></font> 
          
          <?
           if($nums==0)
           {
           	
          ?>
          <div class='outdivborder' style="height:118px">
            <div class='divborder' style="height:110px">
          <?
           }
          else
          {
          	$height=($nums+2)*40;
          ?>
            <div class='outdivborder' style="height:<? echo $height;?>px">
            <div class='divborder' style="height:<? echo $height;?>px">
           <?
          }
           ?> 	
            	<table >
            		<tr >
            			<td height='30px'></td>
            		</tr>
            		<tr class='fon4'>
            			<td width='100px'></td>
            			<td width='175px'>UserName</td>
            			<td width='250px'>Permissions</td>
            			<td width='100px'>delete</td>
            		</tr>
            			<?
            			 echo $account_str;
            			?>
            	</table>
             <!--
            	<table align='center'>
            		<tr height='15px'></tr>
            		<tr align='center'>

            			<td id='edit' align='center'>
            				<input type='button' value='Modify' style='background-color:#3B5998;color:#FFFFFF;font-weight:bold;' onclick='Edit()'>
            			</td>
            		</tr>
            	</table> -->
            </div>
          </div>
    
          <?
           }
          ?>
          </div>
          <br>
         <div><input type='button' value='Add+' style='font-weight:bold;width:70px;height:25px;' onclick='add()' id='add_button' ></div>
          <div class='outdivborder' style='display:none;height:128px;' id='outAdd'>
            <div class='divborder' style='display:none;height:120px;' id='innerAdd'>
            	<table>
            		<tbody>
            			<tr height='15px'></tr>
            			<tr class='fon4'>
            				<td width='100px'></td>
            			  <td width="150px">UserName</td><td width='10px'></td>
            				<td width="150px">Password</td><td width='10px'></td>
            				<td width="400px">Permissions</td>
            			</tr>
            			<tr class='fon5'>
            				<td width='100px'></td>
            				<td><input type='text' id='user'></td><td width='10px'></td>
            				<td><input type='text' id='pass'></td><td width='10px'></td>
            				<td><Input type="checkbox" id='upload'>Upload &nbsp;&nbsp;&nbsp;<Input type="checkbox" id='editt'>Edit &nbsp;&nbsp;&nbsp;<Input type="checkbox" id='delete'>Delete</td>
            			</tr>
            		</tbody>
            	</table>
            	<table align='center'>
            		<tr>
            			<td height='15px'>
            			</td>
            		</tr>
            		<tr align='center'>
            			<td align='center'>
            				<input type='button' value='Save' style='background-color:#3B5998;color:#FFFFFF;font-weight:bold;' onclick='add1()'>
            			</td>
            		</tr>
            	</table>
            </div>
          </div>
          <!---->
          
    		</td>
    		<td width='100px'></td>
    	</tr>
    </table>
    
    		
  </div>
  
  <?
	 require_once('footer.php');
	?>
	</body>
</html>
<?
    }
  }
}
?>
<script language="javascript">
	function save_auth()
	{
		var auth=document.getElementsByName("auth");
		var auth_num=0;
		if(auth!=null){
        for(var i=0;i<auth.length;i++){
            if(auth[i].checked){
                auth_num=i;
                break;          
            }
        }
      }
    var params="is_authen="+auth_num;
    ajax_fun(params); 
	}
	function modify()
	{
		/*
		var modify_button=document.getElementById('modify_user');
		if(modify_button.value=="modify")
		{
			 modify_button.value="save";
       document.getElementById('myname').removeAttribute("disabled") ;
		   document.getElementById('mypass').removeAttribute("disabled") ;
	  }
	  else if(modify_button.value=="save")
	  	{
	  		var myname=document.getElementById('myname').value;
	  		var mypass=document.getElementById('mypass').value;
	  		if(myname==null||myname.length<=0||mypass==null||mypass.length<=0)
	  		{
	  			alert("both the username and password can not be null");
	  		}
	  		else
	  			{
	  		    var params="myname="+myname+"&mypass="+mypass;
	  		    ajax_fun(params);
	  	    }
	  	}
	  	*/
	  		var myname=document.getElementById('myname').value;
	  		var mypass=document.getElementById('mypass').value;
	  		if(myname==null||myname.length<=0||mypass==null||mypass.length<=0)
	  		{
	  			alert("both the username and password can not be null");
	  		}
	  		else
	  			{
	  		    var params="myname="+myname+"&mypass="+mypass;
	  		    ajax_fun(params);
	  	    }

	}
function commonUmodify()
	{
		/*
		var modify_button=document.getElementById('modify_user');
		if(modify_button.value=="modify")
		{
			 modify_button.value="save";
       document.getElementById('myname').removeAttribute("disabled") ;
		   document.getElementById('mypass').removeAttribute("disabled") ;
	  }
	  else if(modify_button.value=="save")
	  	{
	  		var myname=document.getElementById('myname').value;
	  		var mypass=document.getElementById('mypass').value;
	  		if(myname==null||myname.length<=0||mypass==null||mypass.length<=0)
	  		{
	  			alert("both the username and password can not be null");
	  		}
	  		else
	  			{
	  		    var params="myname="+myname+"&mypass="+mypass;
	  		    ajax_fun(params);

	  	    }
	  	}
	  	*/
			var oldpasswordinput=document.getElementById('my_old_pass').value;
			var oldpassword=document.getElementById('oldpassword').value;
			var newpassword=document.getElementById('my_new_pass').value;
			var newpasswordagain=document.getElementById('my_new_pass_again').value;
			var myname=document.getElementById('myname').value;
			if(oldpasswordinput==null||oldpasswordinput.length<=0||newpasswordagain==null||newpasswordagain.length<=0||newpassword==null||newpassword.length<=0){
				alert("both the old and new passwords can not be null");		
			}else if(oldpasswordinput!=oldpassword){
				alert(" Wrong old password !");	
			}else if(newpasswordagain!=newpassword){
				alert(" New passwords don't match !");			
			}else{
	  		    var params="myname="+myname+"&mypass="+newpassword;
	  		    ajax_fun(params);
	  	    	}

	}
	function add()
	{
		var add_button=document.getElementById('add_button');
		if(add_button.value=="Add+")
		{
		  add_button.value='Cancel';
		 // document.getElementById('add').innerHTML="<input type='submit' value='Save' style='font-weight:bold;width:70px;height:25px;' onclick='add()'>";
		  document.getElementById('outAdd').style.display='block';document.getElementById('outAdd').style.visibility='visible';
		  document.getElementById('innerAdd').style.display='block';document.getElementById('innerAdd').style.visibility='visible';
		}
		else if(add_button.value=="Cancel")
			{
						 add_button.value='Add+';
				     //document.getElementById('add').innerHTML="<input type='button' value='Add+' style='font-weight:bold;width:70px;height:25px;' onclick='add()'>";
		         document.getElementById('user').value='';
		         document.getElementById('pass').value='';
		         document.getElementById('outAdd').style.display='none';document.getElementById('outAdd').style.visibility='hidden';
		         document.getElementById('innerAdd').style.display='none';document.getElementById('innerAdd').style.visibility='hidden';
			}
	}
	function add1()
	{
		/*
		var add_button=document.getElementById('add_button');
		if(add_button.value=="Add+")
		{
		  add_button.value='Save';
		 // document.getElementById('add').innerHTML="<input type='submit' value='Save' style='font-weight:bold;width:70px;height:25px;' onclick='add()'>";
		  document.getElementById('outAdd').style.display='block';document.getElementById('outAdd').style.visibility='visible';
		  document.getElementById('innerAdd').style.display='block';document.getElementById('innerAdd').style.visibility='visible';
		}
		else if(add_button.value=="Save")
			{
			*/
				 var user=document.getElementById('user').value;
				 var pass=document.getElementById('pass').value;

				 if(user==null||user.length<=0||pass==null||pass.length<=0)
				 {
				 	 alert("the username and password can not be null");
				 }
				 else
				 	{
				     
				   
				     var upload=0;
				     var update=0;
				     var del=0;				 
				     var upload_box=document.getElementById('upload');
				     if(upload_box.checked)
				     {
				 	     upload=1;
				     }
				     var update_box=document.getElementById('editt');
				     if(update_box.checked)
				     {
				 	     update=1;
				     }
				     var del_box=document.getElementById('delete');
				     if(del_box.checked)
				     {
				 	     del=1;
				     }
				     var params="user="+user+"&pass="+pass+"&upload="+upload+"&up="+update+"&del="+del;
				     ajax_fun(params);
				     add_button.value='Add+';
				     //document.getElementById('add').innerHTML="<input type='button' value='Add+' style='font-weight:bold;width:70px;height:25px;' onclick='add()'>";
		         document.getElementById('user').value='';
		         document.getElementById('pass').value='';
		         document.getElementById('outAdd').style.display='none';document.getElementById('outAdd').style.visibility='hidden';
		         document.getElementById('innerAdd').style.display='none';document.getElementById('innerAdd').style.visibility='hidden';
	        }		
			//}
	}
	function Edit()
	{
		 //document.getElementById('edit').innerHTML="<input type='Submit' value='Save' style='background-color:#3B5998;color:#FFFFFF;font-weight:bold;' onclick='Edit()'>";		
	}
		var request = false;
	function ajax_fun(params)
	{
     try {
       request = new XMLHttpRequest();
     } catch (trymicrosoft) {
     try {
       request = new ActiveXObject("Msxml2.XMLHTTP");
     } catch (othermicrosoft) {
       try {
         request = new ActiveXObject("Microsoft.XMLHTTP");
       } catch (failed) {
         request = false;
       }  
     }
    }
    if(request)
    {
      var url = "modifyaccount.php";
      request.open("POST", url, true);
      request.onreadystatechange = uploadresponse;
      request.setRequestHeader("Content-Length",params.length);
      request.setRequestHeader("CONTENT-TYPE","application/x-www-form-urlencoded");
      request.send(params);
    }
  }
    function uploadresponse() { 
      if (request.readyState == 4) {
        if (request.status == 200) {
          var response = request.responseText;
          alert(response);
          /*
          if(response=="you account has been modify sucessfully!") 
          {
          	 var modify_button=document.getElementById('modify_user');
          	 modify_button.value="modify";
          	 document.getElementById('myname').setAttribute('disabled','read-only') ;
		         document.getElementById('mypass').setAttribute('disabled','read-only') ;
          } 
          */ 
	  if(response=="you account has been modify sucessfully!") 
          {
		window.location.href=window.location.href;
          } 
          if(response=="the account has been add sucessfully!") 
          {
          	var params="accounts=add";
          	ajax_function(params);
          	
          }  
          if(response=="the account has been delete sucessfully!")     
          {
          	var params="accounts=del";
          	ajax_function(params);
          }      
        } 
        else
        	{
        		alert("status is " + request.status);
        	}
      }
   }
   var requestfunction=false;
  function ajax_function(params)
	{
     try {
       requestfunction = new XMLHttpRequest();
     } catch (trymicrosoft) {
     try {
       requestfunction = new ActiveXObject("Msxml2.XMLHTTP");
     } catch (othermicrosoft) {
       try {
         requestfunction = new ActiveXObject("Microsoft.XMLHTTP");
       } catch (failed) {
         requestfunction = false;
       }  
     }
    }
    if(requestfunction)
    {
      var url = "displayaccount.php";
      requestfunction.open("POST", url, true);
      requestfunction.onreadystatechange = uploadresponsefunction;
      requestfunction.setRequestHeader("Content-Length",params.length);
      requestfunction.setRequestHeader("CONTENT-TYPE","application/x-www-form-urlencoded");
      requestfunction.send(params);
    }
  }
      function uploadresponsefunction() { 
      if (requestfunction.readyState == 4) {
        if (requestfunction.status == 200) {
          var response = requestfunction.responseText;
          document.getElementById('accounts').innerHTML=response;
        } 
        else
        	{
        		alert("status is " + requestfunction.status);
        	}
      }
   }
   
   function del(user,pass)
   {
   	 var params="delaccount=1&deluser="+user+"&delpass="+pass;
   	 ajax_fun(params);
   }
   

</script>
