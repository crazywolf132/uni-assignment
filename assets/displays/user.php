<?php

    function pageHandler() {
        if (isset($_GET['Screen'])) {
            $screenMode = $_GET['Screen'];
            displayNavBar($screenMode);
            switch($screenMode) {
                case "1":
                    // This is the Front page...
                    showHomeScreen();
                    break;
                case "2":
                    // All bought tickets...
                    loadAllTickets();
                    break;
                case "3":
                    // Edit account info...
                    editAccountInfo();
                    break;
                case "4":
                    //Latest Tickets.
                    latestTickets();
                    break;
            }
        } else {
            // There is no Screen variable... so we are going to assume its the homescreen.
            displayNavBar(1);
            showHomeScreen();
        }
    }

    function editAccountInfo(){
        if (isset($_POST['old'])){
            $old = $_POST['old'];
            $new = $_POST['new'];
            $confirm = $_POST['confirm'];
        }
        echo "
        <div class='row smr sml'>
            <div class='col-2'></div>
            <div class='col-8 no-padding'>
                <h1 class='white-text text-center'>Change Password</h1>
                <hr class='white-hr' />
                <br />
                <form method='post'>
                    <input type='password' name='old' placeholder='Old Password' class='inputBox' />
                    <input type='password' name='new' placeholder='New Passowrd' class='inputBox' />
                    <input type='password' name='confirm' placeholder='Confirm Password' class='inputBox' />
                    <button type='submit' class='formButton'>Change</button>
                </form>
            </div>
            <div class='col-2'></div>
        ";
    }

    function displayNavBar($var) {
        echo "
        <section id='adminNav'>
            <nav>
                <ul>
                    <a href='?Screen=1'><li"; if($var == 1){echo " id='active'";} echo ">Front Page</li></a>
                    <a href='?Screen=2'><li"; if($var == 2){echo " id='active'";} echo ">Tickets</li></a>
                    <a href='?Screen=3'><li"; if($var == 3){echo " id='active'";} echo ">Edit Account</li></a>
                    <a href='?Screen=4'><li"; if($var == 4){echo " id='active'";} echo ">Latest Tickets</li></a>
                </ul>
            </nav>
        </section>
        ";
    }

    function showHomeScreen() {
        $dbh = connectToDatabase();
        $user = loadUser();
        $stmt = $dbh->prepare("SELECT COUNT(TicketID) AS Total, SUM (PricePaid) AS Spent FROM Tickets INNER JOIN MemberTickets USING (TicketID) WHERE MemberID = ?;");
        $stmt->bindValue(1, $user['MemberID']);
        $stmt->execute();
        $res = $stmt->fetch(PDO::FETCH_ASSOC);
        $amount = $res['Total'];
        $spent = $res['Spent'];
        echo 
        '
        <div class="row smr sml">
            <div class="col-6 no-padding">
                <div class="blue expand-color">
                <h1 class="text-center">Amount of tickets: '.$amount.'</h1>
                </div>
            </div>
            <div class="col-6 no-padding">
                <div class="pink expand-color">
                <h1 class="text-center">Total Spent: $'.$spent.'</h1>
                </div>
            </div>
        </div>
        ';
    }

    function loadAllTickets() {
        include_once('assets/code/barcode/handler.php');
        $dbh = connectToDatabase();
        $user = loadUser();
        $statement = $dbh->prepare("SELECT Title, MovieID, TicketID, Classification, COUNT(TicketID) AS Amount,  Plot, TimeStamp, SessionTime,  RoomID From MemberTickets INNER JOIN Tickets USING (TicketID) INNER JOIN Sessions USING (SessionID) INNER JOIN Movies USING (MovieID) WHERE MemberID = ? AND OnlinePurchase = 1 GROUP BY SessionID ORDER BY TimeStamp DESC ;");
        $statement->bindValue(1, $user['MemberID']);
        $statement->execute();

        while ($result = $statement->fetch()) {
            $Title = makeOutputSafe($result['Title']);
            $MovieID = makeOutputSafe($result['MovieID']);
            $TicketID = makeOutputSafe($result['TicketID']);
            $Rating = makeOutputSafe($result['Classification']);
            $Amount = makeOutputSafe($result['Amount']);
            $About = makeOutputSafe($result['Plot']);
            $Owner = $user['LastName'] . ", ". $user['FirstName'];
            $Purchase = date("g:ia - M jS", $result['TimeStamp']);
            $Showing = date("g:ia - M jS", $result['SessionTime']);
            $Room = makeOutputSafe($result['RoomID']);
            displayTicket($Title, $MovieID, $TicketID, $Rating, $Amount, $Owner, $About, $Purchase, $Showing, $Room);
        }
    }

    function latestTickets() {
        include_once('assets/code/barcode/handler.php');
        $dbh = connectToDatabase();
        $user = loadUser();
        $statement = $dbh->prepare("SELECT Title, MovieID, TicketID, Classification, COUNT(TicketID) AS Amount,  Plot, TimeStamp, SessionTime,  RoomID From MemberTickets INNER JOIN Tickets USING (TicketID) INNER JOIN Sessions USING (SessionID) INNER JOIN Movies USING (MovieID) WHERE MemberID = ? AND OnlinePurchase = 1 GROUP BY SessionID ORDER BY TimeStamp DESC LIMIT 1 ;");
        $statement->bindValue(1, $user['MemberID']);
        $statement->execute();

        $result = $statement->fetch(PDO::FETCH_ASSOC);

        if ($result && count($result) > 0) {
            $Title = makeOutputSafe($result['Title']);
            $MovieID = makeOutputSafe($result['MovieID']);
            $TicketID = makeOutputSafe($result['TicketID']);
            $Rating = makeOutputSafe($result['Classification']);
            $Amount = makeOutputSafe($result['Amount']);
            $About = makeOutputSafe($result['Plot']);
            $Owner = $user['LastName'] . ", ". $user['FirstName'];
            $Purchase = date("g:ia - M jS", $result['TimeStamp']);
            $Showing = date("g:ia - M jS", $result['SessionTime']);
            $Room = makeOutputSafe($result['RoomID']);
            displayTicket($Title, $MovieID, $TicketID, $Rating, $Amount, $Owner, $About, $Purchase, $Showing, $Room);
        } else {
            echo '<div class="expand-color white smr sml smt smb liftOff curved"><h1 class="text-center">Please buy a ticket first!</h1></div>';
        }
    }

    function displayTicket($Title, $ID, $TicketID, $Rating, $Amount, $Owner, $About, $Purchase, $Showing, $Room) {
        echo "
        <div class='expand-color white smr sml smt smb liftOff curved'>
            <div class='row'>
                <div class='col-3'>
                <img src='assets/img/movies/$ID.jpg' class='full-width curved liftOff'>
                </div>
                <div class='col-6 no-padding'>
                <h1>$Title <span id='rating$Rating'>$Rating</span></h1>
                <br />
                <h4>Room $Room, <span class='smr green-around-text'>Showing at $Showing</span></h4>
                <p>$About</p>
                <h4> Purchased at <span class='blue-around-text'> $Purchase</span></h4>
                <h4 class='smt'><span class='pink-around-text'>For $Amount "; if ($Amount > 1) { echo "People";}else { echo "Person";} echo "</span></h4>
                <h4>Purchased by <span class='green-around-text'> $Owner</span></h4>
                </div>
                <div class='col-3'>
                <h4>Ticket ID:</h4>";
                $res = generateBarcode($TicketID); echo $res;
                echo"</div>
            </div>
        </div>";

    }

    