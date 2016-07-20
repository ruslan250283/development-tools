<?php
$time = date ( "Y_m_d_G_i_s", time () );
$error = "/var/www/fota/service/log/error.txt";
$info = "/var/www/fota/service/log/log.txt";
$info_bak = "/var/www/fota/service/log/log" . $time . ".txt";
$error_bak = "/var/www/fota/service/log/error" . $time . ".txt";
$max = 1024 * 1027;
function info($message) {
    global $info, $max, $info_bak;
    setlocale ( LC_TIME, "EST" );
    $time = date ( "Y-m-d G:i:s", time () );
    if (file_exists ( $info )) {
        $size = filesize ( $info );
        if ($size > $max) {
            $file = fopen ( $info, "a" );
            flock ( $file, 2 );
            if (copy ( $info, $info_bak )) {
                fclose ( $file );
                unlink ( $info );
            }
        }
    }
    $file = fopen ( $info, "a" );
    fwrite ( $file, $time . "   " . $message );
    fwrite ( $file, "\n" );
    fclose ( $file );
}
function error($message) {
    global $error, $max, $error_bak, $info;
    setlocale ( LC_TIME, "EST" );
    $time = date ( "Y-m-d G:i:s", time () );
    
    if (file_exists ( $error )) {
        $size = filesize ( $error );
        if ($size > $max) {
            $file = fopen ( $error, "a" );
            flock ( $file, 2 );
            if (copy ( $error, $error_bak )) {
                fclose ( $file );
                unlink ( $error );
            }
        }
    }
    $file = fopen ( $error, "a" );
    if ($file) {
        fwrite ( $file, "--------------------------------error--------------------------------\n" );
        fwrite ( $file, $time . "   " . $message );
        fwrite ( $file, "\n" );
        fwrite ( $file, "--------------------------------error--------------------------------\n" );
        fclose ( $file );
    }
}
?>
