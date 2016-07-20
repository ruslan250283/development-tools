<?php
session_start();
require_once('config.php');  
require_once('fotalog.php');
$token=urldecode($_POST['token']);
//for test
// $token=$_SESSION['token'];
info("##start downloading...");
if(!isset($token)||strlen($token)<=0) {
	header('HTTP/1.1 401 token lost');  
} else  {
	$vercode=IsTokenInvalid($token);
	$vercode = $token_valid_code;
	if($vercode==$illegal_code) {
		header('HTTP/1.1 401 illegal access');
	} else if($vercode==$token_invalid_code) {
		header('HTTP/1.1 401 token invalid');  
		echo "123";
	} else {
		info("token is valid!");
		$delta_id=urldecode($_POST['deltaId']);
		if(!isset($delta_id)||strlen($delta_id)<=0) {
			$delta_id=$_SESSION['deltaId'];
		} 
		//for test 
		//$delta_id=2;
		if(!isset($delta_id)||$delta_id<=0) {
			header('HTTP/1.1 401 illegal access');  
		} else {
			$tb=connect_database();
			info("connect database...");
			if(!isset($tb)) {
				header('HTTP/1.1 404 Delta Not Found');  
			} else {
				//取出差分包路径，文件大小，压缩率
				//database operation 1
				$sql1='select delta_path, delta_size, delta_compress, download_num, sucess_num, sucess_ratio from delta where delta_id='.$delta_id;
				$query1=mysql_query($sql1);
				if(mysql_affected_rows()<1) {
					header('HTTP/1.1 404 Delta Not Found');
					mysql_close($tb);
				} else { 
					//取得差分包的相关信息
					$result1=mysql_fetch_array($query1);
					$filename=$result1['delta_path'];
					$size=$result1['delta_size'];
					$compress=$result1['delta_compress'];
					$download_num = $result1['download_num'];
					$success_num = $result1['sucess_num'];
					$success_ratio = $result1['sucess_ratio'];
					//echo $filename;
					$download_num++;
					$sql2 = "UPDATE delta ". "SET download_num = $download_num ". "WHERE delta_id = $delta_id";
					info($sql2);
					$retval = mysql_query($sql2);
					if(!$retval ) {
						header("Could not update data: " . mysql_error());
						info("Could not update data: " . mysql_error());
					}
					//下载差分包
					if(isset($filename)&&strlen($filename)>0) {                 	   
						if(!file_exists($filename)) {
							header('HTTP/1.1 404 File Not Found');
						} else {
							// echo $filename;
							$file=fopen($filename,"r");
							$filesize=filesize($filename);
							info("size is ".$filesize);
							//用于断点序传
							$start=$_POST['HTTP_RANGE'];
							//              if (isset($_SERVER['HTTP_RANGE']) && ($_SERVER['HTTP_RANGE'] != "") && preg_match("/^bytes=([0-9]+)-$/i", //$_SERVER['HTTP_RANGE'], $match) && ($match[1] < $size)) {
							//                 $start = $match[1];
							//             } else {
							//                $start = 0;
							//               }
							info("start is ".$start);
							//echo $start;
							//echo "123";
							//header("Content-type: application/octet-stream");
							//header("Accept-Ranges: bytes");
							//header("Accept-Length: ".$size);

							$blocksize=1024;
							if($file) {
								if(isset($start)&&$start>0&&$start<$filesize) {
									info("file skip ".$start);
									fseek($file, $start);
									Header("HTTP/1.1 206 Partial Content");
									Header("Content-Length: " . ($filesize-$start));
									Header("Content-Ranges: bytes" . $start . "-" . ($filesize - 1) . "/" . $filesize);
								} else {
									Header("Content-Length: ".$filesize);
									Header("Accept-Ranges: bytes");
								}
								info("downloading...");
								header( "Cache-Control: public" );
								header( "Pragma: public" ); 
								header( "Content-Disposition: inline; filename=".$filename) ;  
								$index = 0;
								while(!feof($file)) {
									echo fread($file,$blocksize);
									// info("index: ".$index++."  time: ".time());
								}
								info("downloading finished!");
								$success_num++;
								$success_ratio = number_format($success_num/$download_num, 2);
								$sql3 = "UPDATE delta ". "SET sucess_num = $success_num, sucess_ratio = $success_ratio ". "WHERE delta_id = $delta_id";
								info($sql3);
								$retval = mysql_query($sql3);
								if(!$retval ) {
									header("Could not update data: " . mysql_error());
									info("Could not update data: " . mysql_error());
								}
							}
							if($file) {
								fclose($file);
							}
						}
					} else {
						header('HTTP/1.1 404 File Not Found');  
					}
					mysql_close($tb);
				}
			}	    	 	
		}
	}
}

?>
