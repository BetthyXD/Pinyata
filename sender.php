<?php
require_once("starter.php");
$con = db_connect();
ini_set('display_errors', '1');
//Import PHPMailer classes into the global namespace
//These must be at the top of your script, not inside a function
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
if (date("H") == 7 && date("i") >= 45||date("H") == 8 && date("i") <= 15) {
    
    if($con){
        $info = array();
        $svatekDB =mysqli_query($con, "SELECT jmeno FROM svatek WHERE den = '".date('d')."' AND mesic = '".date('m')."'");
        for ($i=0; $i < mysqli_num_rows($svatekDB); $i++) { 
            $infoDB = mysqli_query($con, "SELECT * FROM users WHERE name = '".mysqli_fetch_array($svatekDB)["jmeno"]."'");
            for ($j=0; $j < mysqli_num_rows($infoDB); $j++) {
           array_push($info, mysqli_fetch_array($infoDB));
           
            }
        }
        $emails = array();
        //info je promněná, která obsahuje email ke jménu
        foreach($info as $value){
            if(mysqli_num_rows(mysqli_query($con, "SELECT email FROM emailAddresses WHERE email = '".$value["email"]."'"))>0){
              
            $check = true;
            foreach($emails as &$email){
                if($email[0] == $value["email"]){
                    $check = false;
                    if(!str_contains($email[1],$value["name"])){
                    $email[1] = $email[1]." a ".$value["name"];
                    }
                    $email[2] = $email[2]." a ".$value["name"]." ".$value["surname"];
                    break;
                 }      
            }
            if($check){
                $emails[] = array($value["email"], $value["name"], $value["name"]." ".$value["surname"]);

            }
              }  }
        
        for ($i=0; $i < count($emails); $i++) { 
            sendMail($emails[$i][0],$emails[$i][1],$emails[$i][2]);
        }

        
        

    }else{
    
        echo("Error description: " . mysqli_connect_error($con));
    
    }


}
function sendMail($email, $subject, $names){
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
    $mail->Subject = $subject." slaví!";
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
            color: rgb(169, 169, 169);
            
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
            display: block;
            text-align: center;
            margin: 0 auto;
            color: #EC6E8D;
        }
    </style>
    <div class='pozadi'>
        <div class='obsah'>
            <h1>Dnes má svátek ".$subject."!</h1>
            <p><b>".$names."</b></p>
            <p>Nezapomeň jim popřát!</p>
            <p class='grey'>Tato zpráva byla odeslána z aplikace <a href='https://reminder.betthy.cz'>ReMinder</a>.</p>
        </div>
    </div>
    <html>";
    $mail->AltBody = 'Dnes má svátek '.$names.'.';
    $mail->CharSet = 'utf-8';

    $mail->send();
} catch (Exception $e) {
    echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
}
}
?>