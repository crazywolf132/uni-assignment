<?php

  /** THIS IS THE FUNCTION USED FOR SEARCHING FOR MOVIES **/
  function findMovie() {
    if (isset($_GET['search'])) {
      include('helper.php');
      $dbh = connectToDatabase();
      $toSearch = $_GET['search'];
      $statement = $dbh->prepare('SELECT MovieID, Plot, Title, Classification FROM Movies WHERE Title LIKE ? OR Plot LIKE ?');

      $statement->execute();
    }
  }

  /** THIS FUNCTION IS USED FOR THE SESSIONS.PHP PAGE **/
  function getShowing(){
    include('helper.php');
    $dbh = connectToDatabase();
    $statement = $dbh->prepare('SELECT MovieID, Plot, Title, Classification, EarliestSessionTime FROM Movies INNER JOIN UpcommingMovies USING (MovieID) WHERE NoSessionScheduled = 0 ORDER BY EarliestSessionTime ASC LIMIT 5');

    $statement->execute();

    while($row = $statement->fetch()) {
      $MovieID = makeOutputSafe($row['MovieID']);
      $MovieTitle = makeOutputSafe($row['Title']);
      $MovieClassification = makeOutputSafe($row['Classification']);
      $MoviePlot = makeOutputSafe($row['Plot']);
      $time = makeOutputSafe($row['EarliestSessionTime']);
      $time = date('d/m/Y', $time);

      $MovieClassification == "PG-13" ? $MovieClassification = "PG" : $MovieClassification = $MovieClassification;
      $MoviePlot = trim($MoviePlot);

      echo "<div id='movie'>
        <div id='details'>
          <img src='assets/img/movies/$MovieID.jpg' />
          <h4 id='TitleActual'>$MovieTitle</h4>
          <br />
          <h4 id='rating$MovieClassification'>$MovieClassification</h4>
          <br />
          <div id='genres'>
            <h6 class='round'>Action</h6><h6 class='round'>Horror</h6>
          </div>
          <br />
          <p>$MoviePlot</p>
          <br />
          <div id='sessionButtons'>
            <a href='#' id='button' class='curved red'>
              $time
            </a>
            <a href='#' id='button' class='curved red'>
              1pm
            </a>
            <a href='#' id='button' class='curved yellow'>
              2pm
            </a>
          </div>
        </div>
      </div>";
    }
  }

  /** THIS IS THE FUNCTION FOR THE ALLMOVIES.PHP PAGE **/
  function getAll() {
    include('helper.php');
    $dbh = connectToDatabase();
    $statement = $dbh->prepare('SELECT MovieID, Title FROM Movies ORDER BY Title ASC LIMIT 50');
    $statement->execute();

    while($row = $statement->fetch()){
      $MovieID = makeOutputSafe($row['MovieID']);
      echo "<div id='movieItem'>
        <a href='ViewMovie.php?MovieID=$MovieID'>
          <div class='sameImg'>
            <img src='assets/img/movies/$MovieID.jpg' id='movieImage' class='shadowed grow'/>
          </div>
        </a>
      </div>";
    }
  }


  /** THIS IS THE FUNCTION FOR THE INDEX.PHP PAGE **/
  function getSoon() {
    include('helper.php');
    $dbh = connectToDatabase();
    $tomorrow_timestamp = strtotime('tomorrow');
    $statement = $dbh->prepare("SELECT MovieID, Title, Plot, Classification, EarliestSessionTime AS Time FROM Movies INNER JOIN UpcommingMovies USING (MovieID) WHERE Time >= $tomorrow_timestamp ORDER BY Time ASC LIMIT 12");
    $statement->execute();

    while($row = $statement->fetch()) {
      // Getting the info from the statement.
      $MovieID = makeOutputSafe($row['MovieID']);
      $MovieTitle = makeOutputSafe($row['Title']);
      $MoviePlot = makeOutputSafe($row['Plot']);
      $MovieClassification = makeOutputSafe($row['Classification']);
      $MovieTime = makeOutputSafe($row['Time']);
      //Correcting issues with database data and themeing...
      $MovieClassification == "PG-13" ? $MovieClassification = "PG" : $MovieClassification = $MovieClassification;
      //Setting default color.
      $MovieColor = 'green';

      if ($MovieTime >= strtotime("tomorrow") && $MovieTime <= strtotime("+2 day")){
        $MovieTime = "Tomorrow";
      } elseif ($MovieTime <= strtotime("+2 week")) {
      	$MovieColor = 'yellow';
      	$MovieTime = date('d M', $MovieTime);
      } else {
      	$MovieColor = 'red';
      	$MovieTime = date('d M', $MovieTime);
      }

      echo "<div id='movie'>
        <div id='details'>
          <img src='assets/img/movies/$MovieID.jpg' />
          <h4 id='TitleActual'>$MovieTitle</h4>
          <br />
          <h4 id='rating$MovieClassification'>$MovieClassification</h4>
          <br />
          <div id='genres'>
            <h6 class='round'>Action</h6><h6 class='round'>Horror</h6>
          </div>
          <br />
          <p>$MoviePlot</p>
          <br />
          <div id='sessionButtons'>
            <a href='#' id='button' class='curved $MovieColor'>
              $MovieTime
            </a>
          </div>
        </div>
      </div>";
    }
  }

  /** THIS FUNCTION IS USED BY VIEWMOVIE.PHP **/
  function getSingleMovie() {
    if (isset($_GET['MovieID'])) {
      include('helper.php');
      $dbh = connectToDatabase();
      $ID = $_GET['MovieID'];
      $statement = $dbh->prepare("SELECT MovieID, Title, Plot, Classification FROM Movies WHERE MovieID = $ID");
      $statement->execute();
      while($row = $statement->fetch()) {
        $MovieID = makeOutputSafe($row['MovieID']);
        $MovieTitle = makeOutputSafe($row['Title']);
        $MoviePlot = makeOutputSafe($row['Plot']);
        $MovieClassification = makeOutputSafe($row['Classification']);

        $MovieClassification == 'PG-13' ? $MovieClassification = "PG" : $MovieClassification = $MovieClassification;

        echo "<div id='movie' class='expand'>
          <div id='details'>
            <img src='assets/img/movies/$MovieID.jpg' />
            <h4 id='TitleActual'>$MovieTitle</h4>
            <br />
            <h4 id='rating$MovieClassification'>$MovieClassification</h4>
            <br />
            <div id='genres'>
              <h6 class='round'>Action</h6><h6 class='round'>Horror</h6>
            </div>
            <br />
            <p>$MoviePlot</p>
            <br />
            <div id='sessionButtons'>
              <a href='#' id='button' class='curved yellow'>
                12:45pm
              </a>
              <a href='#' id='button' class='curved green'>
                1pm
              </a>
              <a href='#' id='button' class='curved red'>
                2pm
              </a>
            </div>
          </div>
        </div>";
      }

    } else {
      header('Location: index.php');
      die();
    }
  }
