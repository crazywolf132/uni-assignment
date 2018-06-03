<?php

function showMovieTimes() {
	if (isset($_GET['MovieID'])) {
        $MovieID = $_GET['MovieID'];
		(isset($_GET['MovieName'])) ? $MovieTitle = $_GET['MovieName'] : header("Location: ViewMovie.php?MovieID=$MovieID");
		(isset($_GET['Rating'])) ? $MovieRating = $_GET['Rating'] : header("Location: ViewMovie.php?MovieID=$MovieID");
		(isset($_GET['RunTime'])) ? $MovieRuntime = $_GET['RunTime'] : header("Location: ViewMovie.php?MovieID=$MovieID");
		
		echo
		"
		<div class='row'>
			<div class='col-6 no-padding'>
				<div class='col-1'></div>
				<div class='col-10 no-padding smb'>
					<img class='liftOff expandIMG' src='assets/img/movies/$MovieID.jpg' />
					<br/>
					<h3 class='white-text text-center'>Runtime: $MovieRuntime Minutes</h3>
				</div>
				<div class='col-1'></div>
			</div>
			<div class='col-6 expandMe'>
				<h1 class='white-text text-center'>$MovieTitle <span id='rating$MovieRating'>$MovieRating</span></h1>
				<br />
        ";
        if (isset($_POST['PURCHASE_MODE'])) {
            $user = loadUser();
            /**
             * This is the code that will run after the "BUY NOW" button has been pressed after selecting session time
             * In here we collect all the variables that have been passed... and we make the ticket/tickets accordingly.
             * 
             * As there is a system in place to allow for multiple tickets to be bought... We are going to need to create a for
             * loop to do all the ticket creating...
             */

            $dbh = connectToDatabase();
            $dbh->beginTransaction();
            $counter = 0;
            $memberID = "";
            if (isLoggedIn()) {
                $memberID = $user['MemberID'];
            } else {
                setCookieMessage("Please login to purchase any tickets!");
                redirect("ViewMovie.php?MovieID=".$_POST['MovieID']);
            }

            //Getting the time here rather than in the for-loop. SO the tickets have the same purchase time in the DB.
            $time = time();

            //Loading lastID here so then we can access it for the cookie setting.
            $lastID = "";

            $amount = makeOutputSafe($_POST['amount']);

            if ($_POST['RealSessionTime'] < (time() - 30*60)){
                setCookieMessage("Sorry, the session has closed and is no longer accepting purchases");
                redirect("ViewMovie.php?MovieID=".$_POST['MovieID']);
            }

            for ($counter = 0; $counter < $amount; $counter++) {
                $statement = $dbh->prepare("INSERT INTO Tickets (SessionID, PricePaid, TimeStamp) VALUES(?, ?, ?)");
                $statement->bindValue(1, makeOutputSafe($_POST['SessionID']));
                $statement->bindValue(2, makeOutputSafe($_POST['Price']));
                $statement->bindValue(3, $time);
                $statement->execute();
                $lastID = $dbh->lastInsertId();
                // WE  ARE GOING TO ASSUME THERE IS A USERID AS WE ARE UP TO HERE...
                $statement = $dbh->prepare("INSERT INTO MemberTickets (TicketID, MemberID, OnlinePurchase) VALUES(?, ?, ?)");
                $statement->bindValue(1, $lastID);                 
                $statement->bindValue(2, $user['MemberID']);                 
                $statement->bindValue(3, "1");
                $statement->execute();         
            }
            $dbh->commit();
            setCookieMessage("Order Success!! Click here to view your ticket.$$".$lastID);
            redirect("ViewMovie.php?MovieID=".$_POST['MovieID']);

        } else {

            if (isset($_POST['SessionID'])) {
                /*
                * This whole section here is for if a user has selected their session time they want...
                * They now just have to either enter a username or be logged in. Aswell as enter amount of tickets...
                */
                
                $dbh = connectToDatabase();

                $SessionTime = $_POST['SessionTime'];
                $SessionPrice = $_POST['Price'];
                $Seats = $_POST['Seats'];
                // Need to add a form here... Need to check to see if user is logged in or not...
                
                echo
                "
                <div class='row white-text text-center smb'>
                    <div class='col-4'>$$SessionPrice each </div>
                    <div class='col-4'><a href='#' class='ticketButton-selected text-center'> $SessionTime </a></div>
                    <div class='col-4'>$Seats Seats left</div>
                </div>
                ";
                
                echo 
                "
                <form method='post' class='smr'>
                ";foreach($_POST as $name => $value) {
                            $name = htmlspecialchars($name);
                            $value = htmlspecialchars($value);
                            echo '<input type="hidden" name="'. $name .'" value="'. $value .'">';
                        } echo"
                    <input name='amount' type='number' placeholder='Amount' class='inputBox red-outline' max='$Seats' min='1' required='true' />
                "; if (!(isLoggedIn())) {
                    echo "<input name='username' type='text' placeholder='Purchaser Name' class='inputBox red-outline' required='true' />";
                } else {
                    $user = loadUser();
                    echo "<input name='username' value='". $user['FirstName'] ."' type='hidden' />";
                } echo"<input name='PURCHASE_MODE' value='true' type='hidden' /><button class='formButton' type='Submit'>BUY NOW</button></form>";
            } else {		

                if(!(isLoggedIn())){
                    echo '<a href="login.php"><button class="formButton">Please login to buy tickets</button></a>';
                } else {

                    $dbh = connectToDatabase();
                    
                    $statement = $dbh->prepare("SELECT SessionID, MovieID, SeatsAvailable, NormalPrice AS Price, SessionTime AS SessionDate, SessionTime FROM Sessions JOIN Movies USING (MovieID) where MovieID = ? AND SessionTime >= ?");
                    $statement->bindValue(1, $MovieID);
                    $statement->bindValue(2, (time() - 30*60));
                    $statement->execute();

                    $stmt = $dbh->prepare("SELECT COUNT(TicketID) AS Taken FROM Sessions JOIN Tickets USING (SessionID) WHERE MovieID = ?");
                    $stmt->bindValue(1, $MovieID);
                    $stmt->execute();
                    $res = $stmt->fetch(PDO::FETCH_ASSOC);
                    $taken = makeOutputSafe($res['Taken']);
                    
                    $last = "";
                    $counter = 0;
                    
                    while($row = $statement->fetch()) {
                        // I am using a counter because otherwise the form below will be the exact same for every button.
                        // It needs to be slightly different to each have different values for the same names...
                        $counter += 1;

                        // This is grabbing all the variables from the row...
                        $SessionID = makeOutputSafe($row['SessionID']);
                        $MovieID = makeOutputSafe($row['MovieID']);
                        $SeatsAvailable = makeOutputSafe($row['SeatsAvailable']) - $taken;
                        $Price = makeOutputSafe($row['Price']);
                        $Price = $Price * 0.9;
                        $SessionDate = makeOutputSafe($row['SessionDate']);
                        $SessionTime = date('h:i a', makeOutputSafe($row['SessionTime']));
                        $RealSessionTime = makeOutputSafe($row['SessionTime']);
                        $today = date('d m', time());
                        $today_month = date('m', time());
                        $session_month = date('m', $SessionDate);
                        
                        //We arent even going to display it if there are no seats left...
                        if (!($SeatsAvailable <= 0)) {

                            // Need to get a better way of doing this so it says the different month if there is too...
                            // Aswell as detect if it is today or tomorrow.
                            switch ($SessionDate) {
                                case date('d m', $SessionDate) == $today:
                                    echo "<h3 class='white-text text-center'>Today</h3>
                                    <hr class='orange-hr' /> ";
                                    break;
                                case $SessionDate == (time() + strtotime('+1 day')):
                                    
                                    echo "<h3 class='white-text' text-center'>Tomorrow</h3>
                                    <hr class='orange-hr' /> ";
                                    break;
                                default:
                                    $Day = date("l", $SessionDate);
                                    if (!($Day == $last)) {
                                        if (!($session_month == $today_month)){
                                            $actualMonth = date("F", $SessionDate);
                                            echo "<h3 class='white-text text-center'>$Day - $actualMonth</h3>";
                                        } else {
                                            echo "<h3 class='white-text text-center'>$Day</h3>";
                                        }
                                        
                                        echo"<hr class='orange-hr' /> ";
                                    }
                                    
                                    break;
                            }
                            echo
                            "
                                <div class='row white-text text-center smb'>
                                    <div class='col-4'>$$Price - </div>
                                    <div class='col-4'>
                                        <form method='POST' name='$counter' id='$counter'>
                                        ";foreach($_GET as $name => $value) {
                                            $name = htmlspecialchars($name);
                                            $value = htmlspecialchars($value);
                                            echo '<input type="hidden" name="'. $name .'" value="'. $value .'">';
                                        } echo"
                                            <input type='hidden' name='SessionID' value='$SessionID' />
                                            <input type='hidden' name='Seats' value='$SeatsAvailable' />
                                            <input type='hidden' name='SessionTime' value='$SessionTime' />
                                            <input type='hidden' name='RealSessionTime' value='$RealSessionTime' />
                                            <input type='hidden' name='Price' value='$Price' />
                                            <button type='submit' name='$counter' class='formButton no-padding'>$SessionTime</button>
                                        </form>
                                    </div>
                                    <div class='col-4'>$SeatsAvailable Seats left</div>
                                </div>
                            <br/>
                            ";
                        }
                    }
                }
            }
        }

	}
}	
?>