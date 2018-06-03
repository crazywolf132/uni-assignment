<?php
  include_once('assets/code/login/handler.php');

  $cookieMessage = getCookieMessage();

  if(isLoggedIn()) {
    $user = loadUser();
  }

  //UP HERE WE WILL HANDLE THE PROCESSING OF THE MOVIE REVIEW...
  if (isset($_POST['comment']))
  {
    $dbh = connectToDatabase();
    $stmt = $dbh->prepare("INSERT INTO Reviews (MemberID, MovieID, StarRating, TimeStamp, ReviewText) VALUES(?, ?, ?, ?, ?)");
    $stmt->bindValue(1, makeOutputSafe($_POST['userID']));
    $stmt->bindValue(2, makeOutputSafe($_GET['MovieID']));
    $stmt->bindValue(3, makeOutputSafe($_POST['rating']));
    $stmt->bindValue(4, time());
    $stmt->bindValue(5, makeOutputSafe($_POST['comment']));
    $stmt->execute();

    setCookieMessage("Review Added!");
    redirect("ViewMovie.php?MovieID=".$_GET['MovieID']);
  }
?>
<html>
  <?php include('assets/displays/head.php'); ?>
  <body>
    <div class='row'>
      <div class='col-3 hide-mobile'></div>
      <div id='content' class='col-6 liftOff'>

        <?php include('assets/displays/logo.php'); ?>

        <section id='nav'>
          <nav class='hide-mobile'>
            <ul>
              <a href="index.php"><li id='active'>HOME</li></a>
              <a href="sessions.php"><li>SESSIONS</li></a>
              <a href="all.php"><li>ALL</li></a>
              <a href="report.php"><li>REPORT</li></a>
              <?php loadNavChange(); ?>
            </ul>
          </nav>
          <nav class='mobile-only'>
            <ul>
              <a href="#">CLICK ME</a>
            </ul>
          </nav>
        </section>

        <?php include('assets/displays/searchBar.php'); ?>
        <br />

        <?php 
          if ($cookieMessage) {
            $message = "";
            $ticketID = "";
            $link = "";
            if (stringContains($cookieMessage, "$$") == 1) {
              $tokens = explode("$$", $cookieMessage);
              $message = $tokens[0];
              $ticketID = $tokens[1];
            } else {
              $message = $cookieMessage;
            }
            if ($ticketID){
              if (isLoggedIn()){
                $link = "user.php?Screen=4";
              } else {
                $link = "ViewTicket.php?TicketID=".$ticketID;
              }
            }
              echo '<a href="'. $link .'"><div class="green-around-text text-center white-text smr sml smb"><h3>'. $message .'</h3></div></a>';
            }
        ?>

        <section id='core'>
          <?php include('./assets/code/displays.php'); getMovieByID(); ?>
        </section>
		
		<section id='reviewHolder' class='smb'>
			<?php getMovieReviews(); ?>
			<section id='reviewBox'>
				<div id="reviews">
          <?php
            if (!(isLoggedIn())) {
              echo '<div class="text-center green-text"><h1>PLEASE LOGIN TO SUBMIT A REVIEW!</h1></div>';
            } else {
              echo '<form method="post" id="reviewNew">
              <div class="row">
                <div class="col-1"></div>
                <div class="col-10">
                  <div class="row">
                    <div class="col-6 text-left no-padding">
                      <h3 class="white-text">'. $user['UserName'] .'</h3>
                    </div>
                    <div class="col-6 text-right no-padding smt">
                      <label for="rating" class="white-text">Star Rating: </label>
                      <select name="rating" id="rating" class="drop-down">
                        <option value="1">1</option>
                        <option value="2">2</option>
                        <option value="3">3</option>
                        <option value="4">4</option>
                        <option value="5">5</option>
                      </select>
                    </div>
                  </div>
                  <textarea name="comment" class="text-area" form="reviewNew" placeholder="Enter review here..."></textarea>
                  <input name="userID" value="'. $user['MemberID'] .'" type="hidden" />
                  <button type="submit" class="formButton smt">Submit</button>
                </div>
                <div class="col-1"></div>
                
              </div>
                
              </form>
              ';
            }
          ?>
          
        </div>
			</section>
		</section>

      </div>
    </div>
  </body>
</html>
