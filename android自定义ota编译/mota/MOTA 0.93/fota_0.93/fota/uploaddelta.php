<?
session_start ();
$id = md5 ( microtime () . rand () );
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

$username = $_SESSION ['username'];
$password = $_SESSION ['password'];
if (! isset ( $username ) || ! isset ( $password ) || strlen ( $username ) <= 0 || strlen ( $password ) <= 0) {
    header ( "Location: userlogin.php?access=400" );
} else {
    
    require_once ('header.php');
    
    ?>
<html>
<head>
<link type="text/css" rel="StyleSheet" href="css/css.css" />
<link type="text/css" rel="StyleSheet" href="css/navg.css" /> 
 <?
    require_once ('db.php');
    require_once ("fotalog.php");
    $version = $_GET ['version'];
    $versionId = $_GET ['versionId'];
    $versiontime = $_GET ['versiontime'];
    if (! isset ( $versionId ) || strlen ( $versionId ) <= 0 || ! isset ( $version ) || strlen ( $version ) <= 0 || ! isset ( $versiontime ) || strlen ( $versiontime ) <= 0) {
        echo "<table align='center'><tr><td height='10px'></td></tr><tr><td><font color='#FF0000'>Error! The version file is not exist!</font></td></tr></table>";
    } else {
        $db = connect_database ();
        if (! $db) {
            echo "<table align='center'><tr><td height='10px'></td></tr><tr><td><font color='#FF0000'>Database Error! Please try again!</font></td></tr></table>";
        } else {
            // get the version info
            // database operation 1
            $sql1 = 'select oem,product,region,operator from version where version_id=' . $versionId;
            $query1 = mysql_query ( $sql1 );
            if (mysql_affected_rows () <= 0) {
                echo "<table align='center'><tr><td height='10px'></td></tr><tr><td><font color='#FF0000'>The version file is not exist!</font></td></tr></table>";
            } else {
                $result1 = mysql_fetch_array ( $query1 );
                $version_str = $result1 ['oem'] . '_' . $result1 ['product'] . '_' . $result1 ['region'] . '_' . $result1 ['operator'];
                $upload_path = "OEM_" . $result1 ['oem'] . '/PRO_' . $result1 ['product'] . '/REG_' . $result1 ['region'] . '/OPE_' . $result1 ['operator'] . '/';
                // get the delta info
                // database operation 2
                $sql2 = 'select * from (select version_name,version,version_time from version_detail  where version_detail.version_time<' . $versiontime . ' and version_id=' . $versionId . ') as t1  left join (select version_id,delta_id,delta_name,delta_size,delta_compress,delta_version,old_version from delta where delta_version="' . $version . '") as t2 on (t1.version=t2.old_version and t2.version_id=' . $versionId . ') order by version_time desc ';
                $query2 = mysql_query ( $sql2 );
                if (mysql_affected_rows () <= 0) {
                    echo "<table align='center'><tr><td height='10px'></td></tr><tr><td><font color='#FF0000'>The version has no delta to upload!</font></td></tr></table>";
                } else {
                    $deltastr = '';
                    $i = 0;
                    while ( $result2 = mysql_fetch_array ( $query2 ) ) {
                        $deltastr .= '<tr align="center">';
                        $deltastr .= "<td class='fon6'><div  class='break' style='width:200px;'>" . $result2 ['version'] . "</div></td>";
                        $deltastr .= "<td class='fon6'>" . $result2 ['delta_name'] . "</td>";
                        $deltasize = round ( $result2 ['delta_size'] / (1024 * 1024), 2 );
                        $deltastr .= "<td class='fon6'>" . $deltasize . "M</td>";
                        $deltastr .= "<td class='fon6'>" . $result2 ['delta_compress'] . "</td>";
                        $deltastr .= "<td class='fon6'><input type='hidden' name='deltaId" . $i . "' value=\"" . $result2 ['delta_id'] . "\"><input type='hidden' name='oldversion" . $i . "' value=\"" . $result2 ['version'] . "\"><input name='upfile" . $i . "' type='file'></td>";
                        $deltastr .= '</tr>';
                        $i ++;
                    }
                    ?>

    
<script type="text/javascript">
    var UP = function() {
    
    /* private variables */
    
    var ifr = null;
    
    var startTime = null;
    var upload_max_filesize = <?php echo return_bytes(ini_get('upload_max_filesize'));?>;
    
    var infoUpdated = 0;
    
    var writeStatus = function(text,color) {
        var statDiv = document.getElementById("status");
        if (color == 1 ) {
            statDiv.style.backgroundColor = "green";
        } else if (color == 2 ) {
            statDiv.style.backgroundColor = "orange";
        } else if (color == 3 ) {
            statDiv.style.backgroundColor = "red";
        } else {
            statDiv.style.backgroundColor = "white";
        }
        statDiv.innerHTML = text;
    }
    
    
    return {
        start: function() {
           ifr = document.getElementById("ifr");
           startTime = new Date();
           infoUpdated = 0;
           this.requestInfo();
        },
        stop: function(files,fname,finish) {
           if (files==0) {
                writeStatus('you did not select any file',3);
                              
           } else if(files==1){
               writeStatus('The file was too large (post_max_size: <?php echo ini_get('post_max_size');?>)',3);
           }
           else if(files==2)
           	{
           		var secs = (new Date() - startTime)/1000; 
           		var statusText = fname+"Uploaded succeeded, it took " + secs + " seconds. <br/> "; 
           		writeStatus(statusText,1);
           	}
           	else if(files==3)
           		{
           			writeStatus(fname+'upload error',3);
           		}
           		if(finish==100)
           		{
                startTime = null;
              }
        },
        requestInfo: function() {
                ifr.src="updeltaresult.php?ID=<?php echo $id;?>&"+new Date();
        },
        
        updateInfo: function(uploaded, total, estimatedSeconds) {
            if (startTime) {
                if (uploaded) {
                    infoUpdated++;
                    if (total > upload_max_filesize) {
                        writeStatus("The file is too large and won't be available for PHP after the upload<br/> You file size is " + total + " bytes. Allowed is " + upload_max_filesize + " bytes. That's " + Math.round (total / upload_max_filesize * 100) + "% too large<br/> Download started since " + (new Date() - startTime)/1000 + " seconds. " + Math.floor(uploaded / total * 100) + "% done, " + estimatedSeconds + "  seconds to go",2);
                    } else {
                        writeStatus("Download started since " + (new Date() - startTime)/1000 + " seconds. " + Math.floor(uploaded / total * 100) + "% done, " + estimatedSeconds + "  seconds to go");
                    }
                } else {
                    writeStatus("Download started since " + (new Date() - startTime)/1000 + " seconds. No progress info yet");
                }
                window.setTimeout("UP.requestInfo()",1000);
            }
        }
        
        
    }

}()
  </script>
</head>
<body>
  <br>
  <form onsubmit="UP.start();" target='ifr2'
    action="updeltaresult.php?num=<? echo $i;?>&deltaversion=<? echo $version;?>&versionId=<? echo $versionId;?>&up=<? echo $upload_path;?>"
    enctype="multipart/form-data" method='post'>
    <input type="hidden" name="UPLOAD_IDENTIFIER" value="<?php echo $id;?>" />
    <table align='center'>
      <tr>
        <td height='30px'></td>
      </tr>
      <tr>
        <td>
          <table>
            <tr>
              <td><font size='4' color="#3B5998"><b>The current build type is :</b></font><br> <font color="#FF0000" size='2'><b><? echo $version_str;?></b>
              </font></td>
            </tr>
            <tr>
              <td><font size='4' color="#3B5998"><b>The current build number is :</b></font><br>
              <font color="#FF0000" size='2'><b><? echo $version;?></b></font></td>
            </tr>
          </table>
        </td>
      </tr>
      <tr>
        <td height='30px'></td>
      </tr>
    </table>
    <table align='center'>
      <tr>
        <td>
          <table borderColor=#cccccc border="2" cellpadding="0" cellspacing="2" style="border-collapse: collapse">
            <tr class="fon1" bgcolor="#CCCCCC" borderColor=#cccccc align='center' height='25px' style="" style="table-layout:fixed">
              <td width='150px'>Version Number</td>
              <td width='150px'>delta</td>
              <td width='150px'>Size</td>
              <td width='150px'>Compress</td>
              <td width='150px'>File</td>
            </tr>
            <tr align='center'>
    					<?php
                    echo $deltastr;
                    ?>
    				</tr>
          </table>
        </td>
      </tr>
      <tr align='center'>
        <td height='80px'><input type='submit' value='upload' style='height: 25px; width: 70px;' onclick='upload1()'></td>
      </tr>
      <tr height='30px' width='100%'>
        <td>
          <div id="status" style="height: 30px; color: #000000;"></div>
        </td>
      </tr>
      <tr height='100px' width='100%'>
        <td><iframe name="ifr2" height="100px" width='100%' id="ifr2" style="display: none;" frameboder="0" scrolling="no" ALIGN='CENTER'
            MARGINWIDTH='0' MARGINHEIGHT='0' HSPACE='0' VSPACE='0' FRAMEBORDER='0'></iframe></td>
      </tr>
      <tr height='0px'>
        <td><iframe name="ifr" src="updeltaresult.php?ID=<?php echo $id;?>" style="display: none;" width="500px" height="0px" id="ifr" frameboder="0"
            scrolling="no" ALIGN='CENTER' MARGINWIDTH='0' MARGINHEIGHT='0' HSPACE='0' VSPACE='0' FRAMEBORDER='0'></iframe></td>
      </tr>
    </table>
<?
                }
            }
        }
    }
}
?>    
   <?
require_once ('footer.php');
?>
	</form>
</body>
</html>
<script language="javascript">
	function upload1()
	{
		document.getElementById('ifr2').style.display='block';			
	}
</script>
