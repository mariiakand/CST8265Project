<?php

session_start();

$pageTitle = 'Show Items'; 

include 'init.php';

//SQL INJECTION - USE is_numeric
if (!isset($_GET['itemid']) || !is_numeric($_GET['itemid'])) {
    header('Location: index.php');
    exit();
}
$itemid = intval($_GET['itemid']);

// Fetch approved item with joins using prepared statement
//SQL INJECTION - USE PDO
$stmt = $con->prepare('SELECT `items`.*, `categories`.Name AS My_Category, `users`.Username AS My_Username FROM `items`
                       INNER JOIN `categories` ON `categories`.ID = `items`.Cat_ID
                       INNER JOIN `users`      ON `users`.UserID  = `items`.Member_ID
                       WHERE `Item_ID` = ? AND `Approve` = 1
                    ');
$stmt->execute(array($itemid));

$count = $stmt->rowCount();

// If item exists, display details
if ($count > 0) {
    $item = $stmt->fetch();
    
?>
    <!-- XSS - ADD htmlspecialchars -->
    <h1 class="text-center"><?php echo htmlspecialchars ($item['Name']) ?></h1>
    <div class="container">
        <div class="row">
            <div class="col-md-3"><img class="img-responsive img-thumbnail center-block" src="img.jpg" alt="random image"></div>
            <div class="col-md-9 item-info">
                <!-- XSS - ADD htmlspecialchars -->
                <h2><?php echo htmlspecialchars($item['Name']); ?></h2>
                <!-- XSS - ADD htmlspecialchars -->              
                <p><?php echo nl2br(htmlspecialchars($item['Description'])); ?></p>
                <ul class="list-unstyled">
                    <!-- XSS - ADD htmlspecialchars TO ALL-->
                    <li><i class="fa fa-calendar fa-fw" aria-hidden="true"></i> <span>Adding Date</span>: <?php echo htmlspecialchars($item['Add_Date']); ?></li>
                    <li><i class="fa fa-money fa-fw"    aria-hidden="true"></i> <span>Price</span>: <?php echo htmlspecialchars($item['Price']); ?></li>
                    <li><i class="fa fa-building fa-fw" aria-hidden="true"></i> <span>Made In</span>: <?php echo htmlspecialchars($item['Country_Made']); ?></li>
                    <li><i class="fa fa-tags fa-fw"     aria-hidden="true"></i> <span>Category</span>: <a href="categories.php?pageid=<?php echo htmlspecialchars($item['Cat_ID']); ?>"><?php echo htmlspecialchars($item['My_Category']); ?></a></li> <!-- Using the SQL INNER JOIN statement -->
                    <li><i class="fa fa-user fa-fw"     aria-hidden="true"></i> <span>Added By</span>: <a href="#"><?php echo htmlspecialchars($item['My_Username']); ?></a></li> <!-- Using the SQL INNER JOIN statement -->
                    <li class='tags-items'><i class="fa fa-user fa-fw" aria-hidden="true"></i> <span>Tags</span>:
<?php                   
                        $allTags = explode(',', $item['tags']);

                        foreach ($allTags as $tag) {
                            $tag = str_replace(' ', '', $tag); // to be properly printed in href
                          
                            $lowertag = strtolower($tag); // to be properly printed in href

                            if (!empty($tag)) {
                                //XSS - ADD htmlspecialchars and urlencode
                                echo "<a href='tags.php?name=" . urlencode($lowertag) . "'>" . htmlspecialchars($tag) . "</a>";
                            }
                        }
?>
                    </li>
                </ul>
            </div>
        </div>
        <hr class="custom-hr">
<?php
        // Show comment form if user logged in
        if (isset($_SESSION['user'])) { // if the current user is authenticated/logged-in    // Protected Routes (Protecting Routes)
?>
            <div class="row">
                <div class="col-md-offset-3">
                    <div class="add-comment">
                        <h3>Add Your Comment</h3>
                        <form action="<?php echo $_SERVER['PHP_SELF'] . '?itemid=' . $item['Item_ID'] ?>" method="POST">
                            <textarea name="comment" required></textarea>
                            <input class="btn btn-primary" type="submit" value="Add Comment">
                        </form>
<?php
                        // Handle new comment POST
                        if ($_SERVER['REQUEST_METHOD'] == 'POST') { 
                            // $comment = filter_var($_POST['comment'], FILTER_SANITIZE_STRING);
                            $comment = htmlspecialchars($_POST['comment']); 
                
                            $itemid = $item['Item_ID'];
                            $userid = $_SESSION['uid'];

                            if (!empty($comment)) {
                                //SQL INJECTION - USE PDO
                                $stmt = $con->prepare('INSERT INTO `comments` (`comment`, `status`, `comment_date`, `item_id`, `user_id`) VALUES (:zcomment, 0, NOW(), :zitemid, :zuserid)');
                                $stmt->execute(array(
                                    'zcomment' => $comment,
                                    'zitemid'  => $itemid,
                                    'zuserid'  => $userid
                                ));

                                if ($stmt) {
                                    echo '<br><div class= "alert alert-success">Comment Added!</div>';
                                }
                            }
                        }
?>
                    </div>
                </div>
            </div>
<?php
        } else { 
            // Prompt login/register if not logged in
            echo '<a href="login.php">Login</a> or <a href="login.php">Register</a> to add comment';
        }
?>
        <hr class="custom-hr">
<?php
        // Fetch and display approved comments for this item
        //SQL INJECTION - USE PDO
        $stmt = $con->prepare('SELECT `comments`.*, `users`.`Username` AS My_user_name FROM `comments`
                               INNER JOIN `users` ON `users`.`UserID`  = `comments`.`user_id`
                               WHERE `item_id` = ? AND `status` = 1
                               ORDER BY `c_id` DESC
        ');

        $stmt->execute(array($item['Item_ID']));

        $comments = $stmt->fetchAll();

?>
        
<?php
            // Loop through and display each comment
            foreach ($comments as $comment) {
?>
                <div class="comment-box">
                    <div class="row">
                        <div class="col-sm-2 text-center">
                            <img class="img-responsive img-thumbnail img-circle center-block" src="img.jpg" alt="random image">
                            <!-- XSS - ADD htmlspecialchars -->
                            <?php echo htmlspecialchars($comment['My_user_name']); ?>                        
                        </div>
                        <div class="col-sm-10">
                            <!-- XSS - ADD htmlspecialchars -->
                            <p class="lead"><?php echo nl2br(htmlspecialchars($comment['comment'])); ?></p>                        
                        </div>
                    </div>
                </div>
                <hr class="custom-hr">
<?php
            }
?>
    </div>




<?php

} else {
    echo '<div class="alert alert-danger">There\'s no such item ID or this item is waiting for approval by admin</div>';
}

include $tpl . 'footer.php'; 

?>