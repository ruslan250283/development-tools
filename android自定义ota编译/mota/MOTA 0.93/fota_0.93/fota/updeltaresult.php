<?
require_once ('db.php');
$db = connect_database ();
if (! $db) {
    echo "<table align='center'><tr><td height='10px'></td></tr><tr><td><font color='#FF0000'>Database Error! Please try again!</font></td></tr></table>";
    ?>
<script type="text/javascript">
     <?php
    echo "parent.UP.stop(3,null);";
    ?>
  </script>
<?
} else {
    if ($_SERVER ['REQUEST_METHOD'] == "POST") {
        $num = $_GET ['num'];
        $version = $_GET ['deltaversion'];
        $versionId = $_GET ['versionId'];
        $upload_path = $_GET ['up'];
        if (! isset ( $num ) || $num == 0 || ! isset ( $version ) || strlen ( $version ) <= 0) {
            ?>
<script type="text/javascript">
        <?php
            echo "parent.UP.stop(3,null,100);";
            ?>
       </script>
<?
            echo "<table align='center'><tr><td height='10px'></td></tr><tr><td><font color='#FF0000'>The version is not exist!!</font></td></tr></table>";
        } else {
            
            $filenums = count ( $_FILES );
            if ($filenums <= 0) {
                ?>
<script type="text/javascript">
          <?php
                echo "parent.UP.stop(0,null,100);";
                ?>
         </script>
<?
            } else {
                
                // get the latest version
                // database operstion 1
                $sql1 = 'select version from version_detail where version_id=' . $versionId . ' order by version_time desc limit 1';
                $query1 = mysql_query ( $sql1 );
                if (mysql_affected_rows () <= 0) {
                    ?>
<script type="text/javascript">
           <?php
                    echo "parent.UP.stop(3,null,100);";
                    ?>
           </script>
<?
                    echo "<table align='center'><tr><td height='10px'></td></tr><tr><td><font color='#FF0000'>The version is not exist!</font></td></tr></table>";
                } else {
                    $result1 = mysql_fetch_array ( $query1 );
                    $latestversion = $result1 ['version'];
                    //
                    $result_str = '';
                    for($i = 0; $i < $num; $i ++) {
                        $filename = $_FILES ['upfile' . $i] ['name'];
                        
                        if (isset ( $filename ) && strlen ( $filename ) > 0) {
                            if (is_uploaded_file ( $_FILES ['upfile' . $i] ['tmp_name'] )) {
                                $version_path = $upload_delta_dir . $upload_path . $_POST ['oldversion' . $i] . '/' . time () . $filename;
                                if (move_uploaded_file ( $_FILES ['upfile' . $i] ['tmp_name'], $version_path )) {
                                    // $deltaId=$_POST['deltaId'.$i];
                                    $size = $_FILES ['upfile' . $i] ['size'];
                                    
                                    $old_version = $_POST ['oldversion' . $i];
                                    // database operation 4
                                    $sql4 = 'select delta_id,delta_path from delta where delta_version="' . $version . '" and old_version="' . $old_version . '" and version_id=' . $versionId;
                                    $query4 = mysql_query ( $sql4 );
                                    if (mysql_affected_rows () > 0) {
                                        $result4 = mysql_fetch_array ( $query4 );
                                        $deltaId = $result4 ['delta_id'];
                                        $deltapath = $result4 ['delta_path'];
                                    }
                                    if (isset ( $deltaId ) && $deltaId > 0) {
                                        // update the delta info
                                        // database operation 2
                                        $sql2 = 'update delta set delta_name="' . $filename . '",delta_size=' . $size . ',delta_path="' . $version_path . '" where delta_id=' . $deltaId;
                                        mysql_query ( $sql2 );
                                        if (mysql_affected_rows () > 0 || mysql_errno () == 0) {
                                            $result_str .= $old_version . ' update sucess <br>';
                                            unlink ( $deltapath );
                                        } else {
                                            $result_str .= $old_version . ' update fail!! <br>';
                                        }
                                    } else {
                                        $old_version = $_POST ['oldversion' . $i];
                                        // insert into the database
                                        // database operation 3
                                        $sql3 = 'insert into delta (version_id,delta_name,delta_size,delta_path,old_version,delta_version) values (' . $versionId . ',"' . $filename . '",' . $size . ',"' . $version_path . '","' . $old_version . '","' . $version . '")';
                                        $query3 = mysql_query ( $sql3 );
                                        // echo $sql3;
                                        if (mysql_affected_rows () <= 0) {
                                            $result_str .= $old_version . ' update fail! <br>';
                                        } else {
                                            if (strcmp ( $version, $latestversion ) == 0) {
                                                // update the latest delta info
                                                // database operation 4
                                                $sql4 = 'update version_detail set delta_id=' . mysql_insert_id () . ',delta_version="' . $version . '" where version="' . $old_version . '" and version_id=' . $versionId;
                                                $query = mysql_query ( $sql4 );
                                                if (mysql_affected_rows () <= 0) {
                                                    $result_str .= $old_version . ' update fail !<br>';
                                                } else {
                                                    $result_str .= $old_version . ' update sucess <br>';
                                                }
                                            } else {
                                                $result_str .= $old_version . ' update sucess <br>';
                                            }
                                        }
                                    }
                                    ?>
<head>
<script type="text/javascript">
                         <?php
                                    echo "parent.UP.stop(2,'" . $filename . "',null);";
                                    ?>
                        </script>
</head>
<?
                                } else {
                                    ?>
<head>
<script type="text/javascript">
                         <?php
                                    echo "parent.UP.stop(3,'" . $filename . "',null);";
                                    ?>
                        </script>
</head>
<?
                                }
                            } else {
                                ?>
<script type="text/javascript">
                         <?php
                                echo "parent.UP.stop(3,'" . $filename . "',null);";
                                ?>
                        </script>
<?
                            }
                        }
                    }
                    //
                }
                echo $result_str;
                ?>
<script type="text/javascript">
              <?php
                echo "parent.UP.stop(2,'all files ',100);";
                ?>
             </script>
<?
            }
        }
    } else {
        ?>
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
<?
    }
}
?>
