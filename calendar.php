<?php
require_once("starter.php");


if (!isset($_COOKIE["email"]) || !isset($_COOKIE["hashId"])) {
    $_SESSION["destination"] = "calendar.php";
    header('Location: index.php');
    exit();
} else {
    $email = $_COOKIE["email"];
    if(!isset($con)){
        $con = db_connect();
    }
}


const mesice = ["Leden", "Únor", "Březen", "Duben", "Květen", "Červen", "Červenec", "Srpen", "Září", "Říjen", "Listopad", "Prosinec"];
?>

<!DOCTYPE html>
<html lang="cs">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="svatky.css">
    <link rel="stylesheet" type="text/css" href="gradient.css?v2">
    <link rel="icon" type="image/svg+xml" href="/favicon.svg">
    <title>Reminder by Betthy</title>
    <style>

        .gradient{
            width: 1000px;
        }
        .page {
            padding-top: 0;
            text-align: center;
        }

        main {
            grid-template-columns: 1fr 1fr 1fr 1fr 1fr 1fr 1fr;
            height: 100%;


        }

        .day {
            height: 100%;
            width: auto;
            border-radius: 15px;
            transition: none;
            padding: 0.5rem 1rem;
            box-sizing: border-box;
            gap: 2px;
        }

        .dayInWeek {
            height: 100%;
            width: auto;
            border-radius: 15px;
            overflow: hidden;
            cursor: auto;


        }

        #monthTitle {
            display: inline;
            margin: 0;
            padding: 0;
            width: 15rem;
        }

        #title {
            margin: 1rem 0 1.5rem 0;
            display: flex;
            flex-direction: row;
            align-items: center;
            justify-content: center;
            text-align: center;
        }

        #title>svg {
            width: 2rem;
            height: auto;
            padding: 0.3rem 1rem 0 1rem;
            cursor: pointer;
        }


        .event {
            background-color: white;
            font-size: 0.6rem;
            overflow: hidden;
            border-radius: 2px;
            padding: 2px 5px;
            
        }

        .event.type0 {
            background-color: lightblue;
        }
        .event.type1 {
            background-color: pink;
        }
        .event.type2 {
            background-color: #ffcaaf;
        }
    </style>
</head>

<body>
<p onClick="window.location.href = 'reminder.php';" style="font-size: 1.2rem; text-align: center; margin: 1.5rem 0 0 0; padding: 0; cursor: pointer;"><b>→</b> Zpět do <b>Reminderu!</b></p>
    <div class="page">

        <div id="title">
            <svg onclick="previousMonth()" viewBox="0 0 24 24" fill="none">
                <path d="M16.1795 3.26875C15.7889 2.87823 15.1558 2.87823 14.7652 3.26875L8.12078 9.91322C6.94952 11.0845 6.94916 12.9833 8.11996 14.155L14.6903 20.7304C15.0808 21.121 15.714 21.121 16.1045 20.7304C16.495 20.3399 16.495 19.7067 16.1045 19.3162L9.53246 12.7442C9.14194 12.3536 9.14194 11.7205 9.53246 11.33L16.1795 4.68297C16.57 4.29244 16.57 3.65928 16.1795 3.26875Z" fill="#0F0F0F" />

            </svg>
            <h1 id="monthTitle"><?php echo mesice[date("m") - 1] . " " . date("Y");  ?></h1>
            <svg onclick="nextMonth()" viewBox="0 0 24 24" fill="none">
                <path d="M7.82054 20.7313C8.21107 21.1218 8.84423 21.1218 9.23476 20.7313L15.8792 14.0868C17.0505 12.9155 17.0508 11.0167 15.88 9.84497L9.3097 3.26958C8.91918 2.87905 8.28601 2.87905 7.89549 3.26958C7.50497 3.6601 7.50497 4.29327 7.89549 4.68379L14.4675 11.2558C14.8581 11.6464 14.8581 12.2795 14.4675 12.67L7.82054 19.317C7.43002 19.7076 7.43002 20.3407 7.82054 20.7313Z" fill="#0F0F0F" />
            </svg>
        </div>

        <div class="gradient">
            <div id="list">
                <main>
                    <div class="dayInWeek">Pondělí</div>
                    <div class="dayInWeek">Úterý</div>
                    <div class="dayInWeek">Středa</div>
                    <div class="dayInWeek">Čtvrtek</div>
                    <div class="dayInWeek">Pátek</div>
                    <div class="dayInWeek">Sobota</div>
                    <div class="dayInWeek">Neděle</div>
                    <?php
                    for ($i = 0; $i < 42; $i++) {
                        echo '<div class="day" id="day' . ($i + 1) . '"></div>';
                    }
                    ?>
                </main>
            </div>
        </div>


        <?php require("footer.php"); ?>

        <div onClick="window.location = 'logout.php';" id="logout">Odhlásit se</div>
    </div>
    </div>

    <script>
        const monthTitle = ["Leden", "Únor", "Březen", "Duben", "Květen", "Červen", "Červenec", "Srpen", "Září", "Říjen", "Listopad", "Prosinec"];
        let currentMonth = new Date().getMonth();
        let currentYear = new Date().getFullYear();
        loadMonthDays(currentMonth, currentYear);

        function previousMonth() {
            currentMonth--;
            if (currentMonth < 0) {
                currentMonth = 11;
                currentYear--;
            }
            document.getElementById("monthTitle").innerText = monthTitle[currentMonth] + " " + currentYear;
            loadMonthDays(currentMonth, currentYear);

        }

        function nextMonth() {
            currentMonth++;
            if (currentMonth > 11) {
                currentMonth = 0;
                currentYear++;
            }
            document.getElementById("monthTitle").innerText = monthTitle[currentMonth] + " " + currentYear;
            loadMonthDays(currentMonth, currentYear);

        }

        function loadMonthDays(month, year) {
            let events;
            document.querySelector("main").style.gridTemplateRows = "2.5rem 1fr 1fr 1fr 1fr 1fr";
            document.querySelector("main").style.gridRowGap = "1rem";
            let offset = (new Date(year, month, 1)).getDay(); // Get the day of the week (0 for Sunday, 6 for Saturday)
            offset = (offset - 1 + 7) % 7; // Adjust offset to start from Monday (0 for Monday, 6 for Sunday)
            let posledniDenMesice = new Date(year, month+1, 0).getDate();

            for (let i = 0; i < 42; i++) {
                let dayNum = i - offset + 1;
                if (dayNum < 1) {
                    document.getElementById("day" + (i + 1)).style.opacity = 0.2;
                    document.getElementById("day" + (i + 1)).style.display = "flex";
                    document.getElementById("day" + (i + 1)).innerText = "";
                } else if (dayNum > posledniDenMesice) {
                    if (posledniDenMesice + offset <= 35 && i >= 35) {
                        document.getElementById("day" + (i + 1)).style.display = "none";
                        document.getElementById("day" + (i + 1)).style.opacity = 1;
                    } else {
                        document.getElementById("day" + (i + 1)).style.opacity = 0.2;
                        document.getElementById("day" + (i + 1)).style.display = "flex";
                        document.getElementById("day" + (i + 1)).innerText = "";
                    }


                } else {
                    document.getElementById("day" + (i + 1)).style.display = "flex";
                    document.getElementById("day" + (i + 1)).style.opacity = 1;
                    document.getElementById("day" + (i + 1)).innerText = dayNum;
                    if (i >= 35) {
                        document.querySelector("main").style.gridTemplateRows = "2.5rem 1fr 1fr 1fr 1fr 1fr 1fr";
                        document.querySelector("main").style.gridRowGap = "0.7rem";
                    }

                }
                document.getElementById("day" + (i + 1)).style.backgroundColor = "white";
            }
            
            loadEvents(month, year, offset);
        }


        function loadEvents(month, year, offset) {
            //AJAX
            const xhttp = new XMLHttpRequest();
            let data = new FormData();
            data.append("type", "event");
            data.append("month", month);
            data.append("year", year);

            xhttp.onload = function() {
                events = this.responseText.split(";");
                //0 -> id
                //1 -> start
                //2 -> end
                //3 -> type
                //4 -> name


                //zobrazení eventů
                events.forEach(function(event) {
                    let values = event.split(",");
                    let start = parseInt(values[1]);
                    let end = parseInt(values[2]);
                    if (values[2] != "") {
                        for (let i = start; i <= end; i++) {
                            addEvent("day" + (offset + i), values[3],values[4]);

                        }
                    } else {
                        addEvent("day" + (offset + start),values[3],values[4]);

                    }


                });

            }

            xhttp.open("POST", "ajax.php", false);
            xhttp.send(data);
            //

        }

        function addEvent(id, type, name){
            if(name.length >20){
                name = name.substr(0,17)+"...";
            }

            document.getElementById(id).innerHTML += '<div class="event type'+type+'">'+name+'</div>';
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
    </script>
</body>

</html>