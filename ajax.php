<?php
if (!isset($_COOKIE["email"]) || !isset($_COOKIE["hashId"])) {
    echo false;
    exit;
}
$email = $_COOKIE["email"];
if(filter_var($email, FILTER_VALIDATE_EMAIL)){
require("db.php");
if (isset($_POST["remover"])) {
        if(!hasNotCode($_POST["remover"])){
            echo false;
            exit;
        }
        $removeUser = explode(" ", $_POST["remover"]);
        if(count($removeUser)<2){
            $removeUser[1] = null;
        }
    if(mysqli_query($con, "DELETE FROM users WHERE email = '" . $email . "' AND name = '" . $removeUser[0] . "' AND surname = '" . $removeUser[1] . "'")){
        echo "DELETE FROM users WHERE email = '" . $email . "' AND name = '" . $removeUser[0] . "' AND surname = '" . $removeUser[1] . "'";
        exit;
    }else{
        echo false;
        exit;
    }

}

if (isset($_POST["jmenoIn"])) {
    if(hasNotCode($_POST["jmenoIn"])&&hasNotCode($_POST["prijmeniIn"])){
    $jmeno = mysqli_real_escape_string($con, ucfirst(trim($_POST["jmenoIn"])));
    if (isset($_POST["prijmeniIn"])) {
        $prijmeni = mysqli_real_escape_string($con, ucfirst(trim($_POST["prijmeniIn"])));
    } else {
        $prijmeni = null;
    }
    if(mysqli_num_rows(mysqli_query($con, "SELECT * FROM users WHERE email = '" . $email . "' AND name = '" . $jmeno . "' AND surname = '" . $prijmeni . "'"))==0){
        mysqli_query($con, "INSERT INTO users (email, name, surname) VALUES ('" . $email . "', '" . $jmeno . "', '" . $prijmeni . "')");

//
    $datum = mysqli_query($con, "SELECT den, mesic FROM svatek WHERE jmeno = '" . $jmeno . "'");
                    if (mysqli_num_rows($datum) > 0) {
                        $mesice = ["ledna", "února", "března", "dubna", "května", "června", "července", "srpna", "září", "října", "listopadu", "prosince"];
                        
                        $datum = mysqli_fetch_row($datum);
                        $id = time();
                        echo '<div onClick="removePerson(this)" id="personA' . $id . '" class="person"><h2>' . $jmeno . " " . $prijmeni . '</h2><h3>' . (int)$datum[0] . ". " . $mesice[(int)$datum[1] - 1] . '</h3></div>';
                        exit;
                    } else {
                        echo '<div onClick="removePerson(this)" id="personA' . $id . '" class="person notFound"><h2>' . $jmeno . " " . $prijmeni . '</h2><h3 class="notFound">' . "Neznámé" . '</h3></div>';
                        exit;
                    }

}else{
    echo true;
}
}else{
    echo false;
    exit;
}} 

if(isset($_POST["type"])){
//PROSTOR PRO AJAX... OVĚŘENÍ PŘIHLÁŠENÍ UŽ JE HOTOVÉ
if($_POST["type"] == "event"){
    $month = $_POST["month"];
    $year = $_POST["year"];
    $textResult = [];
    $eventsResult;
    $reminderResult = "";
    $reminderUsers = [];
    $queryResult = mysqli_query($con, "SELECT id, DAY(start) AS start, DAY(end) AS end, type, name, TIMESTAMPDIFF(HOUR, start, end) AS event_length FROM events WHERE email = '" . $email . "' AND ((MONTH(start) = " . ($month+1) . " AND YEAR(start) = " . $year . ") OR (MONTH(end) = " . ($month+1) . " AND YEAR(end) = " . $year . ")) ORDER BY event_length DESC");

    $event = mysqli_fetch_all($queryResult);
    foreach ($event as $row) {
        array_pop($row);
        $textResult[] = implode(",", $row);
    }
    $eventsResult = implode(";", $textResult);


    //načtení z reminderu
    if(strlen($month) < 2){
        $month = "0".$month+1;
    }else{
        $month = $month+1;
    }
    $queryResult = mysqli_query($con, "SELECT * FROM svatek WHERE mesic = '".(string)$month."'");
    $reminderUsersQuery = mysqli_query($con, "SELECT name FROM users WHERE email = '".$email."'");
    $user = mysqli_fetch_all($reminderUsersQuery);
    $duplicateCheck = "";
    foreach ($user as $row) {
        if($row[0] != $duplicateCheck){
            array_push($reminderUsers,$row[0]);
        }
        $duplicateCheck = $row[0];
        
    }
    $svatky = mysqli_fetch_all($queryResult, MYSQLI_ASSOC);
    foreach ($svatky as $row) {
        if(in_array($row["jmeno"], $reminderUsers)){

            $reminderResult .= "N/A,".(int)$row["den"].",,0,".$row["jmeno"].";";
        }
    }





    echo $reminderResult . ";" . $eventsResult;
    exit;
    
}








//
}
}else{
    echo false;
    exit;
}


function hasNotCode($string){
    $result = true;
    $specialChars = "<>\\/()#=\"'+{}[]";
    for ($i=0; $i < strlen($specialChars); $i++) { 
        if(strpos($string, $specialChars[$i]) !== false){
            $result = false;
            break;
        }
    }
    return $result;

}
?>