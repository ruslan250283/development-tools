<?php
date_default_timezone_set('UTC');
$time = date ( "Y_m_d_G_i_s", time () );
$error = "log/error.txt";
$info = "log/log.txt";
$info_bak = "log/log" . $time . ".txt";
$error_bak = "log/log" . $time . ".txt";
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
    global $error, $max, $error_bak;
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
        fwrite ( $file, $time . "   " . $message );
        fwrite ( $file, "\n" );
        fclose ( $file );
    }
}
?>
