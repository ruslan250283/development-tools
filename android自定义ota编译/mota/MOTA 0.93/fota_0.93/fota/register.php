<?
session_start ();
$username = $_SESSION ['username'];
$passsword = $_SESSION ['password'];
if (! isset ( $username ) || ! isset ( $passsword ) || strlen ( $username ) <= 0 || strlen ( $passsword ) <= 0) {
    header ( "Location: userlogin.php?access=400" );
} else {
    ?>
<html>
<head>
<link type="text/css" rel="StyleSheet" href="css/css.css" />
<link type="text/css" rel="StyleSheet" href="css/navg.css" />
</head>
<body>
    <?
    require_once ('header.php');
    require_once ("fotalog.php");
    require_once ('db.php');
    $db = connect_database ();
    if (! $db) {
        echo "<table align='center'><tr><td height='10px'></td></tr><tr><td><font color='#FF0000'><b>Database Error! Please try again!</b></font></td></tr></table>";
    } else {
        
        @ $register = $_GET ['register'];
        if (isset ( $register ) && $register == 1) {
            $number = $_POST ['imeisn'];
            if (! isset ( $number ) || strlen ( $number ) <= 0) {
                echo "<table align='center'><tr><td height='10px'></td></tr><tr><td><font color='#FF0000'><b>Error! You must enter the number you want to register!</b></font></td></tr></table>";
            } else {
                /*
                 * $sle=''; if($type==1) { $sle=' imei="'.$number.'"'; } else { $sle=' sn="'.$number.'"'; }
                 */
                $sle = ' imei="' . $number . '"';
                // database operation 1
                $sql1 = 'select istest_device from device where ' . $sle;
                $query1 = mysql_query ( $sql1 );
                if (mysql_affected_rows () == 0) {
                    // database operation 2
                    $sql2 = 'insert into device (imei,istest_device) values ("' . $number . '",1)';
                    $query2 = mysql_query ( $sql2 );
                    if (mysql_affected_rows () > 0) {
                        echo "<table align='center'><tr><td height='10px'></td></tr><tr><td><font color='#16BD2A'><b>Your number has been Added sucessfully!</b></font></td></tr></table>";
                    } else {
                        echo "<table align='center'><tr><td height='10px'></td></tr><tr><td><font color='#FF0000'><b>Error! Your number can not be added sucessfully!</b></font></td></tr></table>";
                    }
                } else {
                    while ( $result1 = mysql_fetch_array ( $query1 ) ) {
                        $istest_device = $result1 ['istest_device'];
                        if ($istest_device == 0) {
                            // database operation 3
                            $sql3 = 'update device set istest_device=1 where ' . $sle;
                            $query3 = mysql_query ( $sql3 );
                            if (mysql_affected_rows () > 0) {
                                echo "<table align='center'><tr><td height='10px'></td></tr><tr><td><font color='#FF0000'><b>Your number has been registered sucessfully!</b></font></td></tr></table>";
                            } else {
                                echo "<table align='center'><tr><td height='10px'></td></tr><tr><td><font color='#FF0000'><b>Error! Your number can not be registered sucessfully!</b></font></td></tr></table>";
                            }
                        } else {
                            echo "<table align='center'><tr><td height='10px'></td></tr><tr><td><font color='#FF0000'><b>Your number is registered as internal device before!</b></font></td></tr></table>";
                        }
                    }
                }
            }
        }
        
        @ $delete = $_GET ['delete'];
        if (isset ( $delete )) {
            if (! strcmp ( $delete, "~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~goodbye~~~~~~~~~~~~~~~~all~~~~~~~~~~~~~~~~~~~~" )) {
                $delSql = "DELETE FROM device";
            } else {
                $delSql = "DELETE FROM device WHERE imei=\"" . $delete . "\"";
            }
            if (mysql_query ( $delSql )) {
                echo "<table align='center'><tr><td height='10px'></td></tr><tr><td><font color='#16BD2A'><b>Delete sucessfully!</b></font></td></tr></table>";
            } else {
                echo "<table align='center'><tr><td height='10px'></td></tr><tr><td><font color='#FF0000'><b>Error! Delete failed!</b></font></td></tr></table>";
            }
        }
        
        for($i = 100; $i < 1000; $i ++) {
            // mysql_query('insert into device (imei,istest_device) values ("'.$i.'",1)');
        }
        $input_str = "Please enter the IMEI number you want to register:";
        $input_text = '';
        for($i = 0; $i < 1; $i ++) {
            $input_text .= "<tr><td height='40px'><input type='text' name='imeisn' style='width:500px;height:30px;'></tr>";
        }
        $input_text .= "";
        $exist_devices = "";
        $sqlExist = "select imei from device where istest_device=1";
        $queryExist = mysql_query ( $sqlExist );
        $empty = (mysql_num_rows ( $queryExist ) == 0);
        ?>
<form action="register.php?register=1" method='post'>
    <table width="50%" align='center'>
      <tr height='5px'></tr>
      <tr>
        <td><font class='fon2'><b><? echo $input_str;?></b></font> <br /> <br />
          <div class='outdivborder' id='outset' style="height: 118px;">
            <div class='divborder' id='innerset' style="height: 110px;">
              <table align='center'>
                <tr height='15px'></tr>
                <tr>
                  <td width='100px'></td>
                  <td><? echo $input_text;?></td>
                </tr>
              </table>
              <table align='center'>
                <tr align='center'>
                  <td  align='center'><input align='center' type='submit' value='Register' style='background-color: #3B5998; color: #FFFFFF; font-weight: bold;'></td>
                </tr>
              </table>
            </div>
          </div></td>
      </tr>
    </table>
  </form>
  <form action="register.php?delete=~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~goodbye~~~~~~~~~~~~~~~~all~~~~~~~~~~~~~~~~~~~~" method='post'>
    <table width="63%" align='center'>
      <tr>
        <td><font class='fon2'><b>Aleardy exist IMEI:</b></font>
        
        <td width="55%" align="left"><?php echo $empty?"<!--":""?><input type='submit' value='Delete All'
          style='background-color: #3B5998; color: #FFFFFF; font-weight: bold;'><?php echo $empty?"No Record":""?></td>
        </td>
      </tr>
      <tr>
        <table width="63%" align='center'>
          <tr>
			<?php
        $count = 0;
        while ( $exist_result = mysql_fetch_array ( $queryExist ) ) {
            $imei_exist = $exist_result ['imei'];
            $prefix = ($count ++ % 10 == 0) ? "<tr>" : "";
            $lastfix = ($count ++ % 10 == 0) ? "</tr>" : "";
            $exist_html .= $prefix . "<td><input type='text'readonly='true' value='$imei_exist'><a href='register.php?delete=$imei_exist' ><img src='pic/dele.png' value='$imei_exist' /></a></td>".$lastfix;
			}
			$count = 0;
			echo $exist_html;
			?>
			</tr>
        </table>
      </tr>
    </table>
  </form>
<?require_once('footer.php');?>
</body>
</html>
<?
    }
}
?>



