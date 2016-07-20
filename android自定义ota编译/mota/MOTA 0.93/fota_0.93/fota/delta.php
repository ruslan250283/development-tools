<?
session_start ();
$username = $_SESSION ['username'];
$password = $_SESSION ['password'];
if (! isset ( $username ) || ! isset ( $password ) || strlen ( $username ) <= 0 || strlen ( $password ) <= 0) {
    header ( "Location: userlogin.php?access=400" );
} else {
    ?>

<?PHP
    require_once ('header.php');
    require_once ('db.php');
    require_once ("fotalog.php");
    $version_id = $_GET ['versionId'];
    $version = $_GET ['version'];
    @ $delete_delta = $_GET ['deletedelta'];
    
    if (! isset ( $version_id ) || strlen ( $version_id ) <= 0 || ! isset ( $version ) || strlen ( $version ) <= 0) {
        echo "<table align='center'><tr><td height='10px'></td></tr><tr><td><font color='#FF0000'>Error! The delta file is not exist!</font></td></tr></table>";
    } else {
        $db = connect_database ();
        if (! $db) {
            echo "<table align='center'><tr><td height='10px'></td></tr><tr><td><font color='#FF0000'>Database Error! Please try again!</font></td></tr></table>";
        } else {
            if (isset ( $delete_delta ) && strlen ( $delete_delta ) > 0) {
                // get the delta file of the delta
                // database operation 4
                $sql4 = 'select delta_path from delta where delta_id=' . $delete_delta;
                $query4 = mysql_query ( $sql4 );
                if (mysql_affected_rows () >= 1) {
                    $result4 = mysql_fetch_array ( $query4 );
                    $path = $result4 ['delta_path'];
                    if (file_exists ( $path )) {
                        $ret = unlink ( $path );
                        if ($ret == 0) {
                            delete ( "<br><br><br>DELETE ERROR---------------------" );
                            delete ( "delete delta   " . $path );
                            delete ( "<br><br><br>DELETE ERROR---------------------" );
                        } else {
                            delete ( "<br><br><br>DELETE SUCESS---------------------" );
                            delete ( "delete delta   " . $path );
                            delete ( "<br><br><br>DELETE SUCESS---------------------" );
                        }
                    }
                }
                // delete the delta from database
                // database operation 3
                $sql3 = 'delete from delta where delta_id=' . $delete_delta;
                $query3 = mysql_query ( $sql3 );
                if (mysql_affected_rows () <= 0) {
                    echo "<table align='center'><tr><td height='10px'></td></tr><tr><td><font color='#FF0000'>Error! The delta file you want to delete not exist!</font></td></tr></table>";
                }
            }
            
            // get the version info of the delta
            // database operation 1
            $sql1 = 'select version.oem,version.product,version.region,version.operator,version_detail.delta_notes from version,version_detail where version.version_id=' . $version_id . ' and version_detail.version="' . $version . '" and version.version_id=version_detail.version_id';
            $query1 = mysql_query ( $sql1 );
            if (mysql_affected_rows () <= 0) {
                echo "<table align='center'><tr><td height='10px'></td></tr><tr><td><font color='#FF0000'>Error! The delta file is not exist!</font></td></tr></table>";
            } else {
                $result1 = mysql_fetch_array ( $query1 );
                $version_name = $result1 ['oem'] . '_' . $result1 ['product'] . '_' . $result1 ['region'] . '_' . $result1 ['operator'];
                $delta_notes = $result1 ['delta_notes'];
                // get the detail info of the delta
                // database operation 2
                $sql2 = 'select delta_id,delta_name,delta_size,delta_compress,download_num,download_ratio,sucess_ratio,sucess_num from delta where old_version="' . $version . '" and version_id=' . $version_id . '  order by delta_id desc';
                // echo $sql2;
                $query2 = mysql_query ( $sql2 );
                if (mysql_affected_rows () <= 0) {
                    // echo "<table align='center'><tr><td height='10px'></td></tr><tr><td><font color='#FF0000'>This version has no delta file!</font></td></tr></table>";
                } else {
                    $delta_str = '';
                    $i = 0;
                    while ( $result2 = mysql_fetch_array ( $query2 ) ) {
                        if ($i % 2 == 0) {
                            $delta_str .= '<tr height="30px" bgcolor="#E8EEF7" class="fon6">';
                        } else {
                            $delta_str .= '<tr height="30px" bgcolor="#FFFFFF" class="fon6">';
                        }
			$delta_str.='<td width="210px" >'.$result2['delta_name'].'</td>';
			$delta_size=round($result2['delta_size']/(1024*1024),2);
			$delta_str.='<td width="210px">'.$delta_size.'M</td>';
			// $delta_str.='<td width="150px" >'.$result2['delta_compress'].'</td>';
			$delta_str.='<td width="210px">'.$result2['download_num'].'</td>';
			// $delta_str.='<td width="150px">'.$result2['download_ratio'].'</td>';
			$delta_str.='<td width="210px">'.$result2['sucess_num'].'</td>	';
			$delta_str.='<td width="160px">'.$result2['sucess_ratio'].'</td>';
			$delta_str.='<td><input type="button" value="delete" style="WIDTH: 90px; HEIGHT: 27px;background-color:#C4D1EE;color:#292929;font-family:Arial;font-weight:bold;" onclick="deletedelta('.$version_id.',\''.$version.'\','.$result2['delta_id'].')"></td>';
			$delta_str.='</tr>';
			$i++;
                    }
                    ?>
 <?
                }
            }
        }
    }
}
?>
<html>
<head>
<link type="text/css" rel="StyleSheet" href="css/css.css" />
<link type="text/css" rel="StyleSheet" href="css/navg.css" />
</head>
<body>
  <br>
  <table align='center'>
    <tr>
      <td height='25px'></td>
    </tr>
    <tr>
	<td><font size='4' color="#3B5998"><b>The current build type is :</b></font><br>
	<font color="#FF0000" size='2' ><b><? echo $version_name?> </b></font></td> 
    </tr>
    <tr>
      <td><font size='4' color="#3B5998"><b>The current build number is :</b></font><br>
      <font color="#FF0000" size='2'><b><? echo $version;?></b></font></td>
    </tr>
    <tr>
      <td height='30px'></td>
    </tr>
  </table>
		<?
if (isset ( $delta_str ) && strlen ( $delta_str ) > 0) {
    ?>
		  <table width='100%'>
    <td width='15%'></td>
    <td>
      <table width='100%'>
        <font color='#FFCC00'><b>Delta Release Notes</b></font>
        <div class='outdiv'>
          <div class='innerdiv'>
            <br>		  					
                <? echo $delta_notes;?>
                <br> <br>
          </div>
        </div>
      </table>
    </td>
    <td width='15%'></td>
  </table>
  <br>
  <table>
    <tr>
      <td width='15%'></td>
      <td>
	<table  align='center' borderColor=#cccccc  border="0" cellpadding="0" cellspacing="0" style="border-collapse: collapse" >
		<tr bgcolor="#D5DFF3" class='fon1' height='30px'>
			<td width='210px' >Name </td>
			<td width='210px' >Size</td>
			<!-- <td width='150px' >Compress_rate</td> -->
			<td width='210px'>Download_Num</td>
			<!-- <td width='150px'>Download_Ratio</td> -->
			<td width='210px'>Sucess_Nums</td>		
			<td width='160px'>Sucess_Ratio</td>		
			<td></td>
		</tr>
		<?php echo $delta_str;?>
	</table>
      </td>
      <td width='15%'></td>
    </tr>
  </table>
    <?
} else {
    echo "<table align='center'><tr><td height='10px'></td></tr><tr><td><font color='#FF0000'><b>This version has no delta file!<b></font></td></tr></table>";
}
?>
		  <?
    require_once ('footer.php');
    ?>
	</body>
</html>
<script language="javascript">
	function deletedelta(versionId,version,deletedelta)
	{
		if(confirm("Do you really want to delete the file?"))
		{
			location.href='delta.php?versionId='+versionId+"&version="+version+"&deletedelta="+deletedelta;
		}
	}
</script>
