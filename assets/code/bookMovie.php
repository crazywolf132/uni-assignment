<?php

/**
 * As this is a fairly-large function... as is handles everything. I will try to comment it as best as possible.
 * 
 * There are mainly 3 Sections to it.
 * 
 * First is the Session Displayer (This is all the way at the bottom of the function).
 *      This is used to display all the upcomming sessions for the movie, and allow you to select one to purchase
 *      a ticket for that session.
 * 
 * Second Section is the Selected Session Displayer (This is in the middle of the function).
 *      This section is for showing the user the session time they have selected, along with providing them
 *      with either a message saying they need to login to book a ticket, or provide a form asking for the
 *      amount of tickets they wish to purchase...
 * 
 * Third Section is the Purchase Handler (This is closer to the top of the file).
 *      This section is used for creating the connection to the database to register the ticket(s) that
 *      the user has just proceeded to purchase.
 * 
 * All The secions will have a label so it is easy(er) to find them.
 */

function showMovieTimes() {
	if (isset($_GET['MovieID'])) {
        /**
         * This the head of the function. Every sub-function inside of here needs to use the gathered $_GET data.
         * They also need to be within the framework built below with the echo statement.
         */
        $MovieID = $_GET['MovieID'];
		(isset($_GET['MovieName'])) ? $MovieTitle = $_GET['MovieName'] : header("Location: ViewMovie.php?MovieID=$MovieID");
		(isset($_GET['Rating'])) ? $MovieRating = $_GET['Rating'] : header("Location: ViewMovie.php?MovieID=$MovieID");
		(isset($_GET['RunTime'])) ? $MovieRuntime = $_GET['RunTime'] : header("Location: ViewMovie.php?MovieID=$MovieID");
        
        // This echo is very imporant. As it setups the foundations for the below sub-functions to display their data in.
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
        /**
         * Welcome to section 3. Please refer to above for more information.
         */
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

            // This is collecting the information on how many tickets the user is purchasing.
            $amount = makeOutputSafe($_POST['amount']);

            // if the session is more than 30 minutes in the past... dont allow a purchase.
            if ($_POST['RealSessionTime'] < (time() - 30*60)){
                setCookieMessage("Sorry, the session has closed and is no longer accepting purchases");
                redirect("ViewMovie.php?MovieID=".$_POST['MovieID']);
            }

            // We Are now creating a for-loop to add however many tickets the user has purchased, to the database.
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
            // We are now commiting our changes to the database.
            $dbh->commit();
            // Letting the user know all is good.
            setCookieMessage("Order Success!! Click here to view your ticket.$$".$lastID);
            redirect("ViewMovie.php?MovieID=".$_POST['MovieID']);

        } else {

            /**
             * Welcome to section 2! For more information... Go to top of this file.
             */
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
                
                // This echo is just used to display the information from the selected session time, that the user has chosen.
                echo
                "
                <div class='row white-text text-center smb'>
                    <div class='col-4'>$$SessionPrice each </div>
                    <div class='col-4'><a href='#' class='ticketButton-selected text-center'> $SessionTime </a></div>
                    <div class='col-4'>$Seats Seats left</div>
                </div>
                ";
                
                // We are now creating the form that the user must fillout to complete the transaction.
                // There is a forEach loop inside of here to add the hidden information automatically
                // so then it makes it easier and more precise to know what movie we are purchasing a ticket for.
                
                echo 
                "
                <form method='post' class='smr'>
                ";foreach($_POST as $name => $value) {
                            $name = htmlspecialchars($name);
                            $value = htmlspecialchars($value);
                            echo '<input type="hidden" name="'. $name .'" value="'. $value .'">';
                        } echo"
                    <input name='amount' type='number' placeholder='Amount' class='inputBox red-outline' max='$Seats' min='1' required='true' />
                "; $user = loadUser();
                echo "<input name='username' value='". $user['FirstName'] ."' type='hidden' />";
                echo"<input name='PURCHASE_MODE' value='true' type='hidden' /><button class='formButton' type='Submit'>BUY NOW</button></form>";

            } else {
                
                /**
                 * Welcome to section 1. The begginning of it all. Read the top of this file for more information.
                 */

                if(!(isLoggedIn())){
                    echo '<a href="login.php"><button class="formButton">Please login to buy tickets</button></a>';
                } else {

                    $dbh = connectToDatabase();
                    
                    $statement = $dbh->prepare("SELECT SessionID, MovieID, SeatsAvailable, NormalPrice AS Price, SessionTime AS SessionDate, SessionTime FROM Sessions JOIN Movies USING (MovieID) where MovieID = ? AND SessionTime >= ? ORDER BY SessionDate ASC");
                    $statement->bindValue(1, $MovieID);
                    $statement->bindValue(2, (time() - 30*60));
                    $statement->execute();

                    
                    $last = "";
                    $counter = 0;
                    
                    while($row = $statement->fetch()) {
                        // I am using a counter because otherwise the form below will be the exact same for every button.
                        // It needs to be slightly different to each have different values for the same names...
                        $counter += 1;

                        // This is grabbing all the variables from the row...
                        $SessionID = makeOutputSafe($row['SessionID']);
                        $MovieID = makeOutputSafe($row['MovieID']);
                        
                        $Price = makeOutputSafe($row['Price']);
                        $Price = $Price * 0.9;
                        $SessionDate = makeOutputSafe($row['SessionDate']);
                        $SessionTime = date('h:i a', makeOutputSafe($row['SessionTime']));
                        $RealSessionTime = makeOutputSafe($row['SessionTime']);
                        $today = date('d m', time());
                        $today_month = date('m', time());
                        $session_month = date('m', $SessionDate);
                        
                        $stmt = $dbh->prepare("SELECT COUNT(TicketID) AS Taken FROM Sessions JOIN Tickets USING (SessionID) WHERE SessionID = ?");
                        $stmt->bindValue(1, $SessionID);
                        $stmt->execute();
                        $res = $stmt->fetch(PDO::FETCH_ASSOC);
                        $taken = makeOutputSafe($res['Taken']);

                        $SeatsAvailable = makeOutputSafe($row['SeatsAvailable']) - $taken;

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
                                    $displayDate = date("l - jS F", $SessionDate);
                                    echo "<h3 class='white-text text-center'>".$displayDate."</h3>";
                                    echo"<hr class='orange-hr' /> ";
                                    break;
                            }
                            // This is just used to display the session on the page.
                            // The reason why it is so long is because we are actually using the session time
                            // as a button a form... Mainly so we dont have such long read-able urls...
                            // and to make it slightly safer to do the transactions and stop people from 
                            // creating systems to automatically purchase the tickets...
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