<?php
session_start();
require_once('config.php');
require_once('fotalog.php');
$sn=$_POST["sn"];
if(isset($sn)&&strlen($sn)>0)
{
	$_SESSION["sn"]=$sn;
}
else
{
	$sn=$_SESSION['sn'];
}
info("##phone($sn) start login...");
//info("test");
$db=mysql_connect($host,$user,$password);
if($db)
{
    $tb=mysql_select_db($databasename);
  	if($tb)
  	{
		//get the auth info
		//database operation 1
		info("phone($sn) connect databases success!");
		$auth=1;
		$sql1='select is_authen from auth';
		$query1=mysql_query($sql1);
		if(mysql_affected_rows()>0)
		{
			$result1=mysql_fetch_array($query1);
			$auth=$result1['is_authen'];
		}
		if($_SERVER['SERVER_PORT']==443||$auth==0)
		{
			$session_id=session_id();
			// info("sn number is ".$sn);
			if((isset($sn)&&strlen(trim($sn))>0)||($auth==0))
			{
				$query=mysql_query("select sn from sn where sn=\"".$sn."\"");
				if(mysql_affected_rows()>=1||$auth==0)
				{
					// session_register("istest_device");
					$_SESSION["istest_device"]=0;
					$imei=$_POST['imei'];
					// info("imei = ".$imei);
					if(isset($imei)&&strlen($imei)>0)
					{
						$sql2='select istest_device from device where imei="'.$imei.'" and istest_device=1';
						// info($sql2);
						$query2=mysql_query($sql2);
						if(mysql_affected_rows()>0)
						{
							$_SESSION["istest_device"]=1;
						}
					}
					info("phone($sn)'s imei=$imei, is test device ". $_SESSION['istest_device']);
					// session_register("sn");
					// session_register("rand");
					// session_register("token");

					$_SESSION["sn"]=$sn;		
					$rand=randnumber();
					$_SESSION["rand"]=$rand;
					//$token=mhash(MHASH_SHA1,mhash(MHASH_MD5,$sn));
					$token=md5($sn.$rand);
					$_SESSION["token"]=$token;
					echo "{\"status\":".$sucess_code.",\"server_version\":".$server_version.",\"rand\":".$rand.",\"sessionId\":\"".$session_id."\"}";  			 			
					info("phone($sn) login ok!");
				}
				else
				{
					error($sn_error_json." the sn number is not exist in the database");
					echo $sn_error_json; 
				}
			}
			else
			{
				error($auth_fail_json." the sn number is null");
				echo $auth_fail_json;
			}
		}
		else
		{
			header("Location:".$basesslurl."login.php");
		}
	}
	else
	{
		error($error_json." there is a error when open the table");
		echo $error_json;
	}
}
else
{
	error($error_json." there is a error when open the database");
	echo $error_json;
}



function randnumber()
{
	srand(time());
	return rand();
}
?>
