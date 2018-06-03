<?php
	include('assets/code/login/handler.php');
	if (isLoggedIn()) {
		$user = loadUser();
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
              <a href="index.php"><li>HOME</li></a>
              <a href="sessions.php"><li id='active'>SESSIONS</li></a>
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

        <section id='core'>
          <?php include("./assets/code/bookMovie.php"); showMovieTimes(); ?>
		  </div>
        </section>

      </div>
    </div>
  </body>
</html>
