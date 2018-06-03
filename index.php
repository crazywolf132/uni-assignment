<?php
  include('assets/code/login/handler.php');
  $cookieMessage = getCookieMessage();
  if (isLoggedIn()) {
    $user = loadUser();
  }
?>
<html>
  <?php include('assets/displays/head.php'); ?>
  <body>
    <div class='row'>
      <div class='col-3 hide-tablet hide-mobile'></div>
      <div id='content' class='col-6 liftOff'>

        <?php include('assets/displays/logo.php'); ?>

        <section id='nav'>
          <nav class='hide-mobile'>
            <ul>
              <a href="#"><li id='active'>HOME</li></a>
              <a href="sessions.php"><li>SESSIONS</li></a>
              <a href="all.php"><li>ALL</li></a>
              <a href="report.php"><li>REPORT</li></a>
              <?php 
                loadNavChange();
              ?>
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

        <section id='core'>
          <?php
            if ($cookieMessage){
              echo '<a href="#"><div class="blue-around-text text-center white-text smr sml smb"><h3>'. $cookieMessage .'</h3></div></a>';
            }
          ?>
          <h3 class='primary-text text-center'>Latest Releases<h3>
          <hr class='white-hr' />
          <br />
          <div class='movie-row'>
            <?php include('./assets/code/displays.php'); latestMovies(); ?>
          </div>
        </section>

      </div>
      <div class='col-3 hide-tablet hide-mobile'></div>
    </div>
  </body>
</html>
