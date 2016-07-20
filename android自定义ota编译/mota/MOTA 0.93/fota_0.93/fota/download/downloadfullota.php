<?php
session_start ();
require_once ('config.php');
require_once ('fotalog.php');
$token = urldecode ( $_POST ['token'] );

// for test
// $_POST['version'] = "alps.gb2.p73";
// $_POST['versionId'] = "132";
// $token = "test_token";
info ( "downloadfullota" . $token );
if (! isset ( $token ) || strlen ( $token ) <= 0) {
    header ( 'HTTP/1.1 401 token lost' );
} else {
    $vercode = IsTokenInvalid ( $token );
    if ($vercode == $illegal_code) {
        header ( 'HTTP/1.1 401 illegal access' );
    } else if ($vercode == $token_invalid_code) {
        header ( 'HTTP/1.1 401 token invalid' );
    } else {
        $version = urldecode ( $_POST ['version'] ); // must
        if (! isset ( $version ) || strlen ( $version ) <= 0) {
            $version = $_SESSION ['version'];
        }
        $version_id = urldecode ( $_POST ['versionId'] ); // must
        if (! isset ( $version_id ) || strlen ( $version_id ) <= 0) {
            info ( $version_id . "-----------" . $version );
            $version_id = $_SESSION ['versionId'];
            info ( $version_id . "=====s=-=====" . $version );
        }
        
        if (! isset ( $version_id ) || ! isset ( $version )) {
            header ( 'HTTP/1.1 401 illegal access' );
        } else {
            $tb = connect_database ();
            if (! isset ( $tb )) {
                header ( 'HTTP/1.1 404 Delta Not Found' );
            } else {
                
                // full ota path,size,compress rate
                // database operation 1
                // select version_path,version_size,version_compress from version_detail where version_id=132 and version="alps.gb2.p71";
                $sql1 = 'select version_path,version_size,version_compress from version_detail where version_id=' . $version_id . ' and version="' . $version . '"';
                $query1 = mysql_query ( $sql1 );
                if (mysql_affected_rows () < 1) {
                    header ( 'HTTP/1.1 404 Delta Not Found' );
                } else {
                    // full ota info
                    $result1 = mysql_fetch_array ( $query1 );
                    $filename = $result1 ['version_path'];
                    $size = $result1 ['version_size'];
                    $compress = $result1 ['version_compress'];
                    // echo $filename;
                    // download full ota
                    if (isset ( $filename ) && strlen ( $filename ) > 0) {
                        
                        if (! file_exists ( $filename )) {
                            header ( 'HTTP/1.1 404 File Not Found' );
                        } else {
                            // echo $filename;
                            $file = fopen ( $filename, "r" );
                            $filesize = filesize ( $filename );
                            info ( "size is " . $filesize );
                            // 用于断点序传
                            $start = $_POST ['HTTP_RANGE'];
                            // if (isset($_SERVER['HTTP_RANGE']) && ($_SERVER['HTTP_RANGE'] != "") && preg_match("/^bytes=([0-9]+)-$/i", //$_SERVER['HTTP_RANGE'], $match) && ($match[1] < $size)) {
                            // $start = $match[1];
                            // } else {
                            // $start = 0;
                            // }
                            info ( "start is " . $start );
                            // echo $start;
                            // echo "123";
                            // header("Content-type: application/octet-stream");
                            // header("Accept-Ranges: bytes");
                            // header("Accept-Length: ".$size);
                            
                            $blocksize = 1024;
                            if ($file) {
                                if (isset ( $start ) && $start > 0 && $start < $filesize) {
                                    info ( "file skip " . $start );
                                    fseek ( $file, $start );
                                    Header ( "HTTP/1.1 206 Partial Content" );
                                    Header ( "Content-Length: " . ($filesize - $start) );
                                    Header ( "Content-Ranges: bytes" . $start . "-" . ($filesize - 1) . "/" . $filesize );
                                } else {
                                    Header ( "Content-Length: " . $filesize );
                                    Header ( "Accept-Ranges: bytes" );
                                }
                                header ( "Cache-Control: public" );
                                header ( "Pragma: public" );
                                header ( "Content-Disposition: inline; filename=" . $filename ); // 第一个文件下载方式，直接打开（inline）还是另存为（attachment）; 第二个是文件名，自己取，注意避免特殊字符。
                                
                                while ( ! feof ( $file ) ) {
                                    echo fread ( $file, $blocksize );
                                }
                            }
                            if ($file) {
                                fclose ( $file );
                            }
                        }
                    } else {
                        header ( 'HTTP/1.1 404 File Not Found' );
                    }
                }
            }
        }
    }
}

?>
