<?PHP
require_once ('db.php');
$db = connect_database ();
if (! $db) {
    echo "<table align='center'><tr><td height='10px'></td></tr><tr><td><font color='#FF0000'>Database Error! Please try again!</font></td></tr></table>";
} else {
    //
    if ($_SERVER ['REQUEST_METHOD'] == "POST") {
        $upload_path = '';
        $oem_num = $_POST ['oem'];
        if ($oem_num == 1) {
            $oem_str = strtolower ( str_replace ( "_", "$", $_POST ['oem_sle'] ) );
        } else if ($oem_num == 2) {
            $oem_str = strtolower ( str_replace ( "_", "$", $_POST ['oem_text'] ) );
        }
        if (! isset ( $oem_str ) || strlen ( $oem_str ) <= 0) {
            echo "<font color='#FF0000'><b>Error! the oem can not be null!</b></font>";
        } else {
            $upload_path .= "OEM_" . $oem_str . "/";
            $product_num = $_POST ['product'];
            if ($product_num == 1) {
                $product_str = strtolower ( str_replace ( "_", "$", $_POST ['product_sle'] ) );
            } else if ($product_num == 2) {
                $product_str = strtolower ( str_replace ( "_", "$", $_POST ['product_text'] ) );
            }
            if (! isset ( $product_str ) || strlen ( $product_str ) <= 0) {
                echo "<font color='#FF0000'><b>Error! the product can not be null!</b></font>";
            } else {
                $upload_path .= "PRO_" . $product_str . "/";
                $region_num = $_POST ['region'];
                if ($region_num == 1) {
                    $region_str = strtolower ( str_replace ( "_", "$", $_POST ['region_sle'] ) );
                } else {
                    $region_str = strtolower ( str_replace ( "_", "$", $_POST ['region_text'] ) );
                }
                if (! isset ( $region_str ) || strlen ( $region_str ) <= 0) {
                    echo "<font color='#FF0000'><b>Error! the region can not be null!</b></font>";
                } else {
                    $upload_path .= "REG_" . $region_str . "/";
                    $operator_num = $_POST ['operator'];
                    if ($operator_num == 1) {
                        $operator_str = strtolower ( str_replace ( "_", "$", $_POST ['operator_sle'] ) );
                    } else if ($operator_num == 2) {
                        $operator_str = strtolower ( str_replace ( "_", "$", $_POST ['operator_text'] ) );
                    } else if ($operator_num == 3) {
                        $operator_str = '';
                    }
                    if ($operator_num != 3 && (! isset ( $operator_str ) || strlen ( $operator_str ) <= 0)) {
                        echo "<font color='#FF0000'><b>Error! please select/enter the correct operator or select the TBD</b></font>";
                    } else {
                        $upload_path .= "OPE_" . $operator_str . "/";
                        $version = strtolower ( str_replace ( "_", "$", $_POST ['version'] ) );
                        if (! isset ( $version ) || strlen ( $version ) <= 0) {
                            echo "<font color='#FF0000'><b>Error! please enter the correct version.</b></font>";
                        } else {
                            $upload_path .= $version . "/";
                            $version_name = $_POST ['version_name'];
                            if (! isset ( $version_name ) || strlen ( $version_name ) <= 0) {
                                echo "<font color='#FF0000'><b>Error! please enter the correct version name.</b></font>";
                            } else {
                                $release_notes = $_POST ['notes'];
                                if (! isset ( $release_notes ) || strlen ( $release_notes ) <= 0) {
                                    echo "<font color='#FF0000'><b>Error! please enter the correct release notes.</b></font>";
                                } else {
                                    $filename = $_FILES ['upfile'] ['name'];
                                    if (! isset ( $filename ) || strlen ( $filename ) <= 0) {
                                        ?>
<script type="text/javascript">
                    <?php
                                        echo "parent.UP.stop(0);";
                                        ?>
                   </script>
<?
                                        echo "<font color='#FF0000'><b>Error! please select the file you want to upload.</b></font>";
                                    } else {
                                        $max_size = return_bytes ( ini_get ( 'upload_max_filesize' ) );
                                        $version_size = $_FILES ['upfile'] ['size'];
                                        if ($version_size > $max_size) {
                                            echo "<font color='#FF0000'><b>Error! The max file size is $max_size</b></font>";
                                            ?>
<head>
<script type="text/javascript">
                        <?php
                                            echo "parent.UP.stop(1);";
                                            ?>
                        </script>
</head>
<?
                                        } else {
                                            if (is_uploaded_file ( $_FILES ['upfile'] ['tmp_name'] )) {
                                                $dirs = explode ( "/", $upload_path );
                                                $dir_path = $upload_version_dir;
                                                for($i = 0; $i < count ( $dirs ); $i ++) {
                                                    $dir_path .= $dirs [$i] . "/";
                                                    if (! file_exists ( $dir_path )) {
                                                        mkdir ( $dir_path, 0777 );
                                                    }
                                                }
                                                $version_path = $upload_version_dir . $upload_path . $filename;
												echo $dirs;
												echo $dir_path;
												echo $version_path;
                                                if (move_uploaded_file ( $_FILES ['upfile'] ['tmp_name'], $version_path )) {
                                                    ?>
<head>
<script type="text/javascript">
                           <?php
                                                    echo "parent.UP.stop(2);";
                                                    ?>
                           </script>
</head>
<?
                                                    // get the version id
                                                    // database operation 1
                                                    $sql1 = 'select version_id from version where oem="' . $oem_str . '" and product="' . $product_str . '" and region="' . $region_str . '" and operator="' . $operator_str . '"';
                                                    $query1 = mysql_query ( $sql1 );
                                                    
                                                    if (mysql_affected_rows () > 0) {
                                                        $result1 = mysql_fetch_array ( $query1 );
                                                        $version_id = $result1 ['version_id'];
                                                    } else {
                                                        // insert into the version info
                                                        // database operation 2
                                                        $sql2 = 'insert into version(oem,product,region,operator) values ("' . $oem_str . '","' . $product_str . '","' . $region_str . '","' . $operator_str . '")';
                                                        $query2 = mysql_query ( $sql2 );
                                                        $version_id = mysql_insert_id ();
                                                    }
                                                    if (! isset ( $version_id ) || strlen ( $version_id ) <= 0) {
                                                        echo "<font color='#FF0000'><b>Error!! The version is illegal!!!</b></font>";
                                                    } else {
                                                        $version_time = filectime ( $version_path );
                                                        $publish_time = 0;
                                                        if (isset ( $_POST ['publish'] ) && strlen ( $_POST ['publish'] ) > 0) {
                                                            $publish = $_POST ['publish'];
                                                            // insert into the version_detail info
                                                            // database operation 3
                                                            $time_str = $_POST ['date1'];
                                                            $time_array = explode ( "-", $time_str );
                                                            $hour = $_POST ['time'];
                                                            $publish_time = mktime ( $hour, 0, 0, $time_array [1], $time_array [2], $time_array [0] );
                                                        }
                                                        if (! isset ( $publish_time ) || $publish_time < 0 || strlen ( $publish_time ) <= 0) {
                                                            $publish_time = 0;
                                                        }
                                                        $fingerprint = $_POST ['fingerprint_text'];
                                                        $androidversion = $_POST ['androidversion_text'];
                                                        $upgrade_type = $_POST ['scatter'];
                                                        // echo $publish_time;
                                                        $sql3 = 'insert into version_detail (version_id,version,version_name,version_path,version_size,version_compress,release_notes,version_time,update_time,is_publish,publish_time,fingerprint,android_version,scattermd5) values (' . $version_id . ',"' . $version . '","' . $version_name . '","' . $version_path . '",' . $version_size . ',0,"' . $release_notes . '",' . $version_time . ',' . time () . ',' . $publish . ',' . $publish_time . ',"' . $fingerprint . '"' . ',"' . $androidversion . '"' . ',"' . $upgrade_type . '"'. ')';
//                                                          echo $sql3;
                                                        $query3 = mysql_query ( $sql3 );
                                                        if (mysql_affected_rows () <= 0) {
                                                            echo "<font color='#FF0000'><b>Error! The version is illegal</b></font>";
                                                        } else {
                                                            echo "<font color='#FF0000'><b>Upload Sucess!</b></font>";
                                                            $sql4 = 'update version_detail set scattermd5=1 where scattermd5 is NULL;';
                                                            mysql_query ( $sql4 );
                                                            $query3 = mysql_query ( $sql3 );
                                                        }
                                                    }
                                                } else {
                                                    echo "<font color='#FF0000'><b>Error! upload fail, please try again</b></font>";
                                                    ?>
<head>
<script type="text/javascript">
                             <?php
                                                    echo "parent.UP.stop(3);";
                                                    ?>
                             </script>
</head>
<?
                                                }
                                            } else {
                                                echo "<font color='#FF0000'><b>Error! Can not upload the file. please try again.</b></font>";
                                                ?>
<head>
<script type="text/javascript">
                             <?php
                                                echo "parent.UP.stop(3);";
                                                ?>
                             </script>
</head>
<?
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    } else {
        ?>
<html>
<head>
<script type="text/javascript">
	 	  		<?
        $info = uploadprogress_get_info ( $_GET ['ID'] );
        if ($info != null) {
            // echo json_encode($uploadvalues);
            print "parent.UP.updateInfo(" . $info ['bytes_uploaded'] . "," . $info ['bytes_total'] . "," . $info ['est_sec'] . ")";
        } else {
            print 'parent.UP.updateInfo()';
        }
        ?>
	 	  		</script>
</head>
</html>
<?
    }
    
    //
}
function return_bytes($val) {
    $val = trim ( $val );
    $last = strtolower ( $val [strlen ( $val ) - 1] );
    switch ($last) {
        // The 'G' modifier is available since PHP 5.1.0
        case 'g' :
            $val *= 1024;
        case 'm' :
            $val *= 1024;
        case 'k' :
            $val *= 1024;
    }
    
    return $val;
}

?>
