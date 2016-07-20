<?php
session_start ();
require_once ('config.php');
$token = "this is a test token";
session_register ( "token" );
$_SESSION ["token"] = $token;
session_register ( "istest_device" );
$_SESSION ["istest_device"] = 1;
$oem = "";
$product = "";
$version = "";
$language = "";
$oper = "";
$request = "";
if (isset ( $_REQUEST ["versionId"] ) && isset ( $_REQUEST ["version"] )) {
    $tb = connect_database ();
    if (! isset ( $tb )) {
        echo $tb_error_json;
    } else {
        $version_sql = "select * from version where version_id=" . $_REQUEST ["versionId"];
        $query_precheck = mysql_query ( $version_sql );
        if (mysql_affected_rows () < 1) {
            echo "Can't find your version. Are you sure it's exist now ?";
            mysql_free_result ( $query_precheck );
            return;
        } else {
            $pre_result = mysql_fetch_array ( $query_precheck );
            $oem = $pre_result ['oem'];
            $product = $pre_result ['product'];
            $language = $pre_result ['region'];
            $oper = $pre_result ['operator'];
            $version = $_REQUEST ["version"];
            $request = $oem . "_" . $product . "_" . $language . "_" . $version . "_" . $oper;
        }
    }
}
?>
<html>
<head>
<link type="text/css" rel="StyleSheet" href="../css/css.css" />
<link type="text/css" rel="StyleSheet" href="../css/navg.css" />
</head>
<body>
  <div id="header"></div>
  <div class="navg">
    <div class="mainNavg">
      <ul>
        <li id="dataNavg" width='100px'></li>
        <li id="dataNavg" width='100px'></li>
        <li id="dataNavg"><a href="../manage.php">Version Control</a></li>
        <li id="teachNavg"><a href="../upload.php">Version Upload</a></li>
        <li id="teachNavg"><a href="../register.php">Internal Register</a></li>
        <li id="systemNavg"><a href="../account.php">Accounts & Setting</a></li>
      </ul>
    </div>
  </div>
  <div width='100%' style="position: relative; left: 20px;">
    <br> <img src='../pic/logo.png' width="100px" height='30px'> <a href='../userlogin.php'
      style="position: relative; left: 79%; text-decoration: none"><font size='2' color="#3B5998"><b>Logout</b></font></a>
  </div>
  <script type="text/javascript">
String.prototype.trim=function(){return this.replace(/(^s*)ws(\s*$)/g,"");}
function fillversion(){
	var oem = document.getElementsByName("oem")[0].value;
	var product = document.getElementsByName("product")[0].value;
	var language = document.getElementsByName("language")[0].value;
	var operator = document.getElementsByName("operator")[0].value;
	var ver = document.getElementsByName("ver")[0].value;
	if(oem.trim()==""||product.trim()==""||language.trim()==""||operator.trim()==""||ver.trim()==""){
		alert("sorry!you must input all the blanks!");
		return false;
	}
	
	var version=oem.replace(/_/g,"$")+"_"+product.replace(/_/g,"$") +"_"+language.replace(/_/g,"$")+"_"+ ver.replace(/_/g,"$")+"_"+ operator.replace(/_/g,"$");
	document.getElementsByName("version")[0].value = version;
	
}
</script>
  <br>
  <br>
  <strong style="color: red"><center>YOUR DEVICE INFOMATION:</center></strong>
  <br>
  <br>
  <table align="center">
    <tr>
      <td>
        <table border="1">
          <tr>
            <td>OEM:</td>
            <td><input type='text' name="oem" value="<?php echo $oem?>"></td>
          </tr>
          <tr>
            <td>PRODUCT:</td>
            <td><input type='text' name="product" value="<?php echo $product?>"></td>
          </tr>
          <tr>
            <td>LANGUAGE:</td>
            <td><input type='text' name="language" value="<?php echo $language?>"></td>
          </tr>
          <tr>
            <td>OPERATOR:</td>
            <td><input type='text' name="operator" value="<?php echo $oper?>"></td>
          </tr>
          <tr>
            <td>VERSION:</td>
            <td><input type='text' name="ver" value="<?php echo $version?>"></td>
          </tr>
          </td>
        </table>
      </td>
    </tr>
    <tr>
    </tr>
  </table>
  <form onsubmit="return fillversion();" action='checkversion.php' method="POST">
    <table align="center">
      <td>
        <table>
          <tr>
            <td><input type='hidden' name="token" value="<?php echo $token; ?>"></td>
          </tr>
          <tr>
            <td><input width="520px" type='hidden' name="version" value=""></td>
          </tr>
          <tr>
            <td><input style='width: "200px"; height: "40px"; color: "red"; font: 16px' type='submit' value='Check Version Now !' name="submit"></td>
          </tr>
        </table>
      </td>
    </table>
  </form>
	<?
	   require_once('../footer.php');
	  ?>
</body>
</html>
