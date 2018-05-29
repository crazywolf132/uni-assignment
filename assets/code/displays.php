<?php

  // CODE FOR FRONT PAGE / HOME PAGE...
  function latestMovies() {
    include('helper.php');
    $dbh = connectToDatabase();
    $tomorrow_timestamp = strtotime('tomorrow');

    $statement = $dbh->prepare("SELECT MovieID, Title, EarliestSessionTime AS Time FROM Movies INNER JOIN UpcommingMovies USING (MovieID) WHERE Time >= $tomorrow_timestamp ORDER BY Time ASC LIMIT 12");
    $statement->execute();

    while($row = $statement->fetch()) {

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
      include('helper.php');
      $dbh = connectToDatabase();
      $ID = $_GET['MovieID'];
      $statement = $dbh->prepare("SELECT MovieID, Title, Plot, Classification, Runtime FROM Movies WHERE MovieID = $ID");
      $statement->execute();

      while($row = $statement->fetch()) {
        $MovieID = makeOutputSafe($row['MovieID']);
        $MovieTitle = makeOutputSafe($row['Title']);
        $MoviePlot = makeOutputSafe($row['Plot']);
        $MovieClassification = makeOutputSafe($row['Classification']);
        $Runtime = makeOutputSafe($row['Runtime']);

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
            <br />
            <p class='white-text'>$MoviePlot</p>
            <br />
            <div class='text-center'>
                <h3 class='normal-text white-text'>Rated:   <span id='rating$MovieClassification' class='bold-text'>$MovieClassification</span>   RunTime: <span class='bold-text small-text-margin-left'>$Runtime</span> minutes</h3>
            </div>
            <br />
            <a href='bookMovie.php?MovieID=$MovieID'>
              <div class='primary expand-color smr smb'>
                <h1 class='text-center'>BOOK NOW!</h1>
              </div>
            </a>
          </div>
        </div>";
      }
    }
  }

  function allMovies(){
    include('helper.php');
    $dbh = connectToDatabase();
    if (isset($_GET['Search'])){
      $toSearch = $_GET['Search'];
      echo"<h3 class='primary-text text-center'>Results for \"$toSearch\"<h3>
      <hr class='white-hr' />
      <br />";

      $statement = $dbh->prepare("SELECT MovieID, Title, Plot FROM Movies WHERE Title LIKE '%$toSearch%' OR PLOT LIKE '%$toSearch%' ORDER BY Title");
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
      $statement = $dbh->prepare("SELECT MovieID, Title FROM Movies ORDER BY Title ASC LIMIT 24 OFFSET $Total");
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
    include('helper.php');
    $dbh = connectToDatabase();
    $currentTime = time();
    $tomorrow = strtotime("+1 week");
    $today = date("d m Y");
    $statement = $dbh->prepare("
    SELECT
    MovieID,
    Movies.Title AS MovieTitle,
    Movies.Classification,
    Sessions.RoomID,
    STRFTIME('%H:%M', Sessions.SessionTime, 'unixepoch',  'localtime') AS SessionTime,
    STRFTIME('%d %m %Y', SessionTime, 'unixepoch',  'localtime') AS SessionDate,
    Sessions.NormalPrice
    FROM Movies
    INNER JOIN Sessions USING (MovieID)
    WHERE SessionTime >= $currentTime AND SessionTime <= $tomorrow
    ");
    $statement->execute();
    // Remove any containing tomorrows date...
    while($row = $statement->fetch()) {
      $MovieID = makeOutputSafe($row['MovieID']);
      $epoch = makeOutputSafe($row['SessionTime']);
      $Date = makeOutputSafe($row['SessionDate']);
      //$epoch = 1344988800;
      //$dt = new DateTime("@$epoch");
      //$MovieTime = $dt->format('Y-m-d H:i:s');
      $MovieTime = $epoch;
      $Color = "tomorrow";

      if ($Date == $today){
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
      }
    }
  }

  

  function purchaseTicket() {
    include('helper.php');
    $dbh = connectToDatabase();
    if (isset($_GET['Session']) && isset($_GET['PricePaid'])) {
      $session = $_GET['Session'];
      $pricePaid = $_GET['PricePaid'];
      $timeStamp = time();
      
      $statement = $dbh->prepare("
      INSERT INTO TICKETS (SessionID, PricePaid, TimeStamp)
      VALUES(?, ?, ?);
      ");
      $statement->bindValue(1, $session);
      $statement->bindValue(2, $pricePaid);
      $statement->bindValue(3, $timeStamp);
      $statement->execute();
      //TODO ALSO INSERT INTO THE MEMBER TICKETS COLUMN...
      echo "<h1 class='green-around-text'>Shit Works - You just bought a ticket to $session for $$pricePaid</h1>";
    } else {
      echo "<h1 class='red-around-text'>Woooah, backup... shit no work</h1>";
    }
  }
  
  function getMovieReviews() {
    $dbh = connectToDatabase();
    $MovieID = $_GET['MovieID'];
    $statement = $dbh->prepare("SELECT MemberID, UserName, StarRating, ReviewText, TimeStamp AS Date FROM Reviews INNER JOIN Members USING (MemberID) where MovieID = $MovieID ORDER BY StarRating DESC LIMIT 10");
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
