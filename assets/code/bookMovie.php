<?php

function dayChanger() {
    $MovieID = $_GET['MovieID'];
    if (isset($_GET['toLoad'])) {
        // This is the day to load...
        $toLoad = $_GET['toLoad'];
        $prev_enabled = true;
        $next_enabled = true;
        $nextToLoad = 0;
        $lastToLoad = 0;
        $Date = strtotime("+$toLoad day");
        $holder = $toLoad + 1;
        $HoursTomorrow = strtotime("+$holder day");
        $Day = "Today";
        switch($toLoad) {
            case "0":
                $prev_enabled = false;
                $nextToLoad = 1;
                $Date = time();
                $holder = $toLoad + 1;
                $HoursTomorrow = strtotime("+1 day");
                $Day = "Today";
                $next_enabled = true;
                break;
            case "1":
                $nextToLoad = 2;
                $lastToLoad = 0;
                $Day = "Tomorrow";
                $next_enabled = true;
                $prev_enabled = true;
                break;
            case "2":
                $nextToLoad = 3;
                $lastToLoad = 1;
                $Day = date("l", $Date);
                $next_enabled = true;
                $prev_enabled = true;
                break;
            case "3":
                $nextToLoad = 4;
                $lastToLoad = 2;
                $Day = date("l", $Date);
                $next_enabled = true;
                $prev_enabled = true;
                break;
            case "4":
                $nextToLoad = 5;
                $lastToLoad = 3;
                $Day = date("l", $Date);
                $next_enabled = true;
                $prev_enabled = true;
                break;
            case "5":
                $nextToLoad = 6;
                $lastToLoad = 4;
                $Day = date("l", $Date);
                $next_enabled = true;
                $prev_enabled = true;
                break;
            case "6":
                $nextToLoad = 7;
                $lastToLoad = 5;
                $Day = date("l", $Date);
                $next_enabled = true;
                $prev_enabled = true;
                break;
            case "7":
                $lastToLoad = 6;
                $Day = date("l", $Date);
                $next_enabled = false;
                $prev_enabled = true;
                break;
        }
        if ($prev_enabled){
            echo "<div class='col-3 hover-darken'><a href='?MovieID=$MovieID&toLoad=$lastToLoad'><h1 class='text-left'><<h1></a></div>";
        } else {
            echo "<div class='col-3 hover-darken'><h1 class='text-left'><<h1></div>";
        }
        echo "<div class='col-6'><h2 class='text-center'>$Day</h2></div>";
        if ($next_enabled){
            echo "<div class='col-3 hover-darken'><a href='?MovieID=$MovieID&toLoad=$nextToLoad'><h1 class='text-right'>></h1></a></div>";
        } else {
            echo "<div class='col-3 hover-darken'><h1 class='text-right'>></h1></div>";
        }
    } else {
        echo
        "
            <div class='col-3 hover-darken'><h1 class='text-left'><<h1></div>
            <div class='col-6'><h2 class='text-center'>Today</h2></div>
            <div class='col-3 hover-darken'><a href='?MovieID=$MovieID&toLoad=1'><h1 class='text-right'>></h1></a></div>
        ";
        $Date = time();
        $HoursTomorrow = strtotime("+1 day");
        $Day = "Today";
    }
    return $Date."$$".$HoursTomorrow."$$".$Day;
}

function bookMovie() {
    include('helper.php');
    $dbh = connectToDatabase();

    if (isset($_GET['MovieID'])) {
        $MovieID = $_GET['MovieID'];


        echo 
        "
        <div class='row'>
            <div class='col-6 no-padding'>
            <div class='row'>
                <div class='col-1'></div>
                <div class='col-10 no-padding smb'><img class='liftOff expandIMG' src='assets/img/movies/$MovieID.jpg'></div>
                <div class='col-1'></div>
            </div>
            </div>
            <div class='col-6 expandMe'>
            <div class='white smr'>
                <div class='row'>
                ";

        // We first need to see if a session time has been selected... If it has...
        // we wont load the feature to change the days, as that isnt required anymore...
        $MovieClassification = "";
        $Runtime = "";
        if (isset($_GET['SessionTime'])) {
            
            // Going to now load the information they have hopefully provided us...
            // We are just going to assume that everything we need is set.
            $SessionID = $_GET['SessionID'];
            $SessionTime = $_GET['SessionTime'];
            $SessionPrice = $_GET['SessionPrice'];
            $SessionSeats = $_GET['SessionSeats'];
            $SessionDay = $_GET['SessionDay'];

            echo
            "
            <div class='col-3'></div>
            <div class='col-6'><h2 class='text-center'>$SessionDay</h2></div>
            <div class='col-3'></div>
            </div>
            </div>
            <br />
            <section id='ticketsSection'>
            <div class='row white-text text-center smb'>
            <div class='col-4 text-right'>$$SessionPrice - </div>
            <div class='col-4'><a href='ViewSession.php?Session=$SessionID&PricePaid=$SessionPrice' class='ticketButton text-center'> $SessionTime </a></div>
            <div class='col-4'>$SessionSeats Seats Left</div>
            ";
            // NEED TO RENDER THE FORM AND THE TICKET COUNT...
            echo "<h1 class='white-text'>WOWOWOWOWOWO</h1>";
            echo 
            "</div>
            </section>
            <br />
            ";
        } else {
            $results = dayChanger();
            $tokens = explode("$$", $results);
            $today = $tokens[0];
            $tomorrow = $tokens[1];
            $Day = $tokens[2];

            echo
            "</div>
            </div>
            <br />
            <section id='ticketsSection'>
            ";

            $statement = $dbh->prepare("SELECT SessionID, MovieID, Classification, RunTime, SeatsAvailable, NormalPrice AS Price, STRFTIME('%d %m %Y', SessionTime, 'unixepoch',  'localtime') AS SessionDate, STRFTIME('%H:%M', SessionTime, 'unixepoch',  'localtime') AS SessionTime FROM Sessions JOIN Movies USING (MovieID) where MovieID = $MovieID AND (SessionTime >= $today AND SessionTime <= $tomorrow)");
            $statement->execute();
            while($row = $statement->fetch()) {
                $MovieClassification = makeOutputSafe($row['Classification']);
                $Price = makeOutputSafe($row['Price']);
                $SessionID = makeOutputSafe($row['SessionID']);
                $Time = makeOutputSafe($row['SessionTime']);
                $Price_Send = str_replace('.', '%2E', $Price);
                $AvaliableSeats = makeOutputSafe($row['SeatsAvailable']);


                $MovieClassification == 'PG-13' ? $MovieClassification = "PG" : $MovieClassification = $MovieClassification;
                $MovieClassification == 'TV-MA' || $MovieClassification == 'TV-14' ? $MovieClassification = "MA" : $MovieClassification = $MovieClassification;
                $MovieClassification == 'UNRATED' || $MovieClassification == 'N/A' || $MovieClassification == 'NOT RATED' ? $MovieClassification = "U" : $MovieClassification = $MovieClassification;
            
                echo 
                "
                <div class='row white-text text-center smb'>
                <div class='col-4 text-right'>$$Price - </div>
                <div class='col-4'><a href='?MovieID=$MovieID&SessionTime=$Time&SessionID=$SessionID&SessionPrice=$Price&SessionSeats=$AvaliableSeats&SessionDay=$Day' class='ticketButton text-center'> $Time </a></div>
                <div class='col-4'> $AvaliableSeats Seats Left</div>
                </div>
                ";
            }
        }
        echo
        "
        <div class='text-center'>
            <h3 class='normal-text white-text'>Rated:   <span id='rating$MovieClassification' class='bold-text'>$MovieClassification</span>   RunTime: <span class='bold-text small-text-margin-left'>$Runtime</span> minutes</h3>
        </div>
        ";
    }
  }
  ?>