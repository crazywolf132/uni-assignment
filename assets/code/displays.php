<?php

  // CODE FOR FRONT PAGE / HOME PAGE...
  function latestMovies() {
    $dbh = connectToDatabase();
    $tomorrow_timestamp = strtotime('tomorrow');
    $stmt = $dbh->prepare("SELECT MovieID, Title, EarliestSessionTime AS Time FROM Movies INNER JOIN UpcommingMovies USING (MovieID) WHERE Time >= $tomorrow_timestamp ORDER BY TIME ASC LIMIT 12");
    $stmt->execute();
    // I spent a solid hour trying to get this statement working with the $stmt->bindValue(1, $tomorrow_timestamp); feature...
    // ... it just would not work. It would only work if i was showing movies from before tomorrow_timestamp. At 4 am... i am not mentally prepared
    // for this crap. It is not a user input so it should be fine to just put in the statement. It does not compromise the database integrity at all.

    
    while($row = $stmt->fetch()) {
      
      // Getting the info from the statement.
      $MovieID = makeOutputSafe($row['MovieID']);
      $MovieTitle = makeOutputSafe($row['Title']);
      $MovieTime = makeOutputSafe($row['Time']);
      $Text = "";
      $Style = "";

      if ($MovieTime >= strtotime("tomorrow") && $MovieTime <= strtotime("+2 day")) {
        $Text = "Tomorrow";
        $Style = "tomorrow";
      } elseif ($MovieTime <= strtotime("+2 week")) {
        $Text = "Next Week";
        $Style = "next-week";
      } else {
        $Text = "Two Weeks";
        $Style = "two-weeks";
      }

      echo "<div class='movieItem col-3'>
        <a href='ViewMovie.php?MovieID=$MovieID'><div class='text-image curved grow liftOff'>
          <img class='movie-img' src='assets/img/movies/$MovieID.jpg' alt='$MovieTitle' />
          <div class='$Style'>$Text</div>
        </div></a>
      </div>";
    }
  }

  function getMovieByID() {
    if (isset($_GET['MovieID'])) {
      $dbh = connectToDatabase();
      $ID = $_GET['MovieID'];
      $SessionStatement = $dbh->prepare("SELECT COUNT(SessionID) AS Amount FROM Sessions WHERE MovieID = ? AND SessionTime >= ?;");
      $statement = $dbh->prepare("SELECT MovieID, Title, Plot, Classification, Runtime, Genre FROM Movies INNER JOIN MovieGenre USING (MovieID) WHERE MovieID = $ID");
      $SessionStatement->bindValue(1, $ID);
      $SessionStatement->bindValue(2, (time() - 30*60));
      //$statement->bindValue(1, $ID);
      $SessionStatement->execute();
      $statement->execute();
      

      //Declaring some variables so they can appear in the second Statement too...
      $MovieTitle = "";
      $MovieClassification = "";
      $RunTime = "";
      $row = $statement->fetch(PDO::FETCH_ASSOC);
      if($row) {
        $MovieID = makeOutputSafe($row['MovieID']);
        $MovieTitle = makeOutputSafe($row['Title']);
        $MoviePlot = makeOutputSafe($row['Plot']);
        $MovieClassification = makeOutputSafe($row['Classification']);
        $Runtime = makeOutputSafe($row['Runtime']);
        $unSplitGenre = makeOutputSafe($row['Genre']);
        $lots = false;
        if (stringContains($unSplitGenre, ",")) {
          $lots = true;
          $allGenres = explode(",", $unSplitGenre);
        }
        $MovieClassification == 'PG-13' ? $MovieClassification = "PG" : $MovieClassification = $MovieClassification;
        $MovieClassification == 'TV-MA' || $MovieClassification == 'TV-14' ? $MovieClassification = "MA" : $MovieClassification = $MovieClassification;
        $MovieClassification == 'UNRATED' || $MovieClassification == 'N/A' || $MovieClassification == 'NOT RATED' ? $MovieClassification = "U" : $MovieClassification = $MovieClassification;
        echo "<div class='row'>
          <div class='col-6 no-padding'>
            <div class='row'>
              <div class='col-1'></div>
              <div class='col-10 no-padding smb'><img class='liftOff expandIMG' src='assets/img/movies/$MovieID.jpg'></div>
              <div class='col-1'></div>
            </div>
          </div>
          <div class='col-6 expandMe'>
            <div class='white expand-color smr'>
              <h2 class='text-center'>$MovieTitle</h2>
            </div>
            <h4 class='text-center'>";
              if($lots) {
                foreach($allGenres as $item) {
                  echo "<span class='blue-around-text'>" .$item. " </span>";
                }
              }else{ 
                echo "<span class='blue-around-text'>".$unSplitGenre."</span>";
              }
            echo"</h4>
            <br />
            <p class='white-text'>$MoviePlot</p>
            <br />
            <div class='text-center'>
                <h3 class='normal-text white-text'>Rated:   <span id='rating$MovieClassification' class='bold-text'>$MovieClassification</span>   RunTime: <span class='bold-text small-text-margin-left'>$Runtime</span> minutes</h3>
            </div>
            ";
      }
      while ($row=$SessionStatement->fetch()){
        $SessionCount = makeOutputSafe($row['Amount']);

        if (!($SessionCount == 0)) {
          echo "
          <br />
          <a href='bookMovie.php?MovieID=$MovieID&MovieName=$MovieTitle&Rating=$MovieClassification&RunTime=$Runtime'>
            <div class='primary expand-color smr smb'>
              <h1 class='text-center'>BOOK NOW!</h1>
            </div>
          </a>
          ";
        } else {
          echo "
          <br />
          <div class='primary expand-color smr smb'>
            <h1 class='text-center'>NO MORE SESSIONS!</h1>
          </div>
          ";
        }
      }
      echo"
          </div>
        </div>";
      // The only reason we get the session Count seperately... is because otherwise we wont be able to load the other information.
    }
  }

  function allMovies(){
    $dbh = connectToDatabase();

    if (isset($_GET['Search'])){

      $toSearch = $_GET['Search'];
      $theGenre = $_GET['genre'];
      if ($toSearch){
        echo"<h3 class='primary-text text-center'>Results for \"$toSearch\"<h3>";
      } else {
        echo"<h3 class='primary-text text-center'>Results for \"$theGenre\"<h3>";
      }
     echo "
      <hr class='white-hr' />
      <br />";

      $statement="";
      if ($theGenre && $toSearch){
        $statement = $dbh->prepare("SELECT MovieID, Title, Plot FROM Movies INNER JOIN MovieGenre USING (MovieID) WHERE (Title LIKE ? OR PLOT LIKE ?) AND Genre LIKE ? ORDER BY Title");
        $statement->bindValue(1, "%$toSearch%");
        $statement->bindValue(2, "%$toSearch%");
        $statement->bindValue(3, "%$theGenre%");
      } else if ($theGenre && !($toSearch)) {
        $statement = $dbh->prepare("SELECT MovieID, Title, Plot FROM Movies INNER JOIN MOvieGenre USING (MovieID) WHERE Genre LIKE ? ORDER BY Title;");
        $statement->bindValue(1, "%$theGenre%");
      }else {
        $statement = $dbh->prepare("SELECT MovieID, Title, Plot FROM Movies WHERE Title LIKE ? OR PLOT LIKE ? ORDER BY Title");
        $statement->bindValue(1, "%$toSearch%");
        $statement->bindValue(2, "%$toSearch%");
      }
      
      
     
      $statement->execute();

      while($row = $statement->fetch()) {
        $MovieID = makeOutputSafe($row['MovieID']);
        $MovieTitle = makeOutputSafe($row['Title']);

        echo "<div class='movieItem col-3'>
          <a href='ViewMovie.php?MovieID=$MovieID'><div class='text-image curved grow liftOff'>
            <img class='movie-img' src='assets/img/movies/$MovieID.jpg' alt='$MovieTitle' />
          </div></a>
        </div>";
      }
      return 1;

    } else {

      (isset($_GET['Page'])) ? $PageNum = $_GET['Page'] : $PageNum = 1;
      $Total = 24 * $PageNum;
      $Next = ($PageNum - 1) * 24 + 24;
      ($Total == 24) ? $Prev = 1 : $Prev = ($PageNum - 1) * 24;
      echo"<h3 class='primary-text text-center'>Movies $Prev - $Next</h3>
      <hr class='white-hr' />
      <br />";

      $statement = $dbh->prepare("SELECT MovieID, Title FROM Movies ORDER BY Title ASC LIMIT 24 OFFSET ?");
      $statement->bindValue(1, $Total);
      $statement->execute();
      

      while ($row = $statement->fetch()) {
        $MovieID = makeOutputSafe($row['MovieID']);
        $MovieTitle = makeOutputSafe($row['Title']);

        echo 
        "<div class='movieItem col-3'>
          <a href='ViewMovie.php?MovieID=$MovieID'><div class='text-image curved grow liftOff'>
            <img class='movie-img' src='assets/img/movies/$MovieID.jpg' alt='$MovieTitle' />
          </div></a>
        </div>";
      }
      
      return $PageNum;
    }
  }

  function moviesShowing() {
    $dbh = connectToDatabase();
    $currentTime = time();
    $tomorrow = strtotime("+1 week");
    $today = date("d m Y");
    $statement = $dbh->prepare("
    SELECT
    MovieID,
    Title AS MovieTitle,
    Classification,
    RoomID,
    STRFTIME('%H:%M', SessionTime, 'unixepoch',  'localtime') AS SessionTime,
    STRFTIME('%d %m %Y', SessionTime, 'unixepoch',  'localtime') AS SessionDate,
    NormalPrice
    FROM Movies
    INNER JOIN Sessions USING (MovieID)
    WHERE SessionTime >= $currentTime AND SessionTime <= $tomorrow
    GROUP BY MovieID
    ORDER BY Classification ASC
    ");
    $statement->execute();
    // Remove any containing tomorrows date...
    while($row = $statement->fetch()) {
      $MovieID = makeOutputSafe($row['MovieID']);
      $epoch = makeOutputSafe($row['SessionTime']);
      $Date = makeOutputSafe($row['SessionDate']);
      $MovieClassification = makeOutputSafe($row['Classification']);
      //$epoch = 1344988800;
      //$dt = new DateTime("@$epoch");
      //$MovieTime = $dt->format('Y-m-d H:i:s');
      $MovieClassification == 'PG-13' ? $MovieClassification = "PG" : $MovieClassification = $MovieClassification;
      $MovieClassification == 'TV-MA' || $MovieClassification == 'TV-14' ? $MovieClassification = "MA" : $MovieClassification = $MovieClassification;
      $MovieClassification == 'UNRATED' || $MovieClassification == 'N/A' || $MovieClassification == 'NOT RATED' ? $MovieClassification = "U" : $MovieClassification = $MovieClassification;
      $MovieTime = $epoch;
      $Color = "tomorrow";

      echo 
      "
      <div class='movieItem col-3'>
        <a href='ViewMovie.php?MovieID=$MovieID'>
          <div class='text-image curved grow liftOff'>
            <img src='assets/img/movies/$MovieID.jpg' alt='$MovieID' class='movie-img' />
            <div class='ratingOverlay-$MovieClassification'>$MovieClassification</div>
          </div>
        </a>
      </div>
      ";

      /*if ($Date == $today){
        echo "<div class='movieItem col-3'>
          <a href='ViewMovie.php?MovieID=$MovieID'><div class='text-image curved grow liftOff'>
            <img class='movie-img' src='assets/img/movies/$MovieID.jpg' alt='$MovieID' />
            <div class='$Color'>$MovieTime</div>
          </div></a>
        </div>";
      } elseif ($today >= date("d m Y", strtotime('+1 days'))){
        echo "<div class='movieItem col-3'>
          <a href='ViewMovie.php?MovieID=$MovieID'><div class='text-image curved grow liftOff'>
            <img class='movie-img' src='assets/img/movies/$MovieID.jpg' alt='$MovieID' />
            <div class='next-week'>Tomorrow</div>
          </div></a>
        </div>";
      } else {
        echo "<div class='movieItem col-3'>
          <a href='ViewMovie.php?MovieID=$MovieID'><div class='text-image curved grow liftOff'>
            <img class='movie-img' src='assets/img/movies/$MovieID.jpg' alt='$MovieID' />
            <div class='two-weeks'>Another Day</div>
          </div></a>
        </div>";
      }*/
    }
  }

  

  function purchaseTicket() {
    include('helper.php');
    $dbh = connectToDatabase();
    if (isset($_GET['Session']) && isset($_GET['PricePaid'])) {
      $session = $_GET['Session'];
      $pricePaid = $_GET['PricePaid'];
      $timeStamp = strtotime('today UTC -10');
      
      echo $today = date('d/m/Y h:i a e', strtotime('today UTC+10:00'));
      echo $today_midnight = strtotime('today UTC+10:00');

      echo"<br><br>";
      echo date("d/m/Y h:i a");
      echo"<br><br>";

      date_default_timezone_set('Australia/Sydney');
      echo date('Y-m-d H:i:a') . '<br/>';



      $statement = $dbh->prepare("
      INSERT INTO TICKETS (SessionID, PricePaid, TimeStamp)
      VALUES(?, ?, ?);
      ");
      $statement->bindValue(1, $session);
      $statement->bindValue(2, $pricePaid);
      $statement->bindValue(3, $timeStamp);
      //$statement->execute();
      //TODO ALSO INSERT INTO THE MEMBER TICKETS COLUMN...
      echo "<h1 class='green-around-text'>Shit Works - You just bought a ticket to $session for $$pricePaid</h1>";
    } else {
      echo "<h1 class='red-around-text'>Woooah, backup... shit no work</h1>";
    }
  }
  
  function getMovieReviews() {
    $dbh = connectToDatabase();
    $MovieID = $_GET['MovieID'];
    $statement = $dbh->prepare("SELECT MemberID, UserName, StarRating, ReviewText, TimeStamp AS Date FROM Reviews INNER JOIN Members USING (MemberID) where MovieID = ? ORDER BY StarRating DESC LIMIT 10");
    $statement->bindValue(1, $MovieID);
    $statement->execute();

    while($row = $statement->fetch()){
      $Text = makeOutputSafe($row["ReviewText"]);
      $Username = makeOutputSafe($row["UserName"]);
      $Stars = makeOutputSafe($row["StarRating"]);
      $Date = date("jS M Y",makeOutputSafe($row["Date"]));

      echo 
        "<section id='reviews' class='smb'>
          <div class='row'>
            <div class='col-2'></div>
            <div class='col-8 white-text'>
              <div class='inline'>
                <h3>$Username <span class='green-around-text'>on $Date</span></h3>
              </div>
              <p>$Text</p>
              <div class='stars'>
                <span class='blue-around-text'>$Stars out of 5 stars</span>
              </div>
            </div>
            <div class='col-2'></div>
          </div>
        </section>";
    }
  }


?>
