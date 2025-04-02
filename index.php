<?php
//update page
require_once("starter.php");

if (isset($_COOKIE["email"]) && isset($_COOKIE["hashId"])) {
    if(isset($_SESSION["destination"])){
        header('Location: /'.$_SESSION["destination"]);
        unset($_SESSION["destination"]);
    }else{
        header('Location: reminder.php');
    }
    exit();
}
?>



<!DOCTYPE html>
<html lang="cs">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="svatky.css" />
    <link rel="icon" type="image/svg+xml" href="/favicon.svg">
    <title>Reminder by Betthy</title>
    <style>

.page {
    padding-top: 8%;
}s
main h1 {
    font-size: 2.2rem;
    font-weight: bold;

    
}
main p {
    font-size: 1.2rem;
}
#name {
    font-size: 2.7rem;
}

#name span {
font-weight: normal;

}
.gradient{
    display: flex;
    flex-direction: row;
    align-items: center;
}

.header>*,
.login>* {
    margin: 0;
    padding: 0;
}

.header,
.login {
    padding: 7rem 4rem;
    display: flex;
    flex-direction: column;
    gap: 0.7rem;
}

.header p,
.header h1,
.login button, .popUp button {
    color: var(--white);
}

.login p,
.login h1 {
    color: black;
}

.login {
    background-color: var(--white);
    margin: 1rem;
    text-align: center;
    width: 60%;
    border-radius: 40px;

}

form {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.7rem;

}

.login button, .popUp button {
    padding: 0.7rem;
    border-radius: 40px;
    text-align: center;
    font-size: 1.1rem;
    background: linear-gradient(10deg, var(--primary), var(--secondary));
    border: none;
    width: 60%;
    cursor:pointer;
}
.popUp button{
    margin-top: 2rem;
}

.login input {
    padding: 0.7rem;
    border-radius: 40px;
    text-align: center;
    font-size: 1.1rem;
    border: solid 2px var(--primary);
    width: 90%;
    margin-top: 1rem;
}

.header {
    text-align: center;
    width: 40%
}

#popUpBlack .popUp {
            margin: 11.5% auto 0 auto;
            width: 700px;
            height: 45vh;
            background: white;
            border-radius: 40px;
            box-shadow: rgba(17, 17, 26, 0.1) 0px 8px 24px, rgba(17, 17, 26, 0.1) 0px 16px 56px, rgba(17, 17, 26, 0.1) 0px 24px 80px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }
        #popUpBlack {
            left: 50%;
            top: 50%;
            transform: translate(-50%, -50%);
            position: fixed;
            background: rgb(0, 0, 0, 0.7);
            width: 100%;
            height: 100%;

        }

@media screen and (max-width: 950px) {

    #popUpBlack .popUp {
        width: 60%;
        min-height: 55vh;
        margin: 14% auto 0 auto;
        padding: 1rem 2rem;
    }
    .gradient {
        flex-direction: column;
        padding: 1rem;
        width: 85%;
    }
    .login, .header {
        padding: 3rem 0;
        margin: 0;
        width: 100%;
    }

    .login input {
        width: 60%;
    }
    .login button, .popUp button {
        width: 50%;
    }

    .page  main p {
        font-size: 1.1rem;
    }
}
@media screen and (max-width: 450px) {
    .gradient {
        flex-direction: column;
    }
    #popUpBlack .popUp {
        padding: 1rem 3rem;
        width: 80%;
        margin: 19% auto 0 auto;
        box-sizing:border-box;
    }


}
        </style>
</head>

<body>
    <?php
    if(isset($_POST["emailIn"])){
        if(filter_var($_POST["emailIn"], FILTER_VALIDATE_EMAIL)){
            $email = $_POST["emailIn"];
            $logCode = generateRandomString(15);
        require_once("db.php");
        require("mail.php");
    if($con){

        if(mysqli_num_rows(mysqli_query($con, "SELECT email FROM emailAddresses WHERE email = '".$email."'"))>0){
           mysqli_query($con, "UPDATE emailAddresses SET loginId = '".$logCode."' WHERE email = '".$email."'");
        }else{
            $email = mysqli_real_escape_string($con, $email);
           mysqli_query($con, "INSERT INTO emailAddresses (email, loginId) VALUES ('".$email."', '".$logCode."')");
        }




        sendLoginMail($email, "http://reminder.betthy.cz/index.php?id=".$logCode);
        showPopUp("Zkontroluj svůj email","Odkaz pro přihlášení najdeš ve své emailové schránce", true);
}}} else {
    if(isset($_GET["id"])){
        if(isId($_GET["id"])){
        require_once("db.php");
        $user = mysqli_query($con, "SELECT email FROM emailAddresses WHERE loginId = '".$_GET["id"]."'");
        if(mysqli_num_rows($user)>0){
            $email = mysqli_fetch_array($user)["email"];
            $hashCode = generateRandomString(15);
            setcookie("email", $email, time()+ (86400*7));
            setcookie("hashId", $hashCode, time()+ (86400*7));
            if(mysqli_query($con, "UPDATE emailAddresses SET loginId = null, hashId = '".$hashCode."' WHERE email = '".$email."'")){
                if(isset($_SESSION["destination"])){
                    header('Location: /'.$_SESSION["destination"]);
                    unset($_SESSION["destination"]);
                }else{
                    header('Location: reminder.php');
                }
                exit();
            }
        }else{
            showPopUp("Neplatný odkaz","Máš správný odkaz? Možná už vypršel...", true);
        }
    }else {
        showPopUp("Něco se nepovedlo :(","Zkus to prosím znovu...", true);
        }
    
    }



}



function isId($id){
    if(strlen($id)==15){
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    for ($i = 0; $i < 15; $i++) {
        if(strpos($characters,$id[$i])=== false){
            return false;
        }
    }
    return true;


}else {
    return false;
}
}

function generateRandomString($length) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $randomString = "";
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[random_int(0, strlen($characters) - 1)];
    }
    return $randomString;
}

function showPopUp($header, $description, $closable = false){
    if($closable){
        $close = ' class="closable"';
        $button = ' <button onclick="closePopUp()" type="button">Zavřít</button>';
    }else{
        $close = "";
        $button = "";
    }
    echo '<div id="popUpBlack"'.$close.'>
<div class="popUp">
    <h1>'.$header.'</h1>
    <p>'.$description.'</p>'.$button.'
</div>
</div>';
}
?>
<script>
    function closePopUp(){
        let black = document.getElementById("popUpBlack");
        if(black.classList.contains("closable")){
        black.remove();
    }

    }
</script>
    <div class="page">
        <main>

        
            <div class="gradient">
                <div class="login">
                    <h1>Vítej!</h1>
                    <p>Začni zadáním tvého emailu.</p>
                    <form action="" method="POST">
                        <input type="email" id="emailIn" name="emailIn" placeholder="Email" required>
                        <button type="submit">Pokračovat</button>

                    </form>

                </div>

                <div class="header">
                    <h1 id="name"><span>Re</span>minder.</h1>
                    <p>Ať žádný významný den neunikne tvé pozornosti.</p>
                </div>

            </div>

        </main>
       <?php require("footer.php"); ?>
    </div>
</body>

</html>