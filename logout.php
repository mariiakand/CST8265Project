<?php

session_start();

session_unset();
session_destroy();


//XSS - ADD htmlspecialchars
echo htmlspecialchars("You have logged out and will be redirected after 3 secondes...", ENT_QUOTES, 'UTF-8');
// header('Location: index.php');



header('REFRESH:3; URL=index.php'); // to be headed/redirected to another page after a certain time duration you exactly want    // Redirect to eCommerce\index.php
exit();
