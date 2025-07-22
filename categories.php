<?php

session_start();

$pageTitle = 'Categories';

include 'init.php';
?>

    <div class="container">
        <h1 class="text-center">Show Category Items<?php /* echo str_replace('-', ' ', $_GET['pagename']) */ ?></h1>
        <div class="row">
<?php
            // $category = isset($_GET['pageid']) && is_numeric($_GET['pageid']) ? intval($_GET['pageid']) : 0;
            // If a category is clicked in header.php which, in turn, include-ed in index.php
            //SQL INJECTION - ensures that only a valid integer is accepted
            $category = filter_input(INPUT_GET, 'pageid', FILTER_VALIDATE_INT);
            if ($category !== false && $category !== null) {

                // $allItems = getAllFrom('*', '`items`', "WHERE `Cat_ID` = {$category}", 'AND `Approve` = 1', '`Item_ID`');
                //SQL INJECTION - USE PDO
                $stmt = $con->prepare("SELECT * FROM `items` WHERE `Cat_ID` = ? AND `Approve` = 1 ORDER BY `Item_ID`");
                $stmt->execute([$category]);
                $allItems = $stmt->fetchAll();


                foreach ($allItems as $item) {
                    echo '<div class="col-sm-6 col-md-3">';
                        echo '<div class="thumbnail item-box">';
                            //XSS - ADD htmlspecialchars
                            echo '<span class="price-tag">' . htmlspecialchars($item['Price']) . '</span>';
                            echo '<img class="img-responsive" src="img.jpg" alt="random image">';
                            echo '<div class="caption">';
                                //XSS - ADD htmlspecialchars and (int)
                                echo '<h3><a href="items.php?itemid=' . (int)$item['Item_ID'] . '">' . htmlspecialchars($item['Name']) . '</a></h3>';
                                echo '<p>' . htmlspecialchars($item['Description']) . '</p>';
                                echo '<div class="date">' . htmlspecialchars($item['Add_Date']) . '</div>';
                            echo '</div>';
                        echo '</div>';
                    echo '</div>';
                }

            } else {
                echo 'You must specify Page ID';
            }
?>      
        </div>
    </div>

<?php
include $tpl . 'footer.php'; 
?>