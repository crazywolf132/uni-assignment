<?php

    function pageHandler() {
        if (isset($_GET['Screen'])) {
            $screenMode = $_GET['Screen'];
            displayNavBar($screenMode);
            switch($screenMode) {
                case "1":
                    // This is the homescreen.
                    showHomeScreen();
                    break;
                case "2":
                    // This is the users screen.
                    echo "Welcome to screen 2";
                    break;
                case "3":
                    // This is the add session screen.
                    
                    addSessionScreen();
                    break;
                case "4":
                    // This is the Reports screen.
                    if (isset($_GET['mode'])){
                        $theMode = $_GET['mode'];
                        if ($theMode == 2) {
                            ticketsForMovieScreen();
                        } else {
                            showAllReportsScreen(); 
                        }           
                    } else {
                        showReportsScreen();
                    }
                    
                    break;
            }
        } else {
            // Just going to show the same as screen 1.
            displayNavBar(1);
            showHomeScreen();
        }
    }

    function displayNavBar ($var) {
        echo 
        "
        <section id='adminNav'>
            <nav>
                <ul>
                <a href='?Screen=1'><li"; if($var == 1){ echo " id='active'";} 
                echo ">Front Page</li></a>
                <a href='?Screen=2'><li"; if($var == 2){ echo " id='active'";}
                echo ">Users</li></a>
                <a href='?Screen=3'><li"; if($var == 3){ echo " id='active'";}
                echo ">Add Session</li></a>
                <a href='?Screen=4'><li"; if($var == 4){ echo " id='active'";}
                echo">Reports</li></a>
                </ul>
            </nav>
        </section>
        ";
    }

    function showHomeScreen() {
        echo
        "
        <section id='adminContent'>
            <div class='row smr sml'>
                <div class='col-4 text-center green expand-color'>
                <h4><i class='fa fa-user'></i> USERS: "; countUsers(); echo "</h4>
                </div>
                <div class='col-4 text-center pink expand-color'>
                <h4><i class='fa fa-money-bill-alt'></i> PROFIT: "; getProfit(); echo "</h4>
                </div>
                <div class='col-4 text-center blue expand-color'>
                <h4><i class='fa fa-film'></i> Sessions: "; countSessions(); echo "</h4>
                </div>
            </div>
        </section>
        ";
    }

    function showReportsScreen() {
       
        echo
        "
         <section id='adminContent'>
            <div class='row sml smr'>
                <a href='?Screen=4&mode=1'>
                    <div class='col-6 text-center expand-color aqua'>
                        <h1>All Details</h1>
                    </div>
                </a>
                <a href='?Screen=4&mode=2'>
                    <div class='col-6 text-center expand-color magenta'>
                        <h1>View Tickets Sold for movie</h1>
                    </div>
                </a>
            </div>
        </section>

        ";
    }

    function ticketsForMovieScreen() {
        echo "<section id='adminContent'>";
        if(isset($_GET['SessionID'])) {
            $SessionID = $_GET['SessionID'];
            $dbh = connectToDatabase();
            $statement = $dbh->prepare("SELECT TicketID, TimeStamp AS Bought, SessionID, PricePaid, OnlinePurchase From Tickets LEFT JOIN MemberTickets USING (TicketID) WHERE SessionID = ? ORDER BY Bought DESC;");

            $statement->bindValue(1, $SessionID);

            $statement->execute();
            echo 
            "<div class='row smr sml'>
                <div class='col-12'>
                    <table>
                        <tr>
                            <th>Ticket ID</th>
                            <th>Price Paid</th>
                            <th>Time</th>
                            <th>Online Purchase</th>
                        </tr>";
                            
                        
            while($row = $statement->fetch()) {
                $ID = makeOutputSafe($row['TicketID']);
                $Time = date("d/m/Y g:i a", makeOutputSafe($row['Bought']));
                $Price = makeOutputSafe($row['PricePaid']);
                $Online = makeOutputSafe($row['OnlinePurchase']);
                ($Online == 'NULL') ? $Online = 0 : $Online = $Online;
                ($Online == 0) ? $Online = 'False' : $Online = 'True';
                echo
                "
                <tr>
                    <td>$ID</td>
                    <td>$Price</td>
                    <td>$Time</td>
                    <td>$Online</td>
                </tr>
                ";
                
            }
            echo 
            "</table>
                </div>
            </div>";
        } else {
            echo
            "<div class='row'>
                <div class='col-1'></div>
                <div class='col-10'>
                    <form action='' method='get'>
                    ";
                    foreach($_GET as $name => $value) {
                        $name = htmlspecialchars($name);
                        $value = htmlspecialchars($value);
                        echo '<input type="hidden" name="'. $name .'" value="'. $value .'">';
                    } echo"
                        <input type='text' placeholder='Movie ID...' name='SessionID' id='searchBar' class='searchBar' />
                    </form>
                </div>
                <div class='col-1'></div>
            </div>";
        }
        echo "<a href='?Screen=4'><div class='text-center sml smr smb smt rounded expand-color magenta'><h1>Go Back</h1></div></a>";
        echo "</section>";
    }

    function showAllReportsScreen() {
        $theStatement = "";
        if (isset($_GET['search'])) {
            $theStatement = "SELECT Tickets.SessionID AS SessionID, (1.1 / (1.8 + (SessionTime - Released)/604800) + 0.2) AS SessionLicnecePercent, STRFTIME('%Y-%m-%d %H:%M', Sessions.SessionTime, 'unixepoch', 'localtime') AS SessionTime,  RoomID, Title, Movies.Runtime, SeatsAvailable, COUNT(Tickets.TicketID) AS TicketsSold, NormalPrice, round(AVG(Tickets.PricePaid),2) as Average, round(SUM(PricePaid),2) as Revenue FROM Movies INNER JOIN Sessions using (MovieID) INNER JOIN Tickets USING (SessionID) WHERE Title LIKE ? GROUP BY Sessions.SessionID;";
        } else {
            $theStatement = "SELECT Tickets.SessionID AS SessionID, (1.1 / (1.8 + (SessionTime - Released)/604800) + 0.2) AS SessionLicnecePercent, STRFTIME('%Y-%m-%d %H:%M', Sessions.SessionTime, 'unixepoch', 'localtime') AS SessionTime,  RoomID, Title, Movies.Runtime, SeatsAvailable, COUNT(Tickets.TicketID) AS TicketsSold, NormalPrice, round(AVG(Tickets.PricePaid),2) as Average, round(SUM(PricePaid),2) as Revenue FROM Movies INNER JOIN Sessions using (MovieID) INNER JOIN Tickets USING (SessionID) GROUP BY Sessions.SessionID;";
        }
        $dbh = connectToDatabase();
        echo
        "<section id='adminContent'>
            <div class='row'>
                <div class='col-1'></div>
                <div class='col-10'>
                    <form action='' method='get'>";
                    foreach($_GET as $name => $value) {
                        $name = htmlspecialchars($name);
                        $value = htmlspecialchars($value);
                        echo '<input type="hidden" name="'. $name .'" value="'. $value .'">';
                    }    
                    echo"<input type='text' name='search' class='searchBar' placeholder='Search Movie...' />
                    </form>
                </div>
                <div class='col-1'></div>
            </div>
            <a href='?Screen=4'><div class='text-center sml smr smb smt rounded expand-color magenta'><h1>Go Back</h1></div></a>
            <div class='row smr sml'>
                <div class='col-12 no-padding no-letter-spacing '>
                    <table>
                        <tr>
                            <th class='hide-mobile'>Session ID</th>
                            <th class='hide-mobile'>Start Time</th>
                            <th class='hide-mobile'>Room ID</th>
                            <th>Movie Title</th>
                            <th class='hide-mobile'>Runtime</th>
                            <th>Seats Available</th>
                            <th>Tickets Sold</th>
                            <th class='hide-mobile'>Seats Remaining</th>
                            <th class='hide-mobile'>Normal Price</th>
                            <th class='hide-mobile'>Average Ticket Price</th>
                            <th>Revenue</th>
                            <th class='hide-mobile'>Session Licence Cost</th>
                            <th>Net Profit</th>
                        </tr>";
        $statement = $dbh->prepare($theStatement);
        if (isset($_GET['search'])) {$search = $_GET['search']; $statement->bindValue(1,makeOutputSafe("%$search%"));}
        $statement->execute();
        
        while($row = $statement->fetch()){
            $SessionID = makeOutputSafe($row['SessionID']);
            $SessionTime = makeOutputSafe($row['SessionTime']);
            $RoomID = makeOutputSafe($row['RoomID']);
            $Title = makeOutputSafe($row['Title']);
            $Runtime = makeOutputSafe($row['Runtime']);
            $SeatsAvailable = makeOutputSafe($row['SeatsAvailable']);
            $TicketsSold = makeOutputSafe($row['TicketsSold']);
            $NormalPrice = makeOutputSafe($row['NormalPrice']);
            $Average = makeOutputSafe($row['Average']);
            $Revenue = makeOutputSafe($row['Revenue']);
            $Remaining = $SeatsAvailable - $TicketsSold;
            $SessionLicnecePercent = makeOutputSafe($row['SessionLicnecePercent']);
            $Holder = $Revenue * $SessionLicnecePercent;
            $NetProfit = $Revenue - $Holder;
            $NetProfit = number_format($NetProfit, 2, '.', '');
            
            echo 
                "<tr>
                    <td class='hide-mobile'><a href='?Screen=4&mode=2&SessionID=$SessionID' class='black-text'>$SessionID</a></td>
                    <td class='hide-mobile'>$SessionTime</td>
                    <td class='hide-mobile'>$RoomID</td>
                    <td>$Title</td>
                    <td class='hide-mobile'>$Runtime</td>
                    <td>$SeatsAvailable</td>
                    <td>$TicketsSold</td>
                    <td class='hide-mobile'>$Remaining</td>
                    <td class='hide-mobile'>$$NormalPrice</td>
                    <td class='hide-mobile'>$$Average</td>
                    <td>$$Revenue</td>
                    <td class='hide-mobile'>$SessionLicnecePercent</td>
                    <td>$$NetProfit</td>
                </tr>";
        }
                    echo "</table>
                </div>
            </div>
        </section>";
    }

    function addSessionScreen() {
        if (isset($_POST['movieID']) && isset($_POST['movieDate']) && isset($_POST['movieTime'])){
            // We need to process the adding here...
            // We are only checking for a few things... we only really need to check for one anyways, as the others all
            // have the "required" feild anyways. Meaning the user cant press submit unless all feilds are entered.

            $dbh = connectToDatabase();

            $statement = $dbh->prepare("
            INSERT INTO Sessions (MovieID, SessionTime, RoomID, NormalPrice, SeatsAvailable)
            VALUES(?, ?, ?, ?, ?);
            ");
            $tokens = explode("-", $_POST['movieDate']);
            $tokens_time = explode(":", $_POST['movieTime']);
            $time = strtotime("$tokens[2]-$tokens[1]-$tokens[0] $tokens_time[0]:$tokens_time[1]");
            $statement->bindValue(1, $_POST['movieID']);
            $statement->bindValue(2, $time);
            $statement->bindValue(3, $_POST['movieRoom']);
            $statement->bindValue(4, $_POST['moviePrice']);            
            $statement->bindValue(5, $_POST['movieSeats']);
            $statement->execute();
            echo "<div class='text-centered green-around-text'><h2>Successfully Added</h2></div>";
        }
        echo
        "
        <section id='adminContent'>
            <div class='row'>
            <div class='col-2'></div>
            <div class='col-8'>
                <h1 class='white-text text-center'>Add New Session</h1>
                <br>
                <form method='post'>
                    <input type='number' class='inputBox' placeholder='MovieID' name='movieID' id='' required='true'>
                    <input type='date' class='inputBox' placeholder='Show Date' name='movieDate' id='' required='true'>
                    <input type='time' class='inputBox' placeholder='Session Time' name='movieTime' id='' required='true'>
                    <input type='number' class='inputBox' placeholder='Room Number' name='movieRoom' id='' required='true'>
                    <input type='number' class='inputBox' placeholder='Seats Avaliable' name='movieSeats' id='' required='true'>
                    <input type='number' class='inputBox' min='1' step='any' placeholder='Price' name='moviePrice' id='' required='true'>
                    <button type='submit' class='formButton'>Submit</button>
                </form>
            </div>
            <div class='col-2'></div>
            </div>
        </section>
        ";
    }


    function countUsers() {
        $dbh = connectToDatabase();
        $statement = $dbh->prepare("SELECT COUNT(MemberID) AS memberCount FROM Members");
        $statement->execute();

        while($row = $statement->fetch()) {
            $amount = makeOutputSafe($row['memberCount']);
            echo $amount;
        }
    }

    function countSessions() {
        $dbh = connectToDatabase();
        $statement = $dbh->prepare("SELECT COUNT(SessionID) AS sessionCount FROM Sessions");
        $statement->execute();

        while($row = $statement->fetch()) {
            $amount = makeOutputSafe($row['sessionCount']);
            echo $amount;
        }
    }

    function getProfit() {
        $dbh = connectToDatabase();
        $statement = $dbh->prepare("
        SELECT
        SUM(PricePaid) as NetProfit
        FROM Tickets
        ");
        
        $statement->execute();

        while($row = $statement->fetch()) {
            $profit = makeOutputSafe($row['NetProfit']);

            echo "$".$profit;
        }

    }

?>