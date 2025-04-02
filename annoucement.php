<?php
if(isset($_GET["pass"])){
    if($_GET["pass"] != "%BKM3h!zD2W2XDYWjdd9UW$"){
        header("location: 403.html");
        exit;
    }

} else if(isset($_POST["pass"])){
    if($_POST["pass"] != "%BKM3h!zD2W2XDYWjdd9UW$"){
        header("location: 403.html");
        exit;
    }
}
else {
    header("location: 403.html");
    exit;
}
require_once("db.php");
ini_set('display_errors', '1');
//Import PHPMailer classes into the global namespace
//These must be at the top of your script, not inside a function
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

if(isset($_POST["subject"])&&isset($_POST["text"])){
    $subject = $_POST["subject"];
    $text = $_POST["text"];
//

    require_once("db.php");
    if($con){
        $users =mysqli_query($con, "SELECT email FROM emailAddresses");
        for ($i=0; $i < mysqli_num_rows($users); $i++) { 
            sendMail(mysqli_fetch_array($users)["email"], $subject, $text);
            }
    }else{
    
        echo("Error description: " . mysqli_connect_error($con));
    
    }

}
function sendMail($email, $subject, $text){
require_once 'PHPMailer/src/Exception.php';
require_once 'PHPMailer/src/PHPMailer.php';
require_once 'PHPMailer/src/SMTP.php';
//Create an instance; passing `true` enables exceptions
$mail = new PHPMailer();

try {
    //Server settings
   // $mail->SMTPDebug = SMTP::DEBUG_SERVER;                      //Enable verbose debug output
    $mail->isSMTP();                                            //Send using SMTP
    $mail->Host       = 'smtp.seznam.cz';                     //Set the SMTP server to send through
    $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
    $mail->Username   = 'hi@betthy.cz';                     //SMTP username
    $mail->Password   = 'dR*t*X99Gw23';                               //SMTP password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            //Enable implicit TLS encryption
    $mail->Port       = 465;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`
    //Recipients
    $mail->setFrom('hi@betthy.cz', 'Betthy');
    $mail->addAddress($email);


    //Content
    $mail->isHTML(true);                                  //Set email format to HTML
    $mail->Subject = "ReMinder oznámení: ".$subject;
    //$mail->Body    = $text;
    $mail->Body    = "<html>
    <style>
        .obsah {
            width: 80%;
            margin: 0 auto;
            background-color: white;
            padding: 2.5rem 2.5rem;
            box-shadow: 2px 2px 10px rgba(0, 0, 0, 0.1);
            border-radius: 9px;
        }
    
        .pozadi {
            padding: 2rem 0;
            background: linear-gradient(to right, #E326C4, #f4b556) #f4b556;
            width: 100%;
        }
    
    
        p {
            padding-top: 1rem;
            font-family: arial;
            color: rgb(57, 57, 57);
            font-size: 1rem;
            text-align: center;
        }
    
        h1,
        h2 {
            font-size: 2rem;
            color: #EC6E8D;
            text-align: center;
            margin: 0;
            font-family: arial;
        }
    
        h2 {
    
            font-size: 1.5rem;
            color: black;
            margin-top: 3rem;
        }
    
        .grey {
            font-size: 0.7rem;
            color: rgb(169, 169, 169);
            text-align: center;
            
        }
    
        .button {
            font-family: arial;
            display: block;
            margin: 0 auto;
            width: 40%;
            padding: 0.7rem;
            background: linear-gradient(to right, #E326C4, #f4b556) #f4b556;
            color: #fff;
            border-style: none;
            font-size: 1rem;
            cursor: pointer;
            border-radius: 9px;
            margin-top: 2rem;
            text-decoration: none;
            text-align: center;
        }
        a{
            text-align: center;
            margin: 0 auto;
            color: #EC6E8D;
        }
        i{
            text-align: center;
            font-size: 0.9rem;
        }
    </style>
    <div class='pozadi'>
        <div class='obsah'>
            <h1>".$subject."</h1>
            <p>".$text."</p>
            <p><i>Betthy</i></p>
            <p class='grey'>&copy; Betthy's <a href='https://reminder.betthy.cz'>ReMinder</a>.</p>
        </div>
    </div>
    <html>";
    $mail->AltBody = $text;
    $mail->CharSet = 'utf-8';

    $mail->send();
} catch (Exception $e) {
    echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
}
}
?>
<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Send annoucement</title>
    <form action="annoucement.php" method="POST">     
        <input id="subject" name="subject" placeholder="subject" type="text" maxlength="30" minlength="3" required><br>
        <textarea id="text" name="text" rows="10" cols="30"></textarea><br>
        <input id="pass" name="pass" type="hidden" value="<?php echo $_GET["pass"]; ?>" required>
        <button id="submit" type="submit">Send</button>
        </form>
</head>
<body>
    
</body>
</html>