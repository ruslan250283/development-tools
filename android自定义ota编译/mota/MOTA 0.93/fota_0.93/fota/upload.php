<?PHP
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

session_start ();
$username = $_SESSION ['username'];
$password = $_SESSION ['password'];
?>
<html>
<head>
<link type="text/css" rel="StyleSheet" href="css/css.css" />
<link type="text/css" rel="StyleSheet" href="css/navg.css" />
<script type="text/javascript" src="js/mootools.js"></script>
<script type="text/javascript" src="js/datePicker/WdatePicker.js"></script>
<script type="text/javascript">		
		window.addEvent('domready', function() { 
			myCal1 = new Calendar({ date1: 'Y-m-d' }, { direction: 0, tweak: {x: 6, y: 0} });
		});
	</script>
<!--<link rel="stylesheet" type="text/css" href="css1/iframe.css" media="screen" />-->
<link rel="stylesheet" type="text/css" href="css1/calendar.css" media="screen" />
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
        stop: function(files) {
           if (files==0) {
                writeStatus('you did not select any file',3);
                              
           } else if(files==1){
               writeStatus('The file was too large (post_max_size: <?php echo ini_get('post_max_size');?>)',3);
           }
           else if(files==2)
           	{
           		var secs = (new Date() - startTime)/1000; 
           		var statusText = "Upload succeeded, it took " + secs + " seconds. <br/> "; 
           		writeStatus(statusText,1);
           	}
           	else if(files==3)
           		{
           			writeStatus('upload error',3);
           		}
           startTime = null;
        },
        requestInfo: function() {
                ifr.src="uploadresult.php?ID=<?php echo $id;?>&" + new Date();
            },

            updateInfo : function(uploaded, total, estimatedSeconds) {
                if (startTime) {
                    if (uploaded) {
                        infoUpdated++;
                        if (total > upload_max_filesize) {
                            writeStatus(
                                    "The file is too large and won't be available for PHP after the upload<br/> You file size is "
                                            + total + " bytes. Allowed is " + upload_max_filesize
                                            + " bytes. That's "
                                            + Math.round(total / upload_max_filesize * 100)
                                            + "% too large<br/> Download started since "
                                            + (new Date() - startTime) / 1000 + " seconds. "
                                            + Math.floor(uploaded / total * 100) + "% done, "
                                            + estimatedSeconds + "  seconds to go", 2);
                        } else {
                            writeStatus("Download started since " + (new Date() - startTime) / 1000
                                    + " seconds. " + Math.floor(uploaded / total * 100)
                                    + "% done, " + estimatedSeconds + "  seconds to go");
                        }
                    } else {
                        writeStatus("Download started since " + (new Date() - startTime) / 1000
                                + " seconds. No progress info yet");
                    }
                    window.setTimeout("UP.requestInfo()", 1000);
                }
            }
        }

    }()
</script>
</head>
<body>
  <?
require_once ('header.php');
?>
  <?

if (! isset ( $username ) || ! isset ( $password ) || strlen ( $username ) <= 0 || strlen ( $password ) <= 0) {
    header ( "Location: userlogin.php?access=400" );
} else {
    require_once ("fotalog.php");
    require_once ('db.php');
    $db = connect_database ();
    if (! $db) {
        echo "<table align='center'><tr><td height='10px'></td></tr><tr><td><font color='#FF0000'>Database Error! Please try again!</font></td></tr></table>";
    } else {
        $id = md5 ( microtime () . rand () );
        // get versions info
        // database operaton 1
        $sql1 = 'select oem,product,region,operator from version';
        $query1 = mysql_query ( $sql1 );
        // if(mysql_affected_rows()<=0)
        // {
        // echo "<table align='center'><tr><td height='10px'></td></tr><tr><td><font color='#FF0000'>There is not any version now, You can <a href=''>click here</a> to upload some versions now</font></td></tr></table>";
        // }
        // else
        // {
        $oem_num = 0;
        $product_num = 0;
        $region_num = 0;
        $operator_num = 0;
        $oem_array = array ();
        $product_array = array ();
        $region_array = array ();
        $operator_array = array ();
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
            $oem_str .= '<option value ="' . $oem_array [$i] . '">' . $oem_array [$i] . '</option>';
        }
        asort ( $product_array );
        $product_str = '';
        for($i = 0; $i < sizeof ( $product_array ); $i ++) {
            $product_str .= '<option value ="' . $product_array [$i] . '">' . $product_array [$i] . '</option>';
        }
        asort ( $region_array );
        $region_str = '';
        for($i = 0; $i < sizeof ( $region_array ); $i ++) {
            $region_str .= '<option value ="' . $region_array [$i] . '">' . $region_array [$i] . '</option>';
        }
        asort ( $operator_array );
        $operator_str = '';
        for($i = 0; $i < sizeof ( $operator_array ); $i ++) {
            $operator_str .= '<option value ="' . $operator_array [$i] . '">' . $operator_array [$i] . '</option>';
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
            $time_str .= '<option value ="' . $i . '">' . $show . $tag . '</option>';
        }
        
        ?>
  <br>
  <form onsubmit="UP.start();" target='ifr2' action="uploadresult.php" enctype="multipart/form-data" method='post'>
    <input type="hidden" name="UPLOAD_IDENTIFIER" value="<?php echo $id;?>" />
    <!----------------------table1-------->
    <table width='100%' cellpadding="0" cellspacing="0" border="0">
      <tr>
        <td height='30px'></td>
        <td></td>
        <td></td>
        <td></td>
      </tr>
      <tr>
        <td width='25%'></td>
        <td bgcolor="#FFFFFF">
          <!----------------------table2-------->
          <div class='out'>
            <div class='inner'>
              <table width='100%' cellpadding="0" cellspacing="0">
                <tr height='20px'>
                  <td width='20%'></td>
                  <td width='70%'></td>
                  <td width='10%'></td>
                </tr>
                <tr>
                  <td width='20%'></td>
                  <td>
                    <!----------------------table3-------->
                    <table cellpadding="0" cellspacing="0">
                      <tr>
                        <font color="#3B5998"><b>Please select the build type: </b></font>
                      </tr>
                      <br>
                      <br>
                      <tr>
                        <td></td>
                        <td height='20px' bgcolor="#EBF3FF"></td>
                      </tr>
                      <tr>
                        <td width='110px' align='left' class='fon8'>OEM <font color="#FF000000"><b>* </b></font>:
                        </td>
                        <td bgcolor="#EBF3FF" width='300px'>&nbsp;&nbsp;&nbsp; <input type="radio" name="oem" value='1' checked='true'> <select
                          onchange="selectChange(this)" style="WIDTH: 150px; height: 25px;" name='oem_sle' id="oem_sle">
                            <? echo $oem_str;?>
                        </select> <br> &nbsp;&nbsp;&nbsp; <input type="radio" name="oem" value='2'><font size='2'>specify your own value</font><br>
                          &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input onfocus="selectChange(this)" type="text"
                          style="height: 25px; width: 150px;" name="oem_text" id="oem_text"></td>
                      </tr>
                      <tr>
                        <td height='10px'></td>
                        <td height='10px' bgcolor="#EBF3FF"></td>
                      </tr>
                      <tr>
                        <td width='110px' align='left' class='fon8'>PRODUCT <font color="#FF000000"><b>* </b></font>:
                        </td>
                        <td bgcolor="#EBF3FF" width='300px'>&nbsp;&nbsp;&nbsp; <input type="radio" name="product" value='1' checked='true'>
                          <select onchange="selectChange(this)" style="WIDTH: 150px; height: 25px" name="product_sle" id="product_sle">
                            <? echo $product_str;?>
                        </select> <br> &nbsp;&nbsp;&nbsp; <input type="radio" name="product" value='2'><font size='2'>specify your own
                            value</font><br> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input onfocus="selectChange(this)" type="text"
                          style="height: 25px; width: 150px;" name="product_text" id="product_text"></td>
                      </tr>
                      <tr>
                        <td></td>
                        <td height='10px' bgcolor="#EBF3FF"></td>
                      </tr>
                      <tr>
                        <td align='left' class='fon8'>LANGUAGE <font color="#FF000000"><b>* </b></font>:
                        </td>
                        <td bgcolor="#EBF3FF" width='300px'>&nbsp;&nbsp;&nbsp; <input type="radio" name="region" value='1' checked='true'>
                          <select onchange="selectChange(this)" style="WIDTH: 150px; height: 25px" name="region_sle" id="region_sle">
                            <? echo $region_str;?>
                        </select> <br> &nbsp;&nbsp;&nbsp; <input type="radio" name="region" value='2' style="width: 25px;"><font size='2'>specify
                            your own value</font><br> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input onfocus="selectChange(this)"
                          type="text" style="height: 25px; width: 150px;" name="region_text" id="region_text"></td>
                      </tr>
                      <tr>
                        <td></td>
                        <td height='10px' bgcolor="#EBF3FF"></td>
                      </tr>
                      <tr>
                        <td align='left' class='fon8'>OPERATOR <font color="#FF000000"><b>* </b></font>:
                        </td>
                        <td bgcolor="#EBF3FF" width='300px'>&nbsp;&nbsp;&nbsp; <input type="radio" name="operator" value='1' checked='true'>
                          <select style="WIDTH: 150px; height: 25px" name="operator_sle" id="operator_sle">
                            <? echo $operator_str;?>
                        </select> <br> &nbsp;&nbsp;&nbsp; <input type="radio" name="operator" value='2'><font size='2'>specify your own
                            value</font><br> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input onfocus="selectChange(this)" type="text"
                          style="height: 25px; width: 150px;" name="operator_text" id="operator_text"> 
                        </td>
                      </tr>
                      <tr>
                        <td align='left' class='fon8'>ANDROID_VER <font color="#FF000000"><b>* </b></font>:
                        </td>
                        <td bgcolor="#EBF3FF" width='300px'><br> <textarea style="height: 40px; width: 300px;" name="androidversion_text"
                            id="androidversion_text"></textarea><br> <font size="2sp">Input your android version number as: <b>2.3 ,
                              4.1 , 4.2.2 </b>etc.
                        </font> </td>
                      </tr>
                      <tr>
                        <td align='left' class='fon8'>FINGERPRINT <font color="#FF000000"><b>* </b></font>:
                        </td>
                        <td bgcolor="#EBF3FF" width='300px'><br> <textarea style="height: 40px; width: 300px;" name="fingerprint_text"
                            id="fingerprint_text"></textarea><br> <font size="2sp">Refer your <b>build.prop</b> file of your load (
                            /system/build.prop ) and input the value of <b>ro.build.fingerprint</b></font></td>
                      </tr>
                      
                      <tr>
                        <td align='left' class='fon8'>UPGRADE BY <font color="#FF000000"><b>* </b></font>:
                        </td>
                        <td bgcolor="#EBF3FF" width='300px'><font size="2sp"><br>
                          <input type="radio" id="scatter1" name="scatter" value='1' checked='true'><font size="2sp"><b><label
                                for="scatter1">Delta</label></b></font><br>
                          <input type="radio" id="scatter2" name="scatter" value='2'><font size="2sp"><b><label for="scatter2">Full</label></b></font>
                            <br> </font><br></td>
                      </tr>
                      <tr>
                        <td></td>
                        <td height='10px' bgcolor="#EBF3FF"></td>
                      </tr>
                    </table> <!----------------------table3-------->
                  </td>
                  <td width='10%'></td>
                </tr>
                <tr>
                  <td width='20%'></td>
                  <td>
                    <!----------------------table4-------->
                    <table class='fon8'>
                      <tr>
                        <td>Please Enter the build number of the version:</td>
                      </tr>
                      <tr>
                        <td><input type="text" name="version" style="width: 410px; height: 30px;" id="version"></td>
                      </tr>
                      <br>
                      <tr>
                        <td>Please Enter the name of the version:</td>
                      </tr>
                      <tr>
                        <td><input type="text" name="version_name" style="width: 410px; height: 30px;" id="version_name"></td>
                      </tr>
                      <br>
                      <tr>
                        <td>Please Enter your release note:</td>
                      </tr>
                      <tr>
                        <td><textarea name="notes" cols="46" rows="3" id="notes"></textarea></td>
                      </tr>
                      <br>
                      <tr>
                        <td>Please select the publish status of the version: <br> <select style="WIDTH: 200px; height: 30px;" name="publish"
                          id="publish">
                            <option value="0">no-publish</option>
                            <option value="1">test-publish</option>
                            <option value="2">publish</option>
                        </select></td>
                      </tr>
                      <tr>
                        <td>
                          <div id="datetext">Please select the publish time:</div>
                        </td>
                      </tr>
                      <tr>
                        <td>
                          <div id="datesle">
                            Date:<input readOnly="true" id="date1" name="date1" type="text" onClick="WdatePicker()" /> &nbsp;&nbsp;&nbsp; Time:<select
                              style="WIDTH: 100px;" name="time" id="time">
                              <? echo $time_str;?>
                            </select>
                          </div>
                        </td>
                      </tr>
                    </table> <!----------------------table4-------->
                  </td>
                  <td width='10%'></td>
                </tr>
                <tr>
                  <td width='20%'></td>
                  <td>
                    <!----------------------table5-------->
                    <table class='fon8'>
                      <tr>
                        <td>Please select the file you want to upload <!--</td>
  	    					          <td width='10px'></td>  	    						    					
  	    					          <td>--> <input name="upfile" id='upfile' type="file"
                          style="width: 200; border: 1 solid #9a9999; font-size: 9pt; background-color: #ffffff" size="17" onchange="check()"></td>
                      </tr>
                      <tr height='30px'>
                        <td>
                          <div id="status" style="width: 500px; height: 30px; color: #000000;"></div>
                        </td>
                      </tr>
                      <tr height='30px'>
                        <td><iframe name="ifr2" width="500px" height="30px" id="ifr2" style="display: none;" frameboder="0" scrolling="no"
                            ALIGN='CENTER' MARGINWIDTH='0' MARGINHEIGHT='0' HSPACE='0' VSPACE='0' FRAMEBORDER='0' src='wait.php'></iframe></td>
                      </tr>
                      <tr height='0px'>
                        <td><iframe name="ifr" src="uploadresult.php?ID=<?php echo $id;?>" style="display: block;" width="500px" height="10px"
                            id="ifr" frameboder="0" scrolling="no" ALIGN='CENTER' MARGINWIDTH='0' MARGINHEIGHT='0' HSPACE='0' VSPACE='0'
                            FRAMEBORDER='0'></iframe></td>
                      </tr>
                      <tr align='center'>
                        <td>
                          <!--</td>
  	    					          <td>--> <input type="submit" value="upload"
                          style="WIDTH: 85px; HEIGHT: 30px; background-color: #3B5998; color: #FFFFFF; font-family: Arial;" onclick="upload1()">
                        </td>
                      </tr>
                    </table> <!----------------------table5-------->
                  </td>
                  <td width='10%'></td>
                </tr>
                <tr>
                  <td height='20px'></td>
                </tr>
              </table>
            </div>
          </div> <!----------------------table2-------->
        </td>
        <td width='25%'></td>
        <td></td>
      </tr>
    </table>
    <!----------------------table1-------->
  </form>
  <?
        // }
    }
}
?>
  <?

require_once ('footer.php');

?>
</body>
</html>
<script language="javascript">
    function pub() {
        var pub_str = document.getElementById("publish").value;
        if (pub_str == 2) {
            document.getElementById('datetext').style.display = 'none';
            document.getElementById('datetext').style.visibility = 'hidden';
            document.getElementById('date1').style.display = 'none';
            document.getElementById('date1').style.visibility = 'hidden';
            document.getElementById('datesle').style.display = 'none';
            document.getElementById('datesle').style.visibility = 'hidden';
        } else {
            document.getElementById('datetext').style.display = 'inline';
            document.getElementById('datetext').style.visibility = 'visible';
            document.getElementById('datesle').style.display = 'inline';
            document.getElementById('datesle').style.visibility = 'visible';
            document.getElementById('date1').style.display = 'inline';
            document.getElementById('date1').style.visibility = 'visible';
        }

    }
    function upload1() {
        document.getElementById('ifr2').style.display = 'block';
        var ifr2 = document.getElementById("ifr2");
        document.getElementById("ifr2").src = "wait.php";
    }

    function check() {
        var oem = document.getElementsByName("oem");
        var oem_num = -1;
        var oem_str = null;
        if (oem != null) {
            for (var i = 0; i < oem.length; i++) {
                if (oem[i].checked) {
                    oem_num = i;
                    break;
                }
            }
            if (oem_num == 0) {
                oem_str = document.getElementById("oem_sle").value;
            } else if (oem_num == 1) {
                oem_str = document.getElementById("oem_text").value;
            }
        }
        if (oem_str == null || oem_str.length <= 0) {
            alert("The oem must no be null");
            document.getElementById("upfile").value = '';
        } else {

            var product = document.getElementsByName("product");
            var product_num = -1;
            var product_str = null;
            if (product != null) {
                for (var i = 0; i < product.length; i++) {
                    if (product[i].checked) {
                        product_num = i;
                        break;
                    }
                }
                if (product_num == 0) {
                    product_str = document.getElementById("product_sle").value;
                } else if (product_num == 1) {
                    product_str = document.getElementById("product_text").value;
                }
            }
            if (product_str == null || product_str.length <= 0) {
                alert("The product must no be null");
                document.getElementById("upfile").value = '';
            } else {
                //
                var region = document.getElementsByName("region");
                var region_num = -1;
                var region_str = null;
                if (region != null) {
                    for (var i = 0; i < region.length; i++) {
                        if (region[i].checked) {
                            region_num = i;
                            break;
                        }
                    }
                    if (region_num == 0) {
                        region_str = document.getElementById("region_sle").value;
                    } else if (product_num == 1) {
                        region_str = document.getElementById("region_text").value;
                    }
                }
                if (region_str == null || region_str.length <= 0) {
                    alert("The region must no be null");
                    document.getElementById("upfile").value = '';
                } else {
                    //
                    var operator = document.getElementsByName("operator");
                    var operator_num = -1;
                    var operator_str = null;
                    if (operator != null) {
                        for (var i = 0; i < operator.length; i++) {
                            if (operator[i].checked) {
                                operator_num = i;
                                break;
                            }
                        }
                        if (operator_num == 0) {
                            operator_str = document.getElementById("operator_sle").value;
                        } else if (operator_num == 1) {
                            operator_str = document.getElementById("operator_text").value;
                        } else if (operator_num == 2) {
                            operator_str = '';
                        }
                    }
                    if ((operator_str == null || operator_str.length <= 0) && operator_num != 2) {
                        alert("The operator must no be null");
                        document.getElementById("upfile").value = '';
                    } else {
                        var version_str = document.getElementById("version").value;
                        if (version_str == null || version_str.length <= 0) {
                            alert("The version must no be null");
                            document.getElementById("upfile").value = '';
                        } else {
                            var name_str = document.getElementById("version_name").value;
                            if (name_str == null || name_str.length <= 0) {
                                alert("The version name must no be null");
                                document.getElementById("upfile").value = '';
                            } else {
                                var notes_str = document.getElementById("notes").value;
                                if (notes_str == null || notes_str.length <= 0) {
                                    alert("The release notes  must no be null");
                                    document.getElementById("upfile").value = '';
                                } else {
                                    var publishtime_str = document.getElementById("date1").value;
                                    if (publishtime_str == null || publishtime_str.length <= 0) {
                                        alert("The publish time must no be null");
                                        document.getElementById("upfile").value = '';
                                    } else {
                                        var fingerprint_str = document
                                                .getElementById("fingerprint_text").value;
                                        if (fingerprint_str == null || fingerprint_str.length <= 0) {
                                            alert("The FINGERPRINT must no be null");
                                            document.getElementById("upfile").value = '';
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

    function upload() {
        var oem = document.getElementsByName("oem");
        var oem_num = -1;
        var oem_str = null;
        if (oem != null) {
            for (var i = 0; i < oem.length; i++) {
                if (oem[i].checked) {
                    oem_num = i;
                    break;
                }
            }
            if (oem_num == 0) {
                oem_str = document.getElementById("oem_sle").value;
            } else if (oem_num == 1) {
                oem_str = document.getElementById("oem_text").value;
            }
        }
        if (oem_str == null || oem_str.length <= 0) {
            alert("The oem must no be null");
        } else {

            var product = document.getElementsByName("product");
            var product_num = -1;
            var product_str = null;
            if (product != null) {
                for (var i = 0; i < product.length; i++) {
                    if (product[i].checked) {
                        product_num = i;
                        break;
                    }
                }
                if (product_num == 0) {
                    product_str = document.getElementById("product_sle").value;
                } else if (product_num == 1) {
                    product_str = document.getElementById("product_text").value;
                }
            }
            if (product_str == null || product_str.length <= 0) {
                alert("The product must no be null");
            } else {
                //ajax

                //upload_ajax();

                //ajax
            }

        }
    }
    var i = 0;
    var request = false;
    function upload_ajax() {
        try {
            request = new XMLHttpRequest();
        } catch (trymicrosoft) {
            try {
                request = new ActiveXObject("Msxml2.XMLHTTP");
            } catch (othermicrosoft) {
                try {
                    request = new ActiveXObject("Microsoft.XMLHTTP");
                } catch (failed) {
                    request = false;
                }
            }
        }
        if (request) {
            var param = "sucess=100";
            var url = "uploadresult.php";
            request.open("POST", url, true);
            request.onreadystatechange = uploadresponse;
            request.setRequestHeader("Content-Length", param.length);
            request.setRequestHeader("CONTENT-TYPE", "application/x-www-form-urlencoded");
            request.send(param);
        }
    }
    function uploadresponse() {
        if (request.readyState == 4) {
            if (request.status == 200) {
                var response = request.responseText;
                alert("ajax sucess" + response);
            } else {
                alert("status is " + request.status);
            }
        }
    }

    function selectChange(obj) {
        if (obj.id == "oem_text" | obj.id == "product_text" | obj.id == "operator_text"
                | obj.id == "region_text") {
            var name = (obj.id).substring(0, obj.id.length - 5);
            document.getElementsByName(name)[1].checked = 1;
        }
        if (obj.id == "oem_sle" | obj.id == "product_sle" | obj.id == "operator_sle"
                | obj.id == "region_sle") {
            var name = (obj.id).substring(0, obj.id.length - 4);
            document.getElementsByName(name)[0].checked = 1;
        }
    }
</script>
