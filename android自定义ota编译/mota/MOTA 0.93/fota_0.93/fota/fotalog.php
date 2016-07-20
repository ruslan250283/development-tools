<?php
date_default_timezone_set('UTC');
$time=date("Y_m_d_G_i_s",time());
$error="log/error.txt";
$info="log/".getUserName()."/log.txt";
$delete="log/delete.txt";
$info_bak="log/log".$time.".txt";
$error_bak="log/log".$time.".txt";
$max=1024*1027;

function getUserName() {
    if (! is_dir ( "log/" . $_SESSION ["username"] )) {
        mkdir ( "log/" . $_SESSION ["username"], 0777 );
    }
    return $_SESSION ["username"];
}
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
    fwrite ( $file, $time . "   " . "Client:" . GetIP () . "  " . $message );
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
        fwrite ( $file, $time . "   " . "Client:" . GetIP () . "  " . $message );
        fwrite ( $file, "\n" );
        fclose ( $file );
    }
}
function GetIP() {
    if (! empty ( $_SERVER ["HTTP_CLIENT_IP"] )) {
        $cip = $_SERVER ["HTTP_CLIENT_IP"];
    } elseif (! empty ( $_SERVER ["HTTP_X_FORWARDED_FOR"] )) {
        $cip = $_SERVER ["HTTP_X_FORWARDED_FOR"];
    } elseif (! empty ( $_SERVER ["REMOTE_ADDR"] )) {
        $cip = $_SERVER ["REMOTE_ADDR"];
    } else {
        $cip = "UNKOWN Client";
    }
    return $cip;
}
function delete($message) {
    global $delete, $max, $error_bak;
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
    $file = fopen ( $delete, "a" );
    if ($file) {
        fwrite ( $file, $time . "   " . "Client:" . GetIP () . "  " . $message );
        fwrite ( $file, "\n" );
        fclose ( $file );
    }
}
?>
