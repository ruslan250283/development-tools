<?
require_once ('db.php');
session_start ();
unset ( $_SESSION ['username'] );
// $userName = $_POST ['username'];
$userName = isset($_POST['username']) ? $_POST['username'] : '';
// $passWord = $_POST ['password'];
$passWord = isset($_POST['password']) ? $_POST['password'] : '';
// $access = $_GET ['access'];
$access = isset($_GET['access']) ? $_GET['access'] : '';
if ($access == 400) {
    echo "<table align='center'><tr><td height='20px'></td></tr><tr><td><font color='#FF0000'><b>You must login first!</b></font></td></tr></table>";
    $access = 0;
} else if (isset ( $userName ) && isset ( $passWord ) && strlen ( $userName ) > 0 && strlen ( $passWord ) > 0) {
    $db = connect_database ();
    if (! $db) {
        echo "<table align='center'><tr><td height='10px'></td></tr><tr><td><font color='#FF0000'>Database Error! Please try again!</font></td></tr></table>";
    } else {
        // 从数据库中验证用户名和密码
        // datebase operation 1
        $sql1 = 'select username,is_admin from user where username="' . $userName . '" and password="' . $passWord . '"';
        $query1 = mysql_query ( $sql1 );
        if (mysql_affected_rows () == 1) {
            $result1 = mysql_fetch_array ( $query1 );
            $isadmin = $result1 ['is_admin'];
            // session_register ( "username" );
            // session_register ( "password" );
            // session_register ( "isadmin" );
            $_SESSION ["username"] = $userName;
            $_SESSION ["password"] = $passWord;
            $_SESSION ["isadmin"] = $isadmin;
            header ( "Location: manage.php" );
        } else {
            echo "<table align='center'><tr><td height='10px'></td></tr><tr><td><font color='#FF0000'>username and password is uncorrect! Please try again!</font></td></tr></table>";
        }
    }
}

?>
<html>
<head>
<link type="text/css" rel="StyleSheet" href="css/css.css" />
<link type="text/css" rel="StyleSheet" href="css/navg.css" />
<link type="text/css" rel="StyleSheet" href="css/change.css" />
</head>
<body style="background-color: #FFFFFF">
  <div class="change">
    <table align='center'>
      <tr>
        <td height='120px'></td>
      </tr>
      <tr>
        <td width='13%' height='120px'></td>
        <td height='120px'><img src="pic/logo.png" height='100px' width='350px'><br>
        <br></td>
      </tr>
      <tr>
        <td width='13%'></td>
        <td align='center'>
          <table align='center'>
            <form action=<?php echo $basesslurl.'userlogin.php'?> method='post'>
              <tr>
                <td>Please enter your username</td>
              </tr>
              <tr>
                <td><input type="text" name="username" value='username'
                  onfocus="if (this.value=='username') { this.value=''; this.style.color='#000000'; }"
                  onblur="if (this.value=='') { this.value='username'; this.style.color='#989797'; }" style="color: #989797" /></td>
              </tr>
              <tr>
                <td>Please enter your password</td>
              </tr>
              <tr>
                <td><input type="password" name="password" value="password"
                  onfocus="if (this.value=='password') { this.value=''; this.style.color='#000000'; }"
                  onblur="if (this.value=='') { this.value='password'; this.style.color='#989797'; }" style="color: #989797" /></td>
              </tr>
              <tr>
                <td><input type="submit" value=" Login " style="height: 25px" /></td>
              </tr>
            </form>
          </table>
        </td>
      </tr>
      <tr>
        <td height='220px'></td>
      </tr>
      <tr>
        <td width='13px'></td>
        <td class='fon' align='center'>@Copyright MediaTek Inc. All rights reserved</td>
      </tr>
    </table>
  </div>
</body>
</html>
