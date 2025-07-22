<?php

session_start();

$pageTitle = 'HomePage'; // Check eCommerce/includes/templates/header.php file    AND    eCommerce/includes/functions/functions.php file

include 'init.php';

?>



        <div class="container">
            <div class="row">
<?php
                // $allItems = getAllFrom('*', '`items`', 'WHERE `Approve` = 1', '', '`Item_ID`');
                $allItems = getAllFrom('*', '`items`', '`Item_ID`', 'WHERE `Approve` = 1', ''); // Show all the approved (column `Approve` = 1 ) items (`items` table)

                foreach ($allItems as $item) {
                    // Escape output to prevent XSS
                    // XSS - ADD htmlspecialchars
                    $safeItemID      = htmlspecialchars($item['Item_ID']);
                    $safeName        = htmlspecialchars($item['Name']);
                    $safeDescription = nl2br(htmlspecialchars($item['Description']));
                    $safePrice       = htmlspecialchars($item['Price']);
                    $safeDate        = htmlspecialchars($item['Add_Date']);
            
                    echo '<div class="col-sm-6 col-md-3">';
                        echo '<div class="thumbnail item-box">';
                            echo '<span class="price-tag">$' . $safePrice . '</span>';
                            echo '<img class="img-responsive" src="img.jpg" alt="random image">';
                            echo '<div class="caption">';
                                // Safe link: includes escaped ID and name
                                echo '<h3><a href="items.php?itemid=' . $safeItemID . '">' . $safeName . '</a></h3>';
                                // Description supports line breaks and is safe from XSS
                                echo '<p>' . $safeDescription . '</p>';
                                echo '<div class="date">' . $safeDate . '</div>';
                            echo '</div>';
                        echo '</div>';
                    echo '</div>';
                }
?>
            </div>
        </div>

<?php

include $tpl . 'footer.php'; 
?>