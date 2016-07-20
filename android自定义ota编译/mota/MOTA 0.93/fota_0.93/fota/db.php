<?PHP
$host = "localhost:3306";
$user = "root";
$password="1234";
$databasename = "fota";
$baseurl = "http://172.27.153.128/fota/";
$basesslurl = "http://172.27.153.128/fota/";
// $baseurl="http://127.0.0.1/";
// $basesslurl="https://127.0.0.1/";
// $upload_version_dir='/var/www/OTA/';
// $upload_delta_dir='/var/www/OTA/';
$upload_version_dir="D:/01-Projects/MOTA_Server/var/www/OTA/version/";
$upload_delta_dir="D:/01-Projects/MOTA_Server/var/www/OTA/version/";

function connect_database() {
    global $host, $user, $password, $databasename;
    $db = mysql_connect ( $host, $user, $password );
    $tb = mysql_select_db ( $databasename );
    if ($tb) {
        return true;
    } else {
        return false;
    }
}
?>
