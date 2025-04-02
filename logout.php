<?php 
if (isset($_COOKIE["email"]) || isset($_COOKIE["hashId"])) {
    setcookie("email", "", time()-3600);
    setcookie("hashId", "", time()-3600);
    header('Location: logout.php');
    exit();
}

header('Location: index.php');
    exit();
?>