<?php
session_start();
session_unset();
session_destroy();
header("Location: /GiftIQ-main/index.php");
exit;
