<?php 
require "nav.php";

function login() {
    $conn = connectToSql();

    if ($_SESSION['auth']) {
        $msg = " <h3>You are signed in!</h3>";
        showMsg($msg);
    }

    if (isset($_POST['submit'])) {
        if (!empty($_POST['uname']) && !empty($_POST['psw'])) {
            $sql = "select userId from users where psw like '" . $_POST['psw'] . "' and username like '" . $_POST['uname']. "'";
            $res = $conn->query($sql);

            if ($res->num_rows > 0) {
                
                $_SESSION['auth'] = true;
                $_SESSION['uname'] = ucfirst($_POST['uname']);
                header('Location: login.php');
            } else {
                $msg = '<h3>Enter valid username and password to sign in</h3>';
                showMsg($msg);
            }
        }
    }
}


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" type="text/css" href="css/style.css">
</head>
<body>
    <header><?php login() ?>
        <h1>Sign in</h1>
        <h2>To write and post you have to sign in as an writer</h2>
    </header>
    <main>
        <form action="login.php" method="post" id="loginForm">
            <label for="uname" id="user">User name:</label>
            <input type="text" id="uname" name="uname" required><br>
            <label for="psw" id="pass">Password:</label>
            <input type="text" id="psw" name="psw" required><br>
            <input type="submit" class="btn" name="submit" value="Log in">
        </form>
    </main>


<?php


