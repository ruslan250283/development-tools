<?php
session_start ();
require_once ("fotalog.php");
$username = $_SESSION ['username'];
$password = $_SESSION ['password'];
// $oem_sele = $_REQUEST ['oem'];
$oem_sele = isset($_REQUEST ['oem']) ? $_REQUEST ['oem'] : '';
// $product_sele = $_REQUEST ['product'];
$product_sele = isset($_REQUEST ['product']) ? $_REQUEST ['product'] : '';
// $region_sele = $_REQUEST ['region'];
$region_sele = isset($_REQUEST ['region']) ? $_REQUEST ['region'] : '';
// $operator_sele = $_REQUEST ['operator'];
$operator_sele = isset($_REQUEST ['operator']) ? $_REQUEST ['operator'] : '';

@ $page = $_GET ['page'];
if (! isset ( $page )) {
    $page = 0;
}
$num = 10;
$start = $page * $num;

if (isset ( $oem_sele ) && strlen ( $oem_sele ) > 0 && strcmp ( $oem_sele, 'ALL' ) != 0) {
    if (isset ( $sle ) && strlen ( $sle ) > 0)
        $sle .= " and ";
	@ $sle .= 'oem="' . $oem_sele . '"';
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
?>
<html>
<head>
<link type="text/css" rel="StyleSheet" href="css/css.css" />
<link type="text/css" rel="StyleSheet" href="css/navg.css" />
</head>
  <?
  
if (! isset ( $username ) || ! isset ( $password ) || strlen ( $username ) <= 0 || strlen ( $password ) <= 0) {
    echo "<script>location.href='userlogin.php?access=400';</script>";
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
                $item_arr = explode ( "||||", $item );
                $del_id = $item_arr [0];
                $del_name = $item_arr [1];
                $del_number = $item_arr [2];
                $del_sle .= ' version_id="' . $del_id . '" and version_name="' . $del_name . '"and version="' . $del_number . '" ';
                $version_idSql = 'select version_id,version from version_detail where version_id="' . $del_id . '"';
                $version_id_query = mysql_query ( $version_idSql );
                if (mysql_affected_rows () > 0) {
                    $version_id_result = mysql_fetch_array ( $version_id_query );
                    $version_id = $version_id_result ['version_id'];
                    $version_number = $version_id_result ['version'];
                    $del_delta .= ' version_id=' . $del_id . ' and (delta_version="' . $version_number . '" or old_version="' . $version_number . '")';
                }
                $i ++;
            }
            // get the file path, and delete the file
            // database operation 6
            $sql6 = 'select version_path from version_detail where ' . $del_sle;
            $query6 = mysql_query ( $sql6 );
            while ( $result6 = mysql_fetch_array ( $query6 ) ) {
                if (file_exists ( $result6 ['version_path'] )) {
                    $ret = unlink ( $result6 ['version_path'] );
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
                    $ret = unlink ( $result7 ['delta_path'] );
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
            mysql_query ( $sql4 );
            // $sql5='delete from delta where delta_id in (select delta_id from version_detail where '.$del_delta.' )';
            $sql5 = 'delete from delta where ' . $del_delta;
            mysql_query ( $sql5 );
            
            // echo $sql4 . "<br>";
            // echo $sql5;
        }
        
        // get versions info
        // database operaton 1
        $sql1 = 'select oem,product,region,operator from version';
        $query1 = mysql_query ( $sql1 );
        if (mysql_affected_rows () <= 0) {
            echo "<table align='center'><tr><td height='10px'></td></tr><tr><td><font color='#FF0000'>There is not any version now, You can <a href='upload.php'>click here</a> to upload some versions now</font></td></tr></table>";
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
            // $sql2='select version_id,version,version_name,version_size,release_notes,version_time,publish_time,is_publish from version_detail';
            $sql2 = 'select d.*,v.product from version_detail as d,version as v where v.version_id=d.version_id';
            if (isset ( $sle ) && strlen ( $sle ) > 0) {
                $sql2 = 'select version_detail.version_id,version_detail.version,version_detail.version_name,version_detail.version_size,version_detail.release_notes,version_detail.version_time,version_detail.publish_time,version_detail.is_publish,version_detail.delta_id ,version.product from version_detail,version where ' . $sle . ' and version_detail.version_id=version.version_id';
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
                
                $version_detail_str .= '<td><Input type="checkbox" name="vid[]" value="' . $result2 ['version_id'] . '||||' . $result2 ['version_name'] . '||||' . $result2 ['version'] . '"></td>';
                $version_detail_str .= '<td ><div class="break" >' . $result2 ['version'] . '</div></td>';
                $version_detail_str .= '<td >' . $result2 ['version_name'] . '</td>'; //
                $version_size = round ( $result2 ['version_size'] / (1024 * 1024), 2 );
                $version_detail_str .= '<td >' . $version_size . 'M</td>';
                $version_detail_str .= '<td  align="left"><div class="break">' . $result2 ['release_notes'] . '<br></td>';
                $version_detail_str .= '<td >' . date ( "Y-m-d G:i:s", $result2 ['publish_time'] ) . '</td>';
                $status = '';
                if ($result2 ['is_publish'] == 0) {
                    $status = '<img src="pic/no_publish.png" height="15px" width="15px" title="No-Publish">';
                } else if ($result2 ['is_publish'] == 1) {
                    $status = '<img src="pic/internal_publish.png" height="15px" width="15px" title="Test-Publish">';
                } else if ($result2 ['is_publish'] == 2) {
                    $status = '<img src="pic/publish.png" height="15px" width="15px" title="Publish">';
                }
                $version_detail_str .= '<td >' . $status . '</td>';
                $version_detail_str .= '<td ><a href="delta.php?versionId=' . $result2 ['version_id'] . '&version=' . $result2 ['version'] . '">delta info</a></td>';
                $version_detail_str .= '<td><a href="edit.php?version=' . $result2 ["version"] . '&versionId=' . $result2 ['version_id'] . '">edit</a></td>';
                $version_detail_str .= '<td ><a href="uploaddelta.php?version=' . $result2 ["version"] . '&versionId=' . $result2 ['version_id'] . '&versiontime=' . $result2 ['version_time'] . '">upload</a></td>';
                $version_detail_str .= '<td><a href="download/testcheckversion.php?&version=' . $result2 ["version"] . '&versionId=' . $result2 ['version_id'] . '&versiontime=' . $result2 ['version_time'] . '">Pre-check</a></td>';
                $version_detail_str .= '</tr>';
            }
            for($i = 0; $i < $page_nums; $i ++) {
                if ($page == $i)
                   @ $page_str .= '<a style="color:#FF0000;font-weight:bold;font-family:Times New Roman;font-size:16px;">' . ($i + 1) . '</a>&nbsp;&nbsp;&nbsp;';
                else
                    @ $page_str .= '<a href="manage.php?page=' . $i . '" style="color:#3B5998;font-weight:bold;font-family:Times New Roman;font-size:16px;">' . ($i + 1) . '</a>&nbsp;&nbsp;&nbsp;';
            }
            ?>


<body>
  <br>
  <div style="width: 100%;">
    <form action='manage.php' method="post">
      <table style="width: 90%;" align='center'>
        <tr>
          <td class='fon7'>OEM</td>
          <td><Select style="WIDTH: 111px; height: 25px;" name="oem">
              <?  echo $oem_str;?>
		  	      </Select></td>
          <td class='fon7'>Product</td>
          <td><Select style="WIDTH: 111px; height: 25px;" name="product">
            <? echo $product_str;?>
				     </Select></td>
          <td class='fon7'>Language</td>
          <td><Select style="WIDTH: 111px; height: 25px;" name="region">
             <?  echo $region_str;?>
				     </Select></td>
          <td class='fon7'>Operator</td>
          <td><Select style="WIDTH: 111px; height: 25px;" name="operator">
             <?  echo $operator_str;?>
				    </Select></td>
          <td><input type="submit" value="Search"
            style="WIDTH: 70px; HEIGHT: 25px; font-weight: bold;"></td>
        </tr>
      </table>
    </form>
    <!-------->
  </div>
    <form action='manage.php' method='post'>
    
      <div align="left" style="WIDTH: 100%;padding-left: 5%;padding-bottom: 1%">
        <input type="button" value="Upload New Version" style="WIDTH: 155px; HEIGHT: 30px; font-weight: bold;" onclick="upload()" /> 
        <input type="submit" value="Delete Version" style="WIDTH: 135px; HEIGHT: 30px; font-weight: bold;">
      </div>
      <div align="center" style="height: 55%;">
          <table>
            <tr>
              <th>
              </td>
              <th class='break'>Version Number
              </td>
              <th>Version Name
              </td>
              <th>Size
              </td>
              <th>Release Notes
              </td>
              <th>Publish Time
              </td>
              <th>Publis Status
              </td>
              <th>Delta
              </td>
              <th>Edit</a>
              </td>
              <th>Upload Delta
              </td>
              <th>Check Version
              </td>
            </tr>
            <? echo $version_detail_str;?>
          </table>
            
      </div>
  </form>
 <?
        }
    }
}
?>
<div align="center"><br><? echo $page_str;?><br></div>
</div>
<?require_once('footer.php');?>
</body>

<style type="text/css">
body {
	margin: 0;
	padding: 0;
	font: 12px/15px "Helvetica Neue", Arial, Helvetica, sans-serif;
	color: #555;
	background: #f5f5f5 url(bg.jpg);
}

a {
	color: #666;
}

#content {
	width: 65%;
	max-width: 690px;
	margin: 6% auto 0;
}

/*
	Pretty Table Styling
	CSS Tricks also has a nice writeup: http://css-tricks.com/feature-table-design/
	*/
table {
	overflow: hidden;
	border: 1px solid #d3d3d3;
	background: #fefefe;
	width: 90%;
	margin: 0% auto 0;
	-moz-border-radius: 5px; /* FF1+ */
	-webkit-border-radius: 5px; /* Saf3-4 */
	border-radius: 5px;
	-moz-box-shadow: 0 0 4px rgba(0, 0, 0, 0.2);
	-webkit-box-shadow: 0 0 4px rgba(0, 0, 0, 0.2);
}

td {
	padding: 2px;
	text-align: center;
}

th {
	padding: 5px;
	text-shadow: 1px 1px 1px #fff;
	background: #e8eaeb;
}

td {
	border-top: 1px solid #e0e0e0;
	border-right: 1px solid #e0e0e0;
}

tr.odd-row td {
	background: #f6f6f6;
}

td.last {
	border-right: none;
}

/*
	Background gradients are completely unnecessary but a neat effect.
	*/
td {
	background: -moz-linear-gradient(100% 25% 90deg, #fefefe, #f9f9f9);
	background: -webkit-gradient(linear, 0% 0%, 0% 25%, from(#f9f9f9),
		to(#fefefe));
}

tr.odd-row td {
	background: -moz-linear-gradient(100% 25% 90deg, #f6f6f6, #f1f1f1);
	background: -webkit-gradient(linear, 0% 0%, 0% 25%, from(#f1f1f1),
		to(#f6f6f6));
}

/* 	th { */
/* 		background: -moz-linear-gradient(100% 20% 90deg, #e8eaeb, #ededed); */
/* 		background: -webkit-gradient(linear, 0% 0%, 0% 20%, from(#ededed), to(#e8eaeb)); */
/* 	} */

/*
	I know this is annoying, but we need additional styling so webkit will recognize rounded corners on background elements.
	Nice write up of this issue: http://www.onenaught.com/posts/266/css-inner-elements-breaking-border-radius
	
	And, since we've applied the background colors to td/th element because of IE, Gecko browsers also need it.
	*/
tr:first-child th.first {
	-moz-border-radius-topleft: 5px;
	-webkit-border-top-left-radius: 5px; /* Saf3-4 */
}

tr:first-child th.last {
	-moz-border-radius-topright: 5px;
	-webkit-border-top-right-radius: 5px; /* Saf3-4 */
}

tr:last-child td.first {
	-moz-border-radius-bottomleft: 5px;
	-webkit-border-bottom-left-radius: 5px; /* Saf3-4 */
}

tr:last-child td.last {
	-moz-border-radius-bottomright: 5px;
	-webkit-border-bottom-right-radius: 5px; /* Saf3-4 */
}
</style>
<script language="javascript">
	function upload()
	{
		window.location ="upload.php" ;
	}
</script>


</html>
