<?php
$updating = false;
$password = "development";
session_start();
/////////////
if($updating){
    if(!isset($_SESSION["dev"])){
if(!isset($_GET["dev"])&& !isset($_SESSION["dev"])){
    header("location: update"); 
}else if(!isset($_SESSION["dev"])) {
    if($_GET["dev"] != $password){
        header("location: update");
    }else{
        $_SESSION["dev"] = true;
    }
}
}
}
?>