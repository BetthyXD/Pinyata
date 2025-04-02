<?php
ini_set('display_errors', '1');
//Import PHPMailer classes into the global namespace
//These must be at the top of your script, not inside a function
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
function sendLoginMail($email, $link){
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
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;           //Enable implicit TLS encryption
        $mail->Port       = 465;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`
        //Recipients
        $mail->setFrom('hi@betthy.cz', 'Betthy');
        $mail->addAddress($email);
    
    
        //Content
        $mail->isHTML(true);                                  //Set email format to HTML
        $mail->Subject = "Přihlašovací link";
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
                <h1>Ahoj!</h1>
                <p>Teď se můžeš přihlásit!</p>
                <a class='button' href='".$link."'>Přihlásit se</a>
                <p>Nebo použij následující odkaz:</p>
                <a href='".$link."'>".$link."</a>
                <p class='grey'>Neodesílal(a) jsi žádost o přihlášení? Nevadí, nejspíš jde pouze o omyl a tento email můžeš
                    klidně ignorovat</p>
            </div>
        </div>
        </html>";
        $mail->AltBody = $link;
        $mail->CharSet = 'utf-8';
    
        $mail->send();
    } catch (Exception $e) {
        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
    }

    ?>