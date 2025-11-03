<?php
session_start();
session_unset();
session_destroy();
header("Location: /GiftIQ/index.php");
exit;
