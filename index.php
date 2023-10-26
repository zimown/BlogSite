<?php 
require "nav.php";

function showPosts() {
    $conn = connectToSql();
 
    $limit = 5;
    $results = "select * from posts";
    $res = $conn->query($results);
    $nr_results = mysqli_num_rows($res);
    $pages = ceil($nr_results/$limit);

    if (!isset($_GET['page'])) {
        $page = 1;
    } else {
        $page = $_GET['page'];
    }

    $offset = ($page-1)*$limit;

    $sql = "select * from posts order by date desc limit ". $offset .", ". $limit;
    $res = $conn->query($sql);

    if ($res->num_rows > 0) {
        while($row = $res->fetch_assoc()) {
            echo "<a class='postLink' href='post.php?post=" . $row['postId'] . "'>
                    <div class='post'>
                        <h2 calss='heading'>" . $row["heading"] . "</h2>
                        <p class='date'>" . $row["date"] . "</p>
                        <p class='text'>" . substr($row['text'],0,350) . "...</p>
                        <p class='author'>" . $row["author"] . "</p>
                    </div>
                 </a>";
        }
    }

    echo '<div id="pagination">';
    for ($nr = 1; $nr <= $pages; $nr++) {
        if ($nr == $page) {
            echo '<a style="background-color:rgb(215, 143, 61)" href="index.php?page=' . $nr . '">' . $nr . ' </a>';
        } else echo '<a href="index.php?page=' . $nr . '">' . $nr . ' </a>';
    }
    echo '</div>';
}

function checkState() {
    if (isset($_GET['state'])) {
        if ($_GET['state'] == 'logout') {
           logout(); 
           $msg = "<p>You have been logged out</p>";
           showMsg($msg);
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
    <title>Posts</title>
    <link rel="stylesheet" type="text/css" href="css/style.css">
</head>
<body>
    <header>
        <?php checkState() ?>
        <h1>Welcome</h1>
        <h2>Here you can see all current post, see what new ones has been posted!</h2>
    </header>

    <main>
        <section id="posts">
            <?php showPosts() ?>

        </section>
        
    </main>
    </body>
</html>

<?php


