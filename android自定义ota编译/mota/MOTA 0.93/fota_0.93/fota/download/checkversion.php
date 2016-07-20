<?php
session_start();
require_once('config.php');
require_once('fotalog.php');
@ $version=$_POST['version'];
@ $token=urldecode($_POST['token']);
info("##start check version...");
if(!isset($token)||strlen($token)<=0){
	echo $token_lost_json;
} else if(!isset($version)||strlen($version)<=0){
	echo $version_lost_json;
} else {
	$vercode=IsTokenInvalid($token);
	if($vercode==$illegal_code){
		echo $illegal_json;
	} else if($vercode==$token_invalid_code){
		echo $token_invalid_json;
	} else if($vercode==$token_valid_code){
		$version=strtolower($version);
		info("token is valid, version = $version");
		$array_ver = explode('_', $version);
		if(count($array_ver)<5) {
			echo $param_invalid_json;
		} else {
			$tb=connect_database();
			info("connect database...");
			if(!isset($tb)) {
				echo $tb_error_json;
			} else {
				$istest_device=$_SESSION["istest_device"];
				$now=time();
				$user_operator = $array_ver[4];
				$user_operator=trim($user_operator);
				if(!isset($user_operator)||strlen($user_operator)==0){
					$user_operator = "null";
				}else if(!strcmp($user_operator,"cu")){
					$user_operator = "op02";
				}else if(!strcmp($user_operator,"cmcc")){
					$user_operator = "op01";
				}else if(!strcmp($user_operator,"orange")){
					$user_operator = "op02";
				}
				$user_product = $array_ver[1];
				$user_version_number = $array_ver[3];
				$user_version_name = $user_product.'.'.$user_version_number;
				info("user_version_name = $user_version_name");
				@ $sle.=' version.oem="'.$array_ver[0].'"';
	
	            info("product = $user_product; version = $user_version_number");
				
				//$sle.=' and version.region="'.$array_ver[2].'"';
				//$sle.=' and version.operator="'.$user_operator.'"';
				$sle_pre = $sle;
				$sle_pre.=' and (version.product="'.$user_product.'"';
				$sle_pre.=' or version.product="'.str_replace('$','_',$user_product).'"'.")";
								 
				//check current version is publish
				$sql_precheck = "select version_detail.fingerprint,version_detail.scattermd5,version.version_id from version_detail,version where "
								.$sle_pre.' and version="'.$user_version_number.'"'
								." and version_detail.version_id=version.version_id and version_detail.version<>'null' and version_detail.version<>'' and ((is_publish=2 and "
								.$now.">=publish_time) or (is_publish=1 and ".$istest_device."=1 and ".$now.">=publish_time))";
				
				info("sql pre check = $sql_precheck");
				
				$query_precheck=mysql_query($sql_precheck);
               
				if(mysql_affected_rows()<1) {
					echo $version_invalid_json;
					mysql_free_result($query_precheck);
					return;
				}
				$pre_result=mysql_fetch_array($query_precheck);
                $user_md5_value = $pre_result['scattermd5'];
				$user_fingerprint = $pre_result['fingerprint'];
				$user_version_id = $pre_result["version_id"];
				
				info("version_id = $user_version_id; old_version = $user_version_number");

				//check if has delta package
				$sle2='delta.version_id="'.$user_version_id.'" and delta.old_version="'.$user_version_number.'"';
				$sql2='select delta.delta_id,delta.delta_notes,delta.delta_size, delta.new_version_id, delta.delta_version from delta where '.$sle2 . ' order by delta_id desc';
				$query2=mysql_query($sql2);
				
				info("check delta = $sql2");
				
                if(mysql_affected_rows()<1){
					$log1 =111;
					info($log1);
					echo $version_latest_json;
					mysql_free_result($query2);
					return;
				}
				$myResult="";
                while($result2=mysql_fetch_array($query2)) {
					$release_note=$result2['delta_notes'];
					$delta_size=$result2['delta_size'];
					$delta_id=$result2['delta_id'];
					$target_version_id = $result2['new_version_id'];
					$target_version = $result2["delta_version"];
					#if (strcmp($target_version, $user_version_number)==0) {//need check
					#	echo $version_latest_json;
					#	return;
					#}
					if (empty($target_version_id)) {//the old data
						//select the latast version
						$sql3 = "select version_detail.android_version,version_detail.scattermd5,version_detail.version_size, 
								version_detail.version_id,version_detail.release_notes,version_detail.version, 
								version_detail.version_name,version_detail.delta_id,version_detail.version_time from version_detail,version where "
								.$sle." and version_detail.version_id=version.version_id and version_detail.version_id = "
								.$user_version_id." and version_detail.version<>'null' and version_detail.version<>'' and ((is_publish=2 and "
								.$now.">=publish_time) or (is_publish=1 and ".$istest_device."=1 and "
								.$now.">=publish_time)) order by version_detail.version_time desc";
					} else {
						$sle3='version_detail.version_id="'.$target_version_id .'" and version_detail.version="'.$target_version.'"';
						$sql3 = "select version_detail.android_version,version_detail.version_size,version_detail.release_notes, 
								version_detail.version,version_detail.version_name,version_detail.scattermd5,version_detail.delta_id, 
								version_detail.version_id,version.product,version_detail.version_time from version_detail,version 
								where version.version_id = version_detail.version_id and $sle and $sle3 and  (is_publish=2 and "
								.$now.">=publish_time) or (is_publish=1 and ".$istest_device."=1 and ".$now.">=publish_time) order by version_detail.version_time desc";
					}
					info("target_version_id = $target_version_id; target_version = $target_version; ");
					$query3=mysql_query($sql3);
					info("check the lastest version = $sql3");
					if(mysql_affected_rows()>0) {
						$result3=mysql_fetch_array($query3);
						$name=$result3["version_name"];
						$target_version_notes=$result3["release_notes"];
						$target_version_number=$result3['version'];
						$android_version=$result3['android_version'];
						$version_id=$result3['version_id'];
						$target_md5_value = $result3['scattermd5'];
						$target_full_size = $result3['version_size'];
                        $release_date = $result3['version_time'];
						$download_version_name = $result3['product'].'.'.$target_version_number;
						info("download_version_name = $download_version_name");
						if(isset($user_md5_value) && isset($target_md5_value)){
							$partition_changed = strcmp($user_md5_value, $target_md5_value);
							if($partition_changed){
								info("partition_changed = $partition_changed");
								$result = '{"status":'.$version_check_sucess.',"name":"'.$target_version.'",
											"android_version":"'.$android_version.'","size":'.$target_full_size.',
											"release_notes":"'.$target_version_notes.'","versionId":'.$version_id.',"release_date":"'.$release_date.'"}';
								// session_register("versionId");
								$_SESSION["versionId"]=$version_id;
								// session_register("version");
								$_SESSION["version"]=$target_version;
								echo $result;
								mysql_free_result($query);
								return;
							}
						}
						if (strcmp($target_version_number,$user_version_number)==0 && strcmp($version_id, $target_version_id)) {
							echo $version_latest_json;
							return;
						}
						$result='{"status":'.$version_check_sucess.',"name":"'.$target_version.'","size":'.$delta_size.',
									"android_version":"'.$android_version.'","release_notes":"'.$target_version_notes.'",
									"deltaId":'.	$delta_id.',"fingerprint":"'.$user_fingerprint.'","release_date":"'.$release_date.'"}';
						$delta_id=$result2['delta_id'];
						// session_register("deltaId");
						$_SESSION["deltaId"]=$delta_id;
						echo $result;
						mysql_free_result($query3);
						return;
					} else {
						if(empty($myResult)){
							$myResult = $version_latest_json;
						}
					}
                }
				echo $myResult;
			}
		}
	}
 }
?>
