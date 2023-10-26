<?php
session_start();

/**
 * This function makes a connection to the mysqli-server
 * 
 * @param string $servername 
 * @param string $username
 * @param string $password
 * @param string $db
 * 
 */
function connectToSql() {
    $servername = "localhost:3306";
    $username = "zb222ck";
    $password = "HN4zA6";
    $db = "zb222ck";

    $conn = new mysqli($servername, $username, $password, $db);
    return $conn;
}

function checkAuth() {
    if (!isset($_SESSION['auth'])) {
        $_SESSION['auth'] = false;
    }

    if ($_SESSION['auth'] == true) {
        echo "<a href='write.php?state=write'><div class='button' id='write'><p class='navBtn'>New post</p></div></a>

        <form action='index.php?state=logout' method='post'>
            <input id='logout' type='submit' name='logout' value='Log out'>
            <p>Signed in as " . $_SESSION['uname'] . "
        </form>";

    } else {
        echo "<div></div>
            <form action='login.php' method='post'>
                <input id='login' type='submit' name='login' value='Sign in'>
             </form>";
    }
}

function showMsg($msg) {
    echo "<div id='msg'>$msg</div>";
}

function logout() {
    if (isset($_POST['logout'])) {
        session_destroy();
        header('Location: index.php?state=logout');
    }
}

?>
    <nav>
        <img src="img/logo-book.png" alt="Logo">
        <a href="index.php"><div class="button" id="start"><p class="navBtn">Start page</p></div></a>

        <?php checkAuth() ?>

    </nav>

    <footer>
        <p>Produced by Zimone</p>
        <p>About: here you can read posts and,<br> if you are a writer, post your own texts</p>

    </footer>

