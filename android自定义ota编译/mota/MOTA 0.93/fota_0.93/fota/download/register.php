<?PHP
require_once ('config.php');
require_once ('fotalog.php');
if ($_SERVER ['SERVER_PORT'] != 443) {
    echo $illegal_json;
} else {
    // 取出手机注册信息
    // 现在已imei号作为手机唯一的标识
    $imei = strtolower ( urldecode ( $_POST ['imei'] ) );
    if (! isset ( $imei ) || strlen ( $imei ) <= 0) {
        echo $imei_lost_json;
    } else {
        $sn = strtolower ( urldecode ( $_POST ['sn'] ) );
        $sim = strtolower ( urldecode ( $_POST ['sim'] ) );
        $oem = strtolower ( urldecode ( $_POST ['oem'] ) );
        $product = strtolower ( urldecode ( $_POST ['product'] ) );
        $region = strtolower ( urldecode ( $_POST ['region'] ) );
        $operator = strtolower ( urldecode ( $_POST ['operator'] ) );
        $is_test = strtolower ( urldecode ( $_POST ['is_test'] ) );
        $tb = connect_database ();
        if (! isset ( $tb )) {
            echo $sn_error_json;
        } else {
            // 查看该device是否已经注册过
            // database operation 1
            $sql1 = "select imei from device where imei=\"" . $imei . "\"";
            $query1 = mysql_query ( $sql1 );
            if (mysql_affected_rows () < 1) {
                // 该device没有注册，插入数据库
                // database operation 2
                $sql2 = "insert into device(imei,sn,sim,oem,product,region,operator,istest_device) values (\"" . $imei . "\",\"" . $sn . "\",\"" . $sim . "\",\"" . $oem . "\",\"" . $product . "\",\"" . $region . "\",\"" . $operator . "\"," . $is_test . ")";
                mysql_query ( $sql2 );
            } else {
                // 该device已经注册，更新其信息
                $sql2 = 'update device set sn="' . $sn . '",sim="' . $sim . '",oem="' . $oem . '",product="' . $product . '",region="' . $region . '",operator="' . $operator . '",istest_device=' . $is_test . ' where imei="' . $imei . '"';
                mysql_query ( $sql2 );
            }
            echo $register_sucess_json;
        }
    }
}
?>