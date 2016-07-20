<?
require_once ("fotalog.php");
session_start ();
$username = $_SESSION ['username'];
$password = $_SESSION ['password'];
$oem_sele = $_POST ['oem'];
$product_sele = $_POST ['product'];
$region_sele = $_POST ['region'];
$operator_sele = $_POST ['operator'];

$page = $_GET ['page'];
if (! isset ( $page )) {
    $page = 0;
}
$num = 11;
$start = $page * $num;

if (isset ( $oem_sele ) && strlen ( $oem_sele ) > 0 && strcmp ( $oem_sele, 'ALL' ) != 0) {
    if (isset ( $sle ) && strlen ( $sle ) > 0)
        $sle .= " and ";
    $sle .= 'oem="' . $oem_sele . '"';
}
if (isset ( $product_sele ) && strlen ( $product_sele ) > 0 && strcmp ( $product_sele, 'ALL' ) != 0) {
    if (isset ( $sle ) && strlen ( $sle ) > 0)
        $sle .= " and ";
    $sle .= 'product="' . $product_sele . '"';
}
if (isset ( $region_sele ) && strlen ( $region_sele ) > 0 && strcmp ( $region_sele, 'ALL' ) != 0) {
    if (isset ( $sle ) && strlen ( $sle ) > 0)
        $sle .= " and ";
    $sle .= 'region="' . $region_sele . '"';
}
if (isset ( $operator_sele ) && strlen ( $operator_sele ) > 0 && strcmp ( $operator_sele, 'ALL' ) != 0) {
    if (isset ( $sle ) && strlen ( $sle ) > 0)
        $sle .= " and ";
    $sle .= 'operator="' . $operator_sele . '"';
}
if (! isset ( $username ) || ! isset ( $password ) || strlen ( $username ) <= 0 || strlen ( $password ) <= 0) {
    header ( "Location: userlogin.php?access=400" );
} else {
    require_once ('db.php');
    require_once ('header.php');
    $db = connect_database ();
    if (! $db) {
        echo "<table align='center'><tr><td height='10px'></td></tr><tr><td><font color='#FF0000'>Database Error! Please try again!</font></td></tr></table>";
    } else {
        
        if (isset ( $_POST ['vid'] )) {
            $i = 0;
            foreach ( $_POST ['vid'] as $item ) {
                if ($i > 0) {
                    $del_sle .= ' or  ';
                    $del_delta .= ' or ';
                }
                $del_sle .= ' version="' . $item . '"';
                $del_delta .= ' delta_version="' . $item . '" or old_version="' . $item . '"';
                $i ++;
            }
            for($i = 0; i < count ( $_POST ['vid'] ); $i ++) {
                $version_number = $_POST ['vid'] [$i];
                $version_id = $_POST ['versionId'] [$i];
                // echo "version number is ".$version_number." version id is ".$version_id."<br>";
            }
            // get the file path, and delete the file
            // database operation 6
            $sql6 = 'select version_path from version_detail where ' . $del_sle;
            $query6 = mysql_query ( $sql6 );
            while ( $result6 = mysql_fetch_array ( $query6 ) ) {
                if (file_exists ( $result6 ['version_path'] )) {
                    // $ret=unlink($result6['version_path']);
                    if ($ret == 0) {
                        delete ( "<br><br><br>DELETE ERROR---------------------" );
                        delete ( $result6 ['version_path'] );
                        delete ( "<br><br><br>DELETE ERROR---------------------" );
                    } else {
                        delete ( "<br><br><br>DELETE SUCESS---------------------" );
                        delete ( $result6 ['version_path'] );
                        delete ( "<br><br><br>DELETE SUCESS---------------------" );
                    }
                }
            }
            // $sql7='select delta_path from delta where delta_id in (select delta_id from version_detail where '.$del_delta.')';
            $sql7 = 'select delta_path from delta where ' . $del_delta;
            $query7 = mysql_query ( $sql7 );
            while ( $result7 = mysql_fetch_array ( $query7 ) ) {
                if (file_exists ( $result7 ['delta_path'] )) {
                    // $ret=unlink($result7['delta_path']);
                    if ($ret == 0) {
                        delete ( "<br><br><br>DELETE ERROR---------------------" );
                        delete ( $result7 ['delta_path'] );
                        delete ( "<br><br><br>DELETE ERROR---------------------" );
                    } else {
                        delete ( "<br><br><br>DELETE SUCESS---------------------" );
                        delete ( $result7 ['delta_path'] );
                        delete ( "<br><br><br>DELETE SUCESS---------------------" );
                    }
                }
            }
            // echo $sql6."<br>";
            // echo $sql7."<br>";
            // delete the version and the correspond delta
            // database operation 4
            $sql4 = 'delete from version_detail where ' . $del_sle;
            // mysql_query($sql4);
            // $sql5='delete from delta where delta_id in (select delta_id from version_detail where '.$del_delta.' )';
            $sql5 = 'delete from delta where ' . $del_delta;
            // mysql_query($sql5);
            
            // echo $sql4."<br>";
            // echo $sql5;
        }
        
        // get versions info
        // database operaton 1
        $sql1 = 'select oem,product,region,operator from version';
        $query1 = mysql_query ( $sql1 );
        if (mysql_affected_rows () <= 0) {
            echo "<table align='center'><tr><td height='10px'></td></tr><tr><td><font color='#FF0000'>There is not any version now, You can <a href=''>click here</a> to upload some versions now</font></td></tr></table>";
        } else {
            $oem_num = 0;
            $product_num = 0;
            $region_num = 0;
            $operator_num = 0;
            $oem_array = array ();
            $oem_array [$oem_num ++] = "ALL";
            $product_array = array ();
            $product_array [$product_num ++] = "ALL";
            $region_array = array ();
            $region_array [$region_num ++] = "ALL";
            $operator_array = array ();
            $operator_array [$operator_num ++] = "ALL";
            while ( $result1 = mysql_fetch_array ( $query1 ) ) {
                $oem = trim ( $result1 ['oem'] );
                $product = $result1 ['product'];
                $region = $result1 ['region'];
                $operator = $result1 ['operator'];
                if (strlen ( $oem ) > 0) {
                    for($i = 0; $i < sizeof ( $oem_array ); $i ++) {
                        if (strcmp ( $oem_array [$i], $oem ) == 0)
                            break;
                    }
                    if ($i == sizeof ( $oem_array )) {
                        $oem_array [$oem_num ++] = $oem;
                    }
                }
                if (strlen ( $product ) > 0) {
                    for($i = 0; $i < sizeof ( $product_array ); $i ++) {
                        if (strcmp ( $product_array [$i], $product ) == 0)
                            break;
                    }
                    if ($i == sizeof ( $product_array ))
                        $product_array [$product_num ++] = $product;
                }
                if (strlen ( $region ) > 0) {
                    for($i = 0; $i < sizeof ( $region_array ); $i ++) {
                        if (strcmp ( $region_array [$i], $region ) == 0)
                            break;
                    }
                    if ($i == sizeof ( $region_array ))
                        $region_array [$region_num ++] = $region;
                }
                if (strlen ( $operator ) > 0) {
                    for($i = 0; $i < sizeof ( $operator_array ); $i ++) {
                        if (strcmp ( $operator_array [$i], $operator ) == 0)
                            break;
                    }
                    if ($i == sizeof ( $operator_array ))
                        $operator_array [$operator_num ++] = $operator;
                }
            }
            asort ( $oem_array );
            $oem_str = '';
            for($i = 0; $i < sizeof ( $oem_array ); $i ++) {
                if (strcmp ( $oem_array [$i], $oem_sele ) == 0) {
                    $oem_str .= '<option value ="' . $oem_array [$i] . '" selected="selected">' . $oem_array [$i] . '</option>';
                } else
                    $oem_str .= '<option value ="' . $oem_array [$i] . '">' . $oem_array [$i] . '</option>';
            }
            asort ( $product_array );
            $product_str = '';
            for($i = 0; $i < sizeof ( $product_array ); $i ++) {
                if (strcmp ( $product_array [$i], $product_sele ) == 0)
                    $product_str .= '<option value ="' . $product_array [$i] . '" selected="selected">' . $product_array [$i] . '</option>';
                else
                    $product_str .= '<option value ="' . $product_array [$i] . '">' . $product_array [$i] . '</option>';
            }
            asort ( $region_array );
            $region_str = '';
            for($i = 0; $i < sizeof ( $region_array ); $i ++) {
                if (strcmp ( $region_array [$i], $region_sele ) == 0)
                    $region_str .= '<option value ="' . $region_array [$i] . '" selected="selected">' . $region_array [$i] . '</option>';
                else
                    $region_str .= '<option value ="' . $region_array [$i] . '">' . $region_array [$i] . '</option>';
            }
            asort ( $operator_array );
            $operator_str = '';
            for($i = 0; $i < sizeof ( $operator_array ); $i ++) {
                if (strcmp ( $operator_array [$i], $operator_sele ) == 0)
                    $operator_str .= '<option value ="' . $operator_array [$i] . '" selected="selected">' . $operator_array [$i] . '</option>';
                else
                    $operator_str .= '<option value ="' . $operator_array [$i] . '">' . $operator_array [$i] . '</option>';
            }
            // calculate the page nums
            // database operation 3
            $sql3 = 'select version_id,version,version_name,version_size,release_notes,publish_time,is_publish from version_detail';
            if (isset ( $sle ) && strlen ( $sle ) > 0) {
                $sql3 = 'select version_detail.version_id,version_detail.version,version_detail.version_name,version_detail.version_size,version_detail.release_notes,
           	 version_detail.publish_time,version_detail.is_publish from version_detail,version where ' . $sle . ' and version_detail.version_id=version.version_id';
            }
            $query3 = mysql_query ( $sql3 );
            $page_nums = (mysql_affected_rows () / $num);
            // get the info of version detail
            // database operation 2
            $sql2 = 'select version_id,version,version_name,version_size,release_notes,version_time,publish_time,is_publish from version_detail';
            if (isset ( $sle ) && strlen ( $sle ) > 0) {
                $sql2 = 'select version_detail.version_id,version_detail.version,version_detail.version_name,version_detail.version_size,version_detail.release_notes,version_detail.version_time,version_detail.publish_time,version_detail.is_publish,version_detail.delta_id from version_detail,version where ' . $sle . ' and version_detail.version_id=version.version_id';
            }
            $sql2 .= ' order by version_time desc ';
            $sql2 .= ' limit ' . $start . ',' . $num;
            $query2 = mysql_query ( $sql2 );
            $version_detail_str = '';
            $i = 0;
            while ( $result2 = mysql_fetch_array ( $query2 ) ) {
                if ($i ++ % 2 != 0)
                    $version_detail_str .= '<tr align="center" bgcolor="#E8EEF7" class="fon6">';
                else
                    $version_detail_str .= '<tr align="center" bgcolor="#FFFFFF" class="fon6">';
                
                $version_detail_str .= '<td><Input type="checkbox" name="vid[]" value="' . $result2 ['version'] . '"></td>';
                $version_detail_str .= '<td width="150px"><div class="break" style="width:150px" name="versionId[]" value="' . $result2 ['version_id'] . '">' . $result2 ['version'] . '</div></td>';
                $version_detail_str .= '<td width="150px">' . $result2 ['version_name'] . '</td>';
                $version_size = round ( $result2 ['version_size'] / (1024 * 1024), 2 );
                $version_detail_str .= '<td width="150px">' . $version_size . 'M</td>';
                $version_detail_str .= '<td width="150px" align="left"><div class="break" style="width:150px"><br>' . $result2 ['release_notes'] . '<br></td>';
                $version_detail_str .= '<td width="150px">' . date ( "Y-m-d G:i:s", $result2 ['publish_time'] ) . '</td>';
                $status = '';
                if ($result2 ['is_publish'] == 0) {
                    $status = 'Non-publish';
                } else if ($result2 ['is_publish'] == 1) {
                    $status = 'Internal-test';
                } else if ($result2 ['is_publish'] == 2) {
                    $status = 'Publish';
                }
                $version_detail_str .= '<td width="150px">' . $status . '</td>';
                $version_detail_str .= '<td width="100px"><a href="delta.php?versionId=' . $result2 ['version_id'] . '&version=' . $result2 ['version'] . '">delat info</a></td>';
                $version_detail_str .= '<td width="100px"><a href="edit.php?version=' . $result2 ["version"] . '&versionId=' . $result2 ['version_id'] . '">edit</a></td>';
                $version_detail_str .= '<td width="100px"><a href="uploaddelta.php?version=' . $result2 ["version"] . '&versionId=' . $result2 ['version_id'] . '&versiontime=' . $result2 ['version_time'] . '">upload</a></td>';
                $version_detail_str .= '</tr>';
            }
            for($i = 0; $i < $page_nums; $i ++) {
                if ($page == $i)
                    $page_str .= '<a style="color:#FF0000;font-weight:bold;font-family:Times New Roman;font-size:16px;">' . ($i + 1) . '</a>&nbsp;&nbsp;&nbsp;';
                else
                    $page_str .= '<a href="manage.php?page=' . $i . '" style="color:#3B5998;font-weight:bold;font-family:Times New Roman;font-size:16px;">' . ($i + 1) . '</a>&nbsp;&nbsp;&nbsp;';
            }
            ?>
<html>
<head>
<link type="text/css" rel="StyleSheet" href="css/css.css" />
<link type="text/css" rel="StyleSheet" href="css/navg.css" />
</head>
<body>
  <div>
    <table style="width: 100%">
      <tr>
        <td width='4%'></td>
        <td>
          <form action='manage.php' method="post">
            <!--------->
            <table align='center' bgcolor="#FFCC00" style="width: 100%">
              <tr>
                <td height='5px'></td>
              </tr>
              <tr align='center'>
                <td width='16%'></td>
                <td class='fon7'>OEM</td>
                <td><Select style="WIDTH: 111px; height: 25px;" name="oem">
                  <?  echo $oem_str;?>
			  	      </Select></td>
                <td width='1%'></td>
                <td class='fon7'>Product</td>
                <td><Select style="WIDTH: 111px; height: 25px;" name="product">
                <? echo $product_str;?>
					     </Select></td>
                <td width='1%'></td>
                <td class='fon7'>Language</td>
                <td><Select style="WIDTH: 111px; height: 25px;" name="region">
                 <?  echo $region_str;?>
					     </Select></td>
                <td width='1%'></td>
                <td class='fon7'>Operator</td>
                <td><Select style="WIDTH: 111px; height: 25px;" name="operator">
                 <?  echo $operator_str;?>
					    </Select></td>
                <td width='1%'></td>
                <td><input type="submit" value="Search"
                  style="WIDTH: 70px; HEIGHT: 25px; background-color: #C4D1EE; color: #595959; font-family: Arial; font-weight: bold;"></td>
                <td width='16%'></td>
              </tr>
              <tr>
                <td height='5px'></td>
              </tr>
            </table>
          </form> <!-------->
        </td>
        <td width='7%'></td>
      </tr>
    </table>
    <form action='manage.php' method='post'>
      <table style="width: 100%">
        <tr>
          <td width='4%'></td>
          <td>
            <table cellpadding="0" cellspacing="0">
              <tr class="fon1" bgcolor="#D5DFF3" borderColor=#cccccc align='center' height='10px' style="" style="table-layout:fixed">
                <td width='50px'></td>
                <td width='150px' class='break'>Version Number</td>
                <td width='150px'>Version Name</td>
                <td width='150px'>Size</td>
                <td width='150px'>Release Notes</td>
                <td width='150px'>Publish Time</td>
                <td width='150px'>Publis Status</td>
                <td width='100px'>Delta</td>
                <td width='100px'>Edit</a></td>
                <td width='100px'>Upload Delta</td>
              </tr>
              <div>
						  <? echo $version_detail_str;?>
						 </div>
            </table>
          </td>
          <td width='7%'></td>
        </tr>
        <tr>
          <td height='0px'></td>
        </tr>
        <tr>
          <td width='4%'></td>
          <td>
            <table>
              <tr height='30px'></tr>
              <tr>
                <td width='150px'><input type="button" value="Upload New Version"
                  style="WIDTH: 155px; HEIGHT: 30px; background-color: #C4D1EE; color: #292929; font-family: Arial; font-weight: bold;"
                  onclick="upload()" /></td>
                <td width='20px'></td>
                <td width='150px'><input type="submit" value="Delete Version"
                  style="WIDTH: 135px; HEIGHT: 30px; background-color: #C4D1EE; color: #292929; font-family: Arial; font-weight: bold;"></td>
              </tr>
            </table>
          </td>
        </tr>
        <tr>
          <td height="20px"></td>
        </tr>
        <tr>
          <td width='4%'></td>
          <td width='100%'>
            <div>	 	        
	 	        <? echo $page_str;?>
	         </div>
          </td>
        </tr>
      </table>
    </form>
	
 <?
      }
   }
} 
?>


	<?
	 require_once('footer.php');
	?>	
	</div>
</body>
</html>
<script language="javascript">
	function upload()
	{
		window.location ="upload.php" ;
	}
</script>
