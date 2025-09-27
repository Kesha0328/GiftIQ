<?php
session_start();
session_unset();
session_destroy();

// redirect to homepage
header("Location: /GIFTIQ/public/index.php");
exit();
