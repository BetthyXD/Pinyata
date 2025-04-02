<?php
//update page
require_once("starter.php");


if (!isset($_COOKIE["email"]) || !isset($_COOKIE["hashId"])) {
    header('Location: index.php');
    exit();
} else{
    $email = $_COOKIE["email"];
    require_once("db.php");
}

?>

<!DOCTYPE html>
<html lang="cs">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="svatky.css">
    <link rel="stylesheet" type="text/css" href="gradient.css?v27">
    <link rel="icon" type="image/svg+xml" href="/favicon.svg">
    <title>Reminder by Betthy</title>
   
</head>

<body>
    <?php
    
    $kontakty = mysqli_query($con, "SELECT * FROM users WHERE email = '" . $email . "'");


    ?>
    <p onClick="window.location.href = '/calendar';" style="font-size: 1.2rem; text-align: center; margin: 3rem 0 0 0; padding: 0; cursor: pointer;"><b>→</b> Novinka! <b>Kalendář!</b></p>

    <div class="page">
    <div onClick="sorter()" id="sort">Řazení: Podle data</div>
    


        <div class="gradient">
            <?php
            if (mysqli_num_rows($kontakty) > 0) {
                echo '<div id="list">
    <main>
    <div onClick="newPerson()" id="addNewPersonButton" class="person"><svg viewBox="0 0 24 24" fill="none">
    <path d="M4 12H20M12 4V20" stroke="#000000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
</svg></div>';
                for ($i = 0; $i < mysqli_num_rows($kontakty); $i++) {
                    $user = mysqli_fetch_row($kontakty);
                    $datum = mysqli_query($con, "SELECT den, mesic FROM svatek WHERE jmeno = '" . $user[1] . "'");
                    if (mysqli_num_rows($datum) > 0) {
                        $mesice = ["ledna", "února", "března", "dubna", "května", "června", "července", "srpna", "září", "října", "listopadu", "prosince"];
                        
                        $datum = mysqli_fetch_row($datum);
                        echo '<div onClick="removePerson(this)" id="person' . $i . '" class="person"><h2>' . $user[1] . " " . $user[2] . '</h2><h3>' . (int)$datum[0] . ". " . $mesice[(int)$datum[1] - 1] . '</h3></div>';
                    } else {
                        echo '<div onClick="removePerson(this)" id="person' . $i . '" class="person notFound"><h2>' . $user[1] . " " . $user[2] . '</h2><h3 class="notFound">' . "Neznámé" . '</h3></div>';
                    }
                }

                echo '
    </main>

</div>';
            } else {
                echo '
                <div onclick="newPerson()" class="noRecord" id="noRecord">
    <svg viewBox="0 0 24 24" fill="none">
        <path d="M4 12H20M12 4V20" stroke="#ffffff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
    </svg>
    <h1>Je tu prázdno.</h1>
    <p>Kliknutím přidej svého známého...</p>
</div>';
            }
            ?>


        </div>

        
        <?php require("footer.php"); ?>
        
        <div onClick="window.location = 'logout.php';" id="logout">Odhlásit se</div>
        
        
        <div id="personPopUpBlack">
            <div class="personPopUp">
                <form id="personForm">
                    <h1>Nový kontakt</h1>
                    <input type="text" id="jmenoIn" placeholder="Jméno" name="jmenoIn" minlenght="2" maxlenght="30" pattern="^[a-zA-Zá-žÁ-Ž]{1,30}$" required>
                    <input type="text" id="prijmeniIn" placeholder="Příjmení" maxlenght="30" pattern="^[a-zA-Zá-žÁ-Ž\-]{1,30}$" name="prijmeniIn">
                    <span>
                    <label for="birthdayDIn"><b style="color: red;">JIŽ BRZY!</b> Narozeniny</label>
                    <span>
                    <input type="number" placeholder="Den" id="birthdayDIn" name="birthdayDIn" min="1" max="31" readonly>
                    <input type="number" placeholder="Měsíc" id="birthdayMIn" name="birthdayMIn" min="1" max="12" readonly>
                    <input type="number" placeholder="Rok" id="birthdayYIn" name="birthdayYIn" min="0" max="10000" readonly>
                    </span>
                    </span>
                    <div>
                        <button type="reset" onClick="addNewPerson()" class="submit">Přidat</button>
                        <button type="reset" onClick="hideNewPerson()" class="cancel">Zrušit</button>
                    </div>
                </form>
            </div>




        </div>
    </div>
    
    <script>
        sortContacts(0);
        sort = 0;

        function sorter(){
            sort++;
            if(sort>1){
                sort = 0;
                document.getElementById("sort").innerText = "Řazení: Podle data";
            }else{
                document.getElementById("sort").innerText = "Řazení: Podle jména";
            }
            sortContacts(sort);
            
        }

        //Scripy JS
        function Contact(jmeno, svatek, svatekF) {
        this.jmenoPrijmeni = jmeno;
        this.svatek = svatek;
        this.svatekF = svatekF;
            }


        function sortContacts(type){
            let kontakty = [];
            document.querySelectorAll("main div:not(#addNewPersonButton)").forEach((element) => {

            let jmenoPrijmeni = element.querySelector('h2').innerText;
            let svatekF = element.querySelector('h3').innerText;
            let svatek = prevodNaCislo(svatekF);
            kontakty.push(new Contact(jmenoPrijmeni, Number(svatek),svatekF));
          });
          let dnes = new Date();
            let den = String(dnes.getDate()).padStart(2, '0');
            let mesic = String(dnes.getMonth() + 1).padStart(2, '0'); // Leden je 0!
            dnes = Number(mesic + den);
                    if(type == 0){
                    //sort podle data
          kontakty.sort((a, b) => {
            if(isNaN(a.svatek)){
                        return 1;
                    }else if(isNaN(b.svatek)){
                        return -1;
                    }
            
            let svatekA = a.svatek;
            let svatekB = b.svatek;

            
                    svatekA = svatekA-dnes;
                    svatekB= svatekB-dnes;
                    
                    if(svatekA<0){
                        svatekA = 1231+svatekA;
                    }
                    if(svatekB<0){
                        svatekB = 1231+svatekB;
                    }

                    //porovnání
    return svatekA - svatekB;
});
        //sort
    } else if(type == 1){
        kontakty.sort((a, b) => {
            jmenoA = a.jmenoPrijmeni;
            jmenoB = b.jmenoPrijmeni;
            return jmenoA.localeCompare(jmenoB);
});


    }


document.querySelectorAll("main div:not(#addNewPersonButton)").forEach((element) => {
    element.remove();
});
let i = 0;
kontakty.forEach((element) => {
    if(isNaN(element.svatek)){
        document.querySelector("main").innerHTML += '<div onClick="removePerson(this)" id="person' + i + '" class="person notFound"><h2>' + element.jmenoPrijmeni + '</h2><h3 class="notFound">' + "Neznámé" + '</h3></div>';

    }else{
        document.querySelector("main").innerHTML += '<div onClick="removePerson(this)" id="person' + i + '" class="person"><h2>' + element.jmenoPrijmeni + '</h2><h3>' +element.svatekF+ '</h3></div>';

    }
    i++;

});


        }
        

function prevodNaCislo(datum) {
    const mesice = {
  "ledna": 1,
  "února": 2,
  "března": 3,
  "dubna": 4,
  "května": 5,
  "června": 6,
  "července": 7,
  "srpna": 8,
  "září": 9,
  "října": 10,
  "listopadu": 11,
  "prosince": 12
};
  let castiDatumu = datum.split('. ');
  let den = castiDatumu[0];
  let mesic = mesice[castiDatumu[1]];
  
  // Přidání nuly před jednocifernými čísly
  den = den < 10 ? '0' + den : den;
  mesic = mesic < 10 ? '0' + mesic : mesic;

  return parseInt(`${mesic}${den}`, 10);
}

        function newPerson() {
            document.getElementById("personPopUpBlack").style.display = "block";
            document.getElementById('jmenoIn').focus();
        }

        function hideNewPerson() {
            document.getElementById("personPopUpBlack").style.display = "none";
        }

        function addNewPerson() {
            const xhttp = new XMLHttpRequest();
            let data = new FormData();
            //data.append("birthdayDIn", document.getElementById("birthdayDIn").value);
            //data.append("birthdayMIn", document.getElementById("birthdayMIn").value);
            //data.append("birthdayYIn", document.getElementById("birthdayYIn").value);
            data.append("jmenoIn", document.getElementById("jmenoIn").value);
            data.append("prijmeniIn", document.getElementById("prijmeniIn").value);

            xhttp.onload = function( ){
                if(this.responseText != false && this.responseText != true){
                    if(document.getElementsByClassName("noRecord").length > 0){
                        window.location = "reminder.php";
                    }
                    document.getElementById("addNewPersonButton").insertAdjacentHTML('afterend',this.responseText);
                    document.getElementById("personPopUpBlack").style.display = "none";
                }else if(this.responseText != true) {
                    window.location = "logout.php";
                }else{
                    document.getElementById("personPopUpBlack").style.display = "none";
                }
                document.getElementById("personForm").reset();
            }

            xhttp.open("POST","ajax.php", true);
            xhttp.send(data);
       }

        function removePerson(element) {
            let jmenoPrijmeni = element.querySelector('h2').innerText;

             const xhttp = new XMLHttpRequest();
            let data = new FormData();
            data.append("remover", jmenoPrijmeni);

            xhttp.onload = function( ){
                if(this.responseText != false){
                    element.remove();
                }else {
                    window.location = "logout.php";
                }
            }

            xhttp.open("POST","ajax.php", true);
            xhttp.send(data);
            

        }
  document.getElementById('jmenoIn').addEventListener('keydown', function(event) {
    if (event.key === 'Enter') {
      event.preventDefault(); // Zabráníme odeslání formuláře
      document.getElementById('prijmeniIn').focus(); // Přesměrování focusu na druhý input
    }
  });


  document.getElementById('prijmeniIn').addEventListener('keydown', function(event) {
    if (event.key === 'Enter') {
    addNewPerson();
    }
  });
    </script>
</body>

</html>