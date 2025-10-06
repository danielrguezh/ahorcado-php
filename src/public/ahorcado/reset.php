<?php
/**
 * @author danielrguezh
 * @version 1.0.0
 */
session_start();
session_destroy();
header("Location: index.php");
exit;
