<?php

session_start();

include 'init.php';
?>

    <div class="container">
        <div class="row">
<?php
            // $category = isset($_GET['pageid']) && is_numeric($_GET['pageid']) ? intval($_GET['pageid']) : 0;
            if (isset($_GET['name'])) {
                $tag = $_GET['name'];
                //XSS - ADD htmlspecialchars
                echo '<h1 class="text-center">' . htmlspecialchars($tag) . '</h1>';

                // $tagItems = getAllFrom('*', '`items`', '`Item_ID`', "WHERE `tags` like '%$tag%'", ' AND `Approve` = 1'); // Show all items that belong to that specific tag
                //SQL INJECTION - USE PDO
                $stmt = $con->prepare("SELECT * FROM `items` WHERE `tags` LIKE ? AND `Approve` = 1");
                $likeTag = '%' . $tag . '%';
                $stmt->execute([$likeTag]);
                $tagItems = $stmt->fetchAll();
                // echo '<pre>', var_dump($tagItems),'</pre>';
                // exit;

                foreach ($tagItems as $item) {
                    echo '<div class="col-sm-6 col-md-3">';
                        echo '<div class="thumbnail item-box">';
                        //XSS - ADD htmlspecialchars
                        echo '<span class="price-tag">' . htmlspecialchars($item['Price']) . '</span>';
                        echo '<img class="img-responsive" src="img.jpg" alt="random image">';
                            echo '<div class="caption">';
                                //XSS - ADD htmlspecialchars + cast numeric IDs
                                echo '<h3><a href="items.php?itemid=' . (int)$item['Item_ID'] . '">' . htmlspecialchars($item['Name']) . '</a></h3>';
                                echo '<p>' . htmlspecialchars($item['Description']) . '</p>';
                                echo '<div class="date">' . htmlspecialchars($item['Add_Date']) . '</div>';
                            echo '</div>';
                        echo '</div>';
                    echo '</div>';
                }
            } else {
                echo 'You must specify Tag name';
            }
?>      
        </div>
    </div>

<?php
    // Footer
    include $tpl . 'footer.php';
?>