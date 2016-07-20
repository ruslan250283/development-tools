<?
// session_start();
require_once ('fotalog.php');
$baseurl = "http://172.27.153.128/fota/download/";
$basesslurl = "http://172.27.153.128/fota/download/";
// $baseurl="http://127.0.0.1/download/";
// $basesslurl="https://127.0.0.1/download/";
$host = "localhost:3306";
$user = "root";
$password = "1234";
$databasename = "fota";
$server_version = "6582";

$auth_fail_code = 1001;
$auth_fail_info = "The sn number is required";
$auth_fail_json = "{\"status\":" . $auth_fail_code . ",\"info\":\"" . $auth_fail_info . "\"}";

$error_code = 1002;
$error_info = "There is a error occured";
$error_json = "{\"status\":" . $error_code . ",\"info\":\"" . $error_info . "\"}";

$sucess_code = 1000;

$key_lost_code = 1003;
$key_lost_info = "The session_key is required";
$key_lost_json = '"status":' . $key_lost_code . ',"info":"' . $key_lost_info . '"}';

$illegal_code = 1004;
$illegal_info = "This is illegal access";
$illegal_json = '{"status":' . $illegal_code . ',"info":"' . $illegal_info . '"}';

$token_lost_code = 1005;
$token_lost_info = "The token is required";
$token_lost_json = '{"status":' . $token_lost_code . ',"info":"' . $token_lost_info . '"}';

$token_invalid_code = 1006;
$token_invalid_info = "The token is invalid";
$token_invalid_json = '{"status":' . $token_invalid_code . ',"info":"' . $token_invalid_info . '"}';

$token_valid_code = 1007;
$token_valid_info = "The token is valid";
$token_valid_json = '{"status":' . $token_valid_code . ',"info":"' . $token_valid_info . '"}';

$sn_error_code = 1008;
$sn_error_info = "The sn is not exist";
$sn_error_json = '{"status":' . $sn_error_code . ',"info":"' . $sn_error_info . '"}';

$version_lost_code = 1009;
$version_lost_info = "The version is required";
$version_lost_json = '{"status":' . $version_lost_code . ',"info":"' . $version_lost_info . '"}';

$version_latest_code = 1010;
$version_latest_info = "Your version is the latest version";
$version_latest_json = '{"status":' . $version_latest_code . ',"info":"' . $version_latest_info . '"}';

$param_lost_code = 1011;

$db_error_code = 1102;
$db_error_info = "There is a error when connect the mysql server";
$db_error_json = '{"status":' . $db_error_code . ',"info":"' . $db_error_info . '"}';

$tb_error_code = 1103;
$tb_error_info = "There is a error when open the database";
$tb_error_json = '{"status":' . $tb_error_code . ',"info":"' . $tb_error_info . '"}';

$param_invalid_code = 1104;
$param_invalid_info = "The param is not legal";
$param_invalid_json = '{"status":' . $param_invalid_code . ',"info":"' . $param_invalid_info . '"}';

$version_invalid_code = 1105;
$version_invalid_info = "Your version is illeagl";
$version_invalid_json = '{"status":' . $version_invalid_code . ',"info":"' . $version_invalid_info . '"}';

$version_delete_code = 1106;
$version_delete_info = "The version is not exist";
$version_delete_json = '{"status":' . $version_delete_code . ',"info":"' . $version_delete_info . '"}';

$imei_lost_code = 1107;
$imei_lost_info = "The IMEI number is required";
$imei_lost_json = '{"status":' . $imei_lost_code . ',"info":"' . $imei_lost_info . '"}';

$register_sucess_code = 1108;
$register_sucess_info = "You have register sucessfully";
$register_sucess_json = '{"status":' . $register_sucess_code . ',"info":"' . $register_sucess_info . '"}';

$version_check_sucess = 1000;
function IsTokenInvalid($token) {
    global $token_lost_code, $illegal_code, $token_invalid_code, $token_valid_code;
    $session_token = $_SESSION ["token"];
    info ( "Server Token: " . $session_token );
    info ( "Client Token: " . $token );
    if (isset ( $session_token ) && strlen ( $session_token ) > 0) {
        if (strcmp ( $token, $session_token ) == 0) {
            // 验证成功
            return $token_valid_code;
        } else {
            // token验证不正确
            return $token_invalid_code;
        }
    } else {
        // Session中token不存在
        return $illegal_code;
    }
}
function connect_database() {
    global $host, $user, $password, $databasename;
    $db = mysql_connect ( $host, $user, $password );
    if ($db) {
        $tb = mysql_select_db ( $databasename );
        if ($tb) {
            return $tb;
        } else {
            echo $tb_error_json;
            return null;
        }
    } else {
        echo $db_error_json;
        return null;
    }
}
?>
