 <?
if ($_SERVER ['REQUEST_METHOD'] == "POST") {
    if (is_uploaded_file ( $_FILES ['file'] ['tmp_name'] )) {
        $upload_dir = 'C:\\fota\\version\\';
        if (move_uploaded_file ( $_FILES ['file'] ['tmp_name'], $upload_dir . "test.zip" )) {
            // echo "parent.UP.stop(true);";
        }
    }
} elseif (! empty ( $_GET ['ID'] )) {
    header ( "Expires: Mon, 26 Jul 1997 05:00:00 GMT" );
    header ( "Last-Modified:" . gmdate ( "D, d M Y H:i:s" ) . " GMT" );
    header ( "Cache-Control: no-store, no-cache, must-revalidate" );
    header ( "Cache-Control: post-check=0, pre-check=0", false );
    header ( "Pragma: no-cache" );
    header ( "Content-Type:text/html;charset=UTF-8" );
    $unique_id = $_GET ['ID'];
    $uploadvalues = uploadprogress_get_info ( $unique_id );
    if (is_array ( $uploadvalues )) {
        echo json_encode ( $uploadvalues );
    } else {
        // ?????¡ã??-?¡ä|m??-
    }
}
?>