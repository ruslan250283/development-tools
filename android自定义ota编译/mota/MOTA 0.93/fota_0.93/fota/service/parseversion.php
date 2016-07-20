<?PHP
require_once ('log.php');
// 查看对应的文件夹下是否有需要解析的xml文件
info ( "service begin......" );
$dirPath = "/var/www/fota/service/TODO/";
$version_name = null;
$version_path = null;
$image_compress = null;
$image_size = null;
$region = null;
$oem = null;
$product = null;
$operator = null;
$releasenote = null;
$delta_name = null;
$delta_compress = null;
$delta_oldversion = null;
$delta_path = null;
$deltasiz = null;
$delta_notes = null;
// 打开数据库
$db = mysql_connect ( 'localhost:3306', 'root', 'root' );
$tb = mysql_select_db ( "fota" );
if (! $tb) {
    error ( "open the database fail" );
} else {
    $dirHandle = opendir ( $dirPath );
    while ( $file = readdir ( $dirHandle ) ) {
        // 处理一个xml文件
        if (strlen ( $file ) > 4) {
            info ( "BEGIN parse xml file, version file is " . $file );
            $sucess = true;
            $xml = new DOMDocument ();
            $xml->load ( $dirPath . $file );
            $rootDom = $xml->getElementsByTagName ( "root" );
            foreach ( $rootDom as $root ) {
                // 获取标签Node
                $build_number_array = $root->getElementsByTagName ( "buildnumber" );
                $build_number_node = $build_number_array->item ( 0 );
                if (isset ( $build_number_node )) {
                    $build_number = strtolower ( $build_number_node->nodeValue );
                }
                $version_name_array = $root->getElementsByTagName ( "versionname" );
                $version_name_node = $version_name_array->item ( 0 );
                if (isset ( $version_name_node )) {
                    $version_name = $version_name_node->nodeValue;
                }
                $version_path_array = $root->getElementsByTagName ( "versionpath" );
                $version_path_node = $version_path_array->item ( 0 );
                if (isset ( $version_path_node )) {
                    $version_path = $version_path_node->nodeValue;
                }
                $compress_array = $root->getElementsByTagName ( "imagecompress" );
                $image_compress_node = $compress_array->item ( 0 );
                if (isset ( $image_compress_node )) {
                    $image_compress = $image_compress_node->nodeValue;
                }
                
                $image_size_array = $root->getElementsByTagName ( "imagesize" );
                $image_size_node = $image_size_array->item ( 0 );
                if (isset ( $image_size_node )) {
                    $image_size = $image_size_node->nodeValue;
                }
                $region_array = $root->getElementsByTagName ( "language" );
                $region_node = $region_array->item ( 0 );
                if (isset ( $region_node )) {
                    $region = strtolower ( $region_node->nodeValue );
                }
                $oem_array = $root->getElementsByTagName ( "oem" );
                $oem_node = $oem_array->item ( 0 );
                if (isset ( $oem_node )) {
                    $oem = strtolower ( $oem_node->nodeValue );
                }
                $product_array = $root->getElementsByTagName ( "product" );
                $product_node = $product_array->item ( 0 );
                if (isset ( $product_node )) {
                    $product = strtolower ( $product_node->nodeValue );
                }
                $operator_array = $root->getElementsByTagName ( "operator" );
                $operator_node = $operator_array->item ( 0 );
                if (isset ( $operator_node )) {
                    $operator = strtolower ( $operator_node->nodeValue );
                }
                $releasenote_array = $root->getElementsByTagName ( "releasenote" );
                $releasenote_node = $releasenote_array->item ( 0 );
                if (isset ( $releasenote_node )) {
                    $releasenote = $releasenote_node->nodeValue;
                }
                if (! isset ( $build_number ) || strlen ( $build_number ) <= 0) {
                    error ( "The build number is null" );
                    $sucess = false;
                } else {
                    
                    // database operation 1
                    $version_id = 0;
                    $sql1 = 'select version_id from version where (oem="' . $oem . '" and region="' . $region . '" and product="' . $product . '" and operator="' . $operator . '")';
                    $query1 = mysql_query ( $sql1 );
                    echo $sql1 . "<br>";
                    info ( $sql1 );
                    if (mysql_affected_rows () >= 1) {
                        $result1 = mysql_fetch_array ( $query1 );
                        $version_id = $result1 ['version_id'];
                    } else {
                        // 版本信息插入数据库
                        // database operation 2
                        $sql2 = 'insert into version (oem,region,product,operator) values ("' . $oem . '","' . $region . '","' . $product . '","' . $operator . '")';
                        mysql_query ( $sql2 );
                        echo $sql2 . "<br>";
                        info ( $sql2 );
                        $version_id = mysql_insert_id ();
                    }
                    
                    if (! isset ( $version_id ) || $version_id <= 0) {
                        error ( "SQL ERROR:Can not insert version info into database. " . mysql_error ( $db ) );
                        $sucess = false;
                    } else {
                        
                        $version_time = filectime ( $dirPath . $file );
                        $update_time = time ();
                        if (! isset ( $image_size ) || strcmp ( $image_size, '' ) == 0) {
                            $image_size = 0;
                        }
                        if (! isset ( $image_compress ) || strcmp ( $image_compress, '' ) == 0) {
                            $image_compress = 0;
                        }
                        
                        // database operation 5
                        $sql5 = 'select version from version_detail where version="' . $build_number . '" and version_id=' . $version_id;
                        mysql_query ( $sql5 );
                        echo $sql5 . "<br>";
                        info ( $sql5 );
                        $affected_nums = mysql_affected_rows ();
                        if ($affected_nums <= 0) {
                            // 插入该版本的详细信息
                            // database operation 6
                            $sql6 = 'insert into version_detail (version_id,version,version_name,version_path,version_size,version_compress,release_notes,version_time,update_time) values (' . $version_id . ',"' . $build_number . '","' . $version_name . '","' . $version_path . '",' . $image_size . ',' . $image_compress . ',"' . $releasenote . '",' . $version_time . ',' . $update_time . ')';
                            $query6 = mysql_query ( $sql6 );
                            echo $sql6 . "<br>";
                            info ( $sql6 );
                            $affected_nums = mysql_affected_rows ();
                        }
                        if ($affected_nums <= 0) {
                            error ( "SQL ERROR:Can not insert version detail info into database. " . mysql_error ( $db ) );
                            $sucess = false;
                        } else {
                            // ---------------------
                            // 解析差分包信息
                            $delta = $root->getElementsByTagName ( "delta" );
                            if (! isset ( $delta ) || sizeof ( $delta ) <= 0 || ! isset ( $delta->item ( 0 )->nodeValue )) {
                                error ( "No delta info" );
                            } else {
                                $deltainfo_array = $delta->item ( 0 )->getElementsByTagName ( "deltainfo" );
                                $i = 0;
                                foreach ( $deltainfo_array as $deltainfo ) {
                                    $delta_name_node = $deltainfo->getElementsByTagName ( "name" )->item ( 0 );
                                    if (isset ( $delta_name_node )) {
                                        $delta_name = strtolower ( $delta_name_node->nodeValue );
                                    }
                                    $delta_compress_node = $deltainfo->getElementsByTagName ( "compress" )->item ( 0 );
                                    if (isset ( $delta_compress_node )) {
                                        $delta_compress = $delta_compress_node->nodeValue;
                                    }
                                    $delta_oldversion_node = $deltainfo->getElementsByTagName ( "o1dversion" )->item ( 0 );
                                    if (isset ( $delta_oldversion_node )) {
                                        $delta_oldversion = strtolower ( $delta_oldversion_node->nodeValue );
                                    }
                                    $delta_path_node = $deltainfo->getElementsByTagName ( "path" )->item ( 0 );
                                    if (isset ( $delta_path_node )) {
                                        $delta_path = $delta_path_node->nodeValue;
                                    }
                                    $deltasize_node = $deltainfo->getElementsByTagName ( "size" )->item ( 0 );
                                    if (isset ( $deltasize_node )) {
                                        $deltasize = $deltasize_node->nodeValue;
                                    }
                                    $delta_notes_node = $deltainfo->getElementsByTagName ( "deltanotes" )->item ( 0 );
                                    if (isset ( $delta_notes_node )) {
                                        $delta_notes = strtolower ( $delta_notes_node->nodeValue );
                                    }
                                    if (! isset ( $delta_compress ) || strcmp ( $deltasize, '' ) == 0) {
                                        $delta_compress = 0;
                                    }
                                    if (! isset ( $deltasize ) || strcmp ( $deltasize, '' ) == 0) {
                                        $deltasize = 0;
                                    }
                                    // 在数据库中插入差分包
                                    // database operation 3
                                    $sql3 = 'insert into delta (version_id,delta_name,delta_path,delta_size,delta_compress,delta_version,old_version,delta_notes) values (' . $version_id . ',"' . $delta_name . '","' . $delta_path . '",' . $deltasize . ',' . $delta_compress . ',"' . $build_number . '","' . $delta_oldversion . '","' . $delta_notes . '")';
                                    $query3 = mysql_query ( $sql3 );
                                    echo $sql3 . "<br>";
                                    info ( $sql3 );
                                    $delta_id = mysql_insert_id ( $db );
                                    $affected_nums = 0;
                                    if ($delta_id <= 0) {
                                        // database operation 7
                                        $sql7 = 'select delta_id from delta where delta_name="' . $delta_name . '"';
                                        $query7 = mysql_query ( $sql7 );
                                        info ( $sql7 );
                                        echo $sql7 . "<br>";
                                        if (mysql_affected_rows () >= 1) {
                                            $result7 = mysql_fetch_array ( $query7 );
                                            $delta_id = $result7 ['delta_id'];
                                        }
                                    }
                                    
                                    // --------------------------------
                                    
                                    if ($delta_id <= 0) {
                                        error ( "SQL REEOR:Can not insert delta info into database" . $sql3 );
                                        $sucess = false;
                                    } else {
                                        // 更新对应版本的差分包信息
                                        // database operation 4
                                        $sql4 = 'update version_detail set delta_id=' . $delta_id . ',delta_version="' . $build_number . '",delta_notes="' . $delta_notes . '" where version="' . $delta_oldversion . '" and version_id=' . $version_id;
                                        mysql_query ( $sql4 );
                                        echo $sql4 . "<br>";
                                        info ( $sql4 );
                                        if (mysql_affected_rows () <= 0) {
                                            error ( "SQL ERROR:" . $sql4 );
                                            $sucess = false;
                                        }
                                    }
                                }
                            }
                            // ----------------
                        }
                    }
                }
            }
            $source_file = $dirPath . $file;
            $delete_sucess = true;
            // 一个xml文件处理完毕
            if ($sucess) {
                // xml解析成功，移动到finish
                $dest_file = "/var/www/fota/service/FINISH/" . time () . $file;
                if (! copy ( $source_file, $dest_file )) {
                    error ( "Copy the version file failed:" . $file );
                    $delete_sucess = false;
                }
            } else {
                // xml解析失败，移动到error
                $dest_file = "/var/www/fota/service/ERROR/" . time () . $file;
                if (! copy ( $source_file, $dest_file )) {
                    error ( "Copy the version file failed:" . $file );
                    $delete_sucess = false;
                }
            }
            // 删除原来的文件
            if ($delete_sucess) {
                if (! unlink ( $source_file )) {
                    error ( "dlete version file failed:" . $file );
                }
            }
            info ( "End parse xml file, version file is " . $file . "\r\n" );
        }
    }
    info ( "\r\n\r\n\r\n" );
}

?>
