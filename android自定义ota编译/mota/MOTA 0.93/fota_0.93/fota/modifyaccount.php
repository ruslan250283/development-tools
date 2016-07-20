<?
if ($_SERVER ['REQUEST_METHOD'] == "POST") {
    session_start ();
    $username = $_SESSION ['username'];
    $passsword = $_SESSION ['password'];
    if (! isset ( $username ) || ! isset ( $passsword ) || strlen ( $username ) <= 0 || strlen ( $passsword ) <= 0) {
        echo "you must login first!";
    } else {
        require_once ("fotalog.php");
        require_once ('db.php');
        $db = connect_database ();
        if (! $db) {
            echo "DataBase Error! Please try again!";
        } else {
            $is_authen = $_POST ['is_authen'];
            if (isset ( $is_authen ) && $is_authen >= 0) {
                // update the auth info
                // database operation 1
                $sql1 = 'update auth set is_authen=' . $is_authen;
                mysql_query ( $sql1 );
                echo "save the auth info sucess";
            }
            $myname = $_POST ['myname'];
            $mypass = $_POST ['mypass'];
            if (isset ( $myname ) && isset ( $mypass ) && strlen ( $myname ) > 0 && strlen ( $mypass ) > 0) {
                // update my account
                // database operation 2
                $sql2 = 'update user set username="' . $myname . '" , password="' . $mypass . '" where username="' . $username . '" and password="' . $passsword . '"';
                $query2 = mysql_query ( $sql2 );
                $error = mysql_errno ();
                if ($error == 0) {
                    $_SESSION ["username"] = $myname;
                    $_SESSION ["password"] = $mypass;
                    echo "you account has been modify sucessfully!";
                } else {
                    echo mysql_error ();
                }
            }
            
            $user = $_POST ['user'];
            $pass = $_POST ['pass'];
            if (isset ( $user ) && strlen ( $user ) > 0 && isset ( $pass ) && strlen ( $pass ) > 0) {
                $upload = $_POST ['upload'];
                if (! isset ( $upload )) {
                    $upload = 0;
                }
                $update = $_POST ['up'];
                if (! isset ( $update )) {
                    $update = 0;
                }
                $delete = $_POST ['del'];
                if (! isset ( $delete )) {
                    $delete = 0;
                }
                // add the account
                // database operation 3
                $sql3 = 'insert into user (username,password,upload,edit,del) values ("' . $user . '","' . $pass . '",' . $upload . ',' . $update . ',' . $delete . ')';
                $query3 = mysql_query ( $sql3 );
                if (mysql_errno () == 0) {
                    echo "the account has been add sucessfully!";
                } else {
                    echo mysql_error ();
                }
            }
            
            $delaccount = $_POST ['delaccount'];
            if (isset ( $delaccount ) && $delaccount == 1) {
                $deluser = $_POST ['deluser'];
                $delpass = $_POST ['delpass'];
                // delete the account
                // database operation 4
                $sql4 = 'delete from user where username="' . $deluser . '" and password="' . $delpass . '"';
                $query4 = mysql_query ( $sql4 );
                if (mysql_errno () == 0) {
                    echo "the account has been delete sucessfully!";
                } else {
                    echo mysql_error ();
                }
            }
        }
    }
}
?>