<?
session_start ();
$username = $_SESSION ['username'];
$password = $_SESSION ['password'];
if (! isset ( $username ) || ! isset ( $password ) || strlen ( $username ) <= 0 || strlen ( $password ) <= 0) {
    header ( "Location: userlogin.php?access=400" );
} else {
    require_once ("fotalog.php");
    require_once ('db.php');
    require_once ('header.php');
    $db = connect_database ();
    if (! $db) {
        echo "<table align='center'><tr><td height='10px'></td></tr><tr><td><font color='#FF0000'>Database Error! Please try again!</font></td></tr></table>";
    } else {
        $version = urldecode ( $_GET ['version'] );
        $versionId = urldecode ( $_GET ['versionId'] );
        $edit = $_GET ['edit'];
        if ($edit == 2) {
            $eversion_name = $_POST ['version_name'];
            $eversion_notes = $_POST ['releasenotes'];
            $version_publish = $_POST ['publish'];
            $fingerprint_info = $_POST ['fingerprint'];
            $androidversion_info = $_POST ['androidversion'];
            $scattermd5_info = $_POST ['scatter'];
            $publish_time = - 1;
            if (isset ( $_POST ['publish'] ) && strlen ( $_POST ['publish'] ) > 0) {
                $publish = $_POST ['publish'];
                // insert into the version_detail info
                // database operation 3
                $time_str = $_POST ['date1'];
                $time_array = explode ( "-", $time_str );
                $hour = $_POST ['time'];
                if (sizeof ( $time_array ) == 3) {
                    $publish_time = mktime ( $hour, 0, 0, $time_array [1], $time_array [2], $time_array [0] );
                }
            }
            if ($publish_time < 0) {
                echo "<table align='center'><tr><td height='10px'></td></tr><tr><td><font color='#FF0000'><b>Update Fail! You should select the publish time!</b></font></td></tr></table>";
            } else {
                $sqlPre = 'select version,version_name,version_size,version_compress,release_notes,version_time,is_publish,publish_time,fingerprint,android_version from version_detail where version="' . $version . '" and version_id=' . $versionId;
                $queryPre = mysql_query ( $sqlPre );
                if (mysql_affected_rows () > 0) {
                    $result1 = mysql_fetch_array ( $queryPre );
                    $version_old = $result1 ['version'];
                    $version_name_old = $result1 ['version_name'];
                    $version_size_old = $result1 ['version_size'];
                    $version_compress_old = $result1 ['version_compress'];
                    $release_notes_old = $result1 ['release_notes'];
                    $release_time_old = $result1 ['version_time'];
                    $is_publish_old = $result1 ['is_publish'];
                    $publish_time_old = date ( "Y-m-d", $result1 ['publish_time'] );
                    $publish_hour_old = date ( "G", $result1 ['publish_time'] );
                    $fingerprint_old = $result1 ['fingerprint'];
                    $androidversion_old = $result1 ['android_version'];
                }
                $sql3 = 'update version_detail set version_name="' . $eversion_name . '",release_notes="' . $eversion_notes . '",is_publish=' . $version_publish . ',publish_time=' . $publish_time . ',fingerprint="' . $fingerprint_info . '",android_version="' . $androidversion_info . '",scattermd5="' . $scattermd5_info . '" where version="' . $version . '" and version_id=' . $versionId;
//                 echo $sql3;
                // update the version info
                // database operation 3
                // $sql3='update version_detail set version_name="'.$eversion_name.'",release_notes="'.$eversion_notes.'",is_publish='.$version_publish.' where version="'.$version.'"';
                $query3 = mysql_query ( $sql3 );
                if (mysql_affected_rows () <= 0 && mysql_errno () != 0) {
                    echo "<table align='center'><tr><td height='10px'></td></tr><tr><td><font color='#FF0000'><b>Error! Please try again!</b></font></td></tr></table>";
                } else {
                    echo "<table align='center'><tr><td height='10px'></td></tr><tr><td><font color='#FF0000'><b>Update Sucess!</b></font></td></tr></table>";
                    
                    if ($version_publish != $is_publish_old) {
                        info ( $eversion_name . "  Publish statue from " . $is_publish_old . "   to   " . $version_publish );
                    }
                    if ($release_notes_old != $eversion_notes) {
                        info ( $eversion_name . "  release_notes from " . $release_notes_old . "   to   " . $eversion_notes );
                    }
                }
            }
        }
        
        if (! isset ( $version ) || strlen ( $version ) <= 0 || ! isset ( $versionId ) || $versionId <= 0) {
            echo "<table align='center'><tr><td height='10px'></td></tr><tr><td><font color='#FF0000'>Error! The version does not exist,Please try again!</font></td></tr></table>";
        } else {
            // get the detail info of the version
            // database operation 1
            $sql1 = 'select version,version_name,version_size,version_compress,release_notes,version_time,is_publish,publish_time,fingerprint,android_version,scattermd5 from version_detail where version="' . $version . '" and version_id=' . $versionId;
            $query1 = mysql_query ( $sql1 );
            if (mysql_affected_rows () <= 0) {
                echo "<table align='center'><tr><td height='10px'></td></tr><tr><td><font color='#FF0000'>Error! The version does not exist,Please try again!</font></td></tr></table>";
            } else {
                $result1 = mysql_fetch_array ( $query1 );
                $version = $result1 ['version'];
                $version_name = $result1 ['version_name'];
                $version_size = $result1 ['version_size'];
                $version_compress = $result1 ['version_compress'];
                $release_notes = $result1 ['release_notes'];
                $release_time = $result1 ['version_time'];
                $is_publish = $result1 ['is_publish'];
                $publish_time = date ( "Y-m-d", $result1 ['publish_time'] );
                $publish_hour = date ( "G", $result1 ['publish_time'] );
                $fingerprint = $result1 ['fingerprint'];
                $upgradetype = $result1 ['scattermd5'];
                $androidversion = $result1 ['android_version'];
                
                $publish_str = '';
                if ($is_publish == 0) {
                    $publish_str .= '<option value ="0" selected="selected">un-publish</option>';
                    $publish_str .= '<option value ="1" >interal-publish</option>';
                    $publish_str .= '<option value="2" >publish</option>';
                } elseif ($is_publish == 1) {
                    $publish_str .= '<option value ="0" >un-publish</option>';
                    $publish_str .= '<option value ="1" selected="selected">interal-publish</option>';
                    $publish_str .= '<option value="2" >publish</option>';
                } else {
                    $publish_str .= '<option value ="0" >un-publish</option>';
                    $publish_str .= '<option value ="1" >interal-publish</option>';
                    $publish_str .= '<option value="2" selected="selected">publish</option>';
                }
                // get the delta info of the version
                // database operation 2
                $has_delta = false;
                $sql2 = 'select delta_id,delta_name,delta_size,delta_compress,delta_path from delta where old_version="' . $version . '"';
                $query2 = mysql_query ( $sql2 );
                if (mysql_affected_rows () > 0) {
                    $has_delta = true;
                    $delta_str = '';
                    $i = 1;
                    while ( $result2 = mysql_fetch_array ( $query2 ) ) {
                        $delta_str .= '<tr>';
                        $delta_str .= '<td><input type= "hidden" name="deltaId' . $i . '"   value="' . $result2 ['delta_id'] . ' "   style="display:none;" /> <input type="text" style="width:100px;height:25px; " value="' . $result2 ['delta_name'] . '" readonly="read-only"></td>';
                        $delta_str .= '<td><input type="text" name="deltaname' . $i . '" style="width:100px;height:25px; " value="' . $result2 ['delta_size'] . '" readonly="read-only"></td>';
                        $delta_str .= '<td><input type="text" name="compress' . $i . '" style="width:150px;height:25px; " value="' . $result2 ['delta_compress'] . '" readonly="read-only"></td>';
                        $delta_str .= '<td ><input type="file" name="file' . $i . '"value="Upload!" style="height:25px;"  ></td>';
                        $i ++;
                    }
                }
                
                $time_str = '';
                for($i = 0; $i < 24; $i ++) {
                    
                    if ($i <= 12) {
                        $show = $i;
                        $tag = " am";
                    } else {
                        $show = ($i - 12);
                        $tag = " pm";
                    }
                    if ($show < 10)
                        $show = "0" . $show;
                    if ($publish_hour == $i) {
                        $time_str .= '<option value ="' . $i . '" selected>' . $show . $tag . '</option>';
                    } else {
                        $time_str .= '<option value ="' . $i . '">' . $show . $tag . '</option>';
                    }
                }
                ?>
<html>
<head>
<link type="text/css" rel="StyleSheet" href="css/css.css" />
<link type="text/css" rel="StyleSheet" href="css/navg.css" />
<script type="text/javascript" src="js/mootools.js"></script>
<script type="text/javascript" src="js/datePicker/WdatePicker.js"></script>
<script type="text/javascript">		
		window.addEvent('domready', function() { 
			myCal1 = new Calendar({ date1: 'd/m/Y' }, { direction: 0, tweak: {x: 6, y: 0} });
		});
	</script>
<link rel="stylesheet" type="text/css" href="css1/calendar.css" media="screen"/
	





</head>
<body>
  <br>
  <div>
    <form action='edit.php?edit=2&version=<? echo $version;?>&versionId=<? echo $versionId;?>' method='post'>
      <table>
        <tr>
          <td width='45%'></td>
          <td height='30px'></td>
        </tr>
        <tr>
          <td width='45%'></td>
          <td align='cneter'>
            <table cellpadding="0" cellspacing="0">
              <tr height='30px'>
                <td width='50px'></td>
                <td width='50px' bgcolor="#EBF3FF"></td>
                <td width='50px' bgcolor="#EBF3FF"></td>
              </tr>
              <tr>
                <td width='150px' class='fon2'>Version Number</td>
                <td width='50px' bgcolor="#EBF3FF"></td>
                <td bgcolor="#EBF3FF" width='330px'><input name='version_number' type="text" style="width: 250px; height: 25px;"
                  value='<?echo $version;?>' id='version' onchange="change('version','<? echo $version;?>')" readonly='read-only'></td>
              </tr>
              <tr>
                <td width='150px' class='fon2'>Version Name</td>
                <td width='50px' bgcolor="#EBF3FF"></td>
                <td bgcolor="#EBF3FF" width='330px'><input name='version_name' type="text" style="width: 250px; height: 25px;"
                  value='<? echo $version_name;?>' id='name' onchange="change('name','<? echo $version_name;?>')"></td>
              </tr>
              <tr>
                <td width='150px' class='fon2'>Version Size</td>
                <td width='50px' bgcolor="#EBF3FF"></td>
                <td bgcolor="#EBF3FF" width='330px'><input type="text" style="width: 250px; height: 25px;" value='<? echo $version_size;?>'
                  readonly='read-only'></td>
              </tr>
              <tr>
                <td width='150px' class='fon2'>Version Compress Rate</td>
                <td width='50px' bgcolor="#EBF3FF"></td>
                <td bgcolor="#EBF3FF" width='330px'><input type="text" style="width: 250px; height: 25px;" value='<? echo $version_compress;?>'
                  readonly='read-only'></td>
              </tr>
              <tr>
                <td width='150px' class='fon2'>Release Notes</td>
                <td width='50px' bgcolor="#EBF3FF"></td>
                <td bgcolor="#EBF3FF" width='330px'><textarea cols="33" rows="7" name="releasenotes" id="release notes"
                    onchange="change('release notes','<? echo $release_notes;?>')"><? echo $release_notes;?></textarea></td>
              </tr>
              <tr>
                <td width='150px' class='fon2'>Android Version</td>
                <td width='50px' bgcolor="#EBF3FF"></td>
                <td bgcolor="#EBF3FF" width='330px'><textarea cols="33" rows="3" name="androidversion" id="android version"
                    onchange="change('android version','<? echo $androidversion;?>')"><? echo $androidversion;?></textarea></td>
              </tr>
              <tr>
                <td width='150px' class='fon2'>Fingerprint</td>
                <td width='50px' bgcolor="#EBF3FF"></td>
                <td bgcolor="#EBF3FF" width='330px'><textarea cols="33" rows="3" name="fingerprint" id="finger print"
                    onchange="change('finger print','<? echo $fingerprint;?>')"><? echo $fingerprint;?></textarea></td>
              </tr>
              <tr>
                <td width='150px' class='fon2'>Upgrade Type</td>
                <td width='50px' bgcolor="#EBF3FF"></td>
                <td bgcolor="#EBF3FF" width='330px'>
                    <input type="radio" id="scatter1" name="scatter" value='1' <?php echo $upgradetype === "1"?'checked':''?>>
                    <font size="2sp"><label for="scatter1">Delta</label></font><br> 
                    <input type="radio" id="scatter2" name="scatter" value='2' <?php echo $upgradetype === "2"?'checked':''?>>
                    <font size="2sp"><label for="scatter2">Full</label></font><br> 
                </td>
              </tr>
              <tr>
                <td width='150px' class='fon2'>Release Time</td>
                <td width='50px' bgcolor="#EBF3FF"></td>
                <td bgcolor="#EBF3FF" width='330px'><input type="text" style="width: 250px; height: 25px;"
                  value='<? echo date("Y-m-d G:i:s",$release_time);?>' readonly='read-only'></td>
              </tr>
              <tr>
                <td width='150px' class='fon2'>Publish Status</td>
                <td width='50px' bgcolor="#EBF3FF"></td>
                <td bgcolor="#EBF3FF" width='330px'><select style="WIDTH: 200px" name='publish' id="publish">
                      <? echo $publish_str;?>
                    </select></td>
              </tr>
              <tr>
                <td width='150px' class='fon2'><div id="timetext">Publish Time</div></td>
                <td width='50px' bgcolor="#EBF3FF"></td>
                <td bgcolor="#EBF3FF" width='330px'>
                  <div id="timesle">
                    Date:<input readOnly="true" id="date1" name="date1" type="text" onClick="WdatePicker()" value="<? echo $publish_time;?>"
                      style="width: 150px;" /> &nbsp;&nbsp;&nbsp; Time:<select name="time" id="time">
  					         <? echo $time_str;?>
  					      </select>
                  </div>
                </td>
              </tr>
              <tr height='30px'>
                <td width='50px'></td>
                <td width='50px' bgcolor="#EBF3FF"></td>
                <td width='50px' bgcolor="#EBF3FF"></td>
              </tr>
            </table>
          </td>
        </tr>
      </table>
      <table align='center'>
        <tr align='center'>
          <td width='31%'></td>
          <td><input type='submit' value='Submit' style="WIDTH: 85px; HEIGHT: 30px; background-color: #3B5998; color: #FFFFFF; font-family: Arial;"></td>
        </tr>
      </table>
<?
            }
        }
    }
}
?>
			<?
require_once ('footer.php');
?>
	   </form>
  </div>
</body>
</html>
<script language="javascript">
	 function pub()
   {   
   	 var pub_str=document.getElementById("publish").value;
   	 if(pub_str==2)
   	 {
   	 	document.getElementById('timetext').style.display='none';
   	 	document.getElementById('timetext').style.visibility='hidden';
   	 	document.getElementById('timesle').style.display='none';
   	 	document.getElementById('timesle').style.visibility='hidden';
   	 }
   	 else
   	 	{
   	 	  document.getElementById('timetext').style.display='block';
   	 	  document.getElementById('timetext').style.visibility='visible';	
   	 	  document.getElementById('timesle').style.display='block';
   	 	  document.getElementById('timesle').style.visibility='visible';	
   	 	}
   	 
   }
   
	function change(tarid,oldcontent)
	{
		var content=document.getElementById(tarid).value;		
		if(content==null||content.length<=0)
		{
			alert("the "+tarid+" can not be null");
			document.getElementById(tarid).value=oldcontent;
		}
	}
</script>
