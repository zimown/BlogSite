<?php
require "nav.php";
$post;
$author;

function showPost() {
    $conn = connectToSql();
    global $post;
    global $author;
   
    if (isset($_GET['post'])) {
        $post = $_GET['post'];
    } else header('Location: index.php');

    $sql = "select * from posts where postId like $post";
    $res = $conn->query($sql);
    
    if ($res->num_rows > 0) {
        while($row = $res->fetch_assoc()) {
            echo "<h1 class='heading'>" . $row["heading"]. "</h1>
                    <p class='date'>" . $row["date"]. "</p>
                    <p class='text'>" . $row["text"]. "</p>
                    <p class='author'>" . $row['author'] . "</p>";
                    $author = $row['author'];
        }
    } else {
        showMsg("<h3>Post was not found</h3>");
    }
}

function addComment() {
    $conn = connectToSql();

    if (isset($_POST['post'])) {
        if (!empty($_POST['signature']) && !empty($_POST['text'])) {
           $sql = "insert into comments (comment, signature, postId) values ('" . $_POST['text'] . "', '" . ucfirst($_POST['signature']) . "', " . $_GET['post'] . ")"; 
           $conn->query($sql);
        } else showMsg("<p>Write something and give your signature, then you can post the comment</p>");
    }
}

function showComments() {
    $conn = connectToSql();
    global $post;

    $sql = "select * from comments where postId like $post order by date desc";
    $res = $conn->query($sql);

    if ($res->num_rows > 0) {
        while($row = $res->fetch_assoc()) {
            echo "<div class='post'>
                    <p class='date'>" . $row["date"]. "</p>
                    <p class='text'>" . $row["comment"]. "</p>
                    <p class='author'>" . $row["signature"]. "</p>
                 </div>";
        }
    } else showMsg("<p>There is currently no comments on this post, you can be the first!</p>");
}

function checkAuthor() {
    global $post;
    global $author;

    if ($_SESSION['auth']) {
        if ($_SESSION['uname'] == $author) {
            echo "<form action='write.php?post=" . $post . "&state=edit' method='post'><input class='btn' id='editPost' type='submit' value='Edit post'></form>";
        }
    }
}

function checkState() {
    if (isset($_GET['state'])) {
        if ($_GET['state'] == 'edited') showMsg("<h3>The post was edited by " . $_SESSION['uname'] . "</h3>");

    }
}


?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chosen post</title>
    <link rel="stylesheet" type="text/css" href="css/style.css">
</head>
<body>
    <header> <?php checkState() ?>
    </header>

    <main>

        <section class="post" id="chosenPost">
            <?php showPost() ?>
            
           
            <section id="commentSection">
                <h3>Write a comment</h3> <?php checkAuthor() ?>
                <form action="post.php?post=<?php echo $post ?>" method="post"> 
                    <label for="head">Signature:</label>
                    <br>
                    <input type="text" name="signature"><br>
                    <label for="text">Comment:</label>
                    <br>
                    <textarea id="comment" type="text" name="text"></textarea>
                    <br>
                    <input class="btn" id="addComment" type="submit" name="post" value="Add comment">
                </form>
                <h2>Comments</h2>
                <?php addComment(); showComments() ?>
            </section>

        </section>
    </main>
<?php
