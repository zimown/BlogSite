<?php 
require_once "nav.php";

function authCheck() {
    if (!$_SESSION['auth']) header('Location: index.php'); 
    if (isset($_GET['state'])) {
        switch ($_GET['state']) {
            case "deleted":
                if (isset($_GET['post'])) editPost();
                showMsg("<h3>Post deleted</h3>");
                break;
            case "posted":
                showMsg("<h3>Posted successfully!</h3>");
                break;
            case "edit":
                editPost();
                echo '<h1>Edit or delete a post by ' . $_SESSION['uname'] . '</h1>
                <h2>You can rewrite and repost, or chose to delete it</h2>';
                break;
            case "write":
                 echo '<h1>New post for ' . $_SESSION['uname'] . '</h1>
    <h2>Write and publish a new post</h2>';
        }
    } else header('Location: index.php'); 
}

function addPost() {
    $conn = connectToSql();

    if (isset($_POST['post'])) {
        if (!empty($_POST['head']) && !empty($_POST['text'])) {
            $sql = "insert into posts (heading, text, author) values ('" . $_POST['head'] . "', '" . $_POST['text'] . "', '" . $_SESSION['uname'] . "')";
            $conn->query($sql);   
            header('Location: write.php?state=posted');
            
            addToXML();
            removeXML();
        } else showMsg("<p>Don't forget that you have to fill out the form before you can post!</p>");
    }
}

function editPost() { 
    $conn = connectToSql();

    $sql = "select author from posts where postId like " . $_GET['post'] . "";
    $res = $conn->query($sql);
    $author = $res->fetch_assoc();

    if ($_SESSION['uname'] == $author['author']) {
        if (isset($_POST['edit'])) {
            if (!empty($_POST['head']) && !empty($_POST['text'])) {
                $sql = "update posts set heading='" . $_POST['head'] ."', text='" . $_POST['text'] . "' where postId like " . $_GET['post'] . "";
                $conn->query($sql);
                header('Location: post.php?post=' . $_GET['post'] . '&state=edited');
            }
        }
        if (isset($_POST['delete'])) {
            $sql = "delete from posts where postId like " . $_GET['post'] . "";
            $conn->query($sql);
            header('Location: write.php?state=deleted');
        } 
    } else {
        showMsg("<h3>You do not have the right authorization to edit this post</h3>");
        header('Location: post.php?post=' . $_GET['post'] . '');
    }
}

function addToXML() {
    $conn = connectToSql();

    $sql = "select postId, date from posts where text like '" . $_POST['text'] . "'";
    $res = $conn->query($sql);
    $r = $res->fetch_assoc();

    $dom = new DOMDocument();
    $dom->load('XML/posts.xml');
    $posts = $dom->getElementsByTagName('channel')[0];

    $post = $dom->createElement("item");
    $post->appendChild($dom->createElement("title",$_POST['head']));
    $post->appendChild($dom->createElement("description",substr($_POST['text'],0,250)));
    $post->appendChild($dom->createElement("link","http://localhost/shared/kursuppgift/post.php?post=" . $r['postId'] . ""));
    $post->appendChild($dom->createElement("pubDate",date("r")));

    $posts->appendChild($post);
    $dom->save('XML/posts.xml');
}

function removeXML() {
    $dom = new DOMDocument();
    $dom->load('XML/posts.xml');
    $list = $dom->getElementsByTagName('channel')[0];
    
    while ($dom->getElementsByTagName('item')->length > 8) {
        $list->removeChild($dom->getElementsByTagName('item')[0]);
        $dom->save('XML/posts.xml');
    }
}

function pageFunction() {
    $conn = connectToSql();

    if (isset($_GET['post'])) {
        $post = $_GET['post'];

        $sql = "select heading, text from posts where postId like $post";
        $res = $conn->query($sql);
        $edit = $res->fetch_assoc();

        echo '<a href="post.php?post=' . $_GET['post'] . '"><p id="returnBtn">Back to post</p></a>
            <form action="write.php?post=' . $post . '&state=edit" method="post">
                <label for="head">Header:</label>
                <br>
                <input type="text" name="head" value="' . $edit['heading'] . '"><br>
                <label for="text">Text:</label>
                <br>
                <textarea id="textarea" type="text" name="text">' . $edit['text'] . '</textarea>
                <br>
                <input class="btn" id="submitPost" type="submit" name="edit" value="Post edited version">
            </form>';
        echo '<form action="write.php?post=' . $post . '&state=deleted" method="post">
                    <input class="btn" id="deleteBtn" type="submit" name="delete" value="Delete post">
                </form>';


    } else {
        echo '<form action="write.php?state=write" method="post">
                <label for="head">Header:</label>
                <br>
                <input type="text" name="head"><br>
                <label for="text">Text:</label>
                <br>
                <textarea id="textarea" type="text" name="text"></textarea>
                <br>
                <input class="btn" id="submitPost" type="submit" name="post" value="Post">
            </form>';
            addPost();
    }
}


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Write a post</title>
    <link rel="stylesheet" type="text/css" href="css/style.css">
</head>
<body>
    <header>
        <?php authCheck();?>
        
    </header>

    <main>
        <section id="newPost">
            <?php pageFunction() ?>

        </section>
    </main>
    

<?php
