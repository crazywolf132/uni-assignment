<?php include('assets/code/login/handler.php'); ?>
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
              <a href="sessions.php"><li>SESSIONS</li></a>
              <a href="#"><li id='active'>ALL</li></a>
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
        echo
        "<section id='core'>
          <div class='movie-row'>";
            include('assets/code/displays.php');
            $results = allMovies(); 
        echo
          "</div>
        </section>";
        
        ?>

        <div class="row">
          
          <?php
            $next = $results + 1;
            ($results == 0) ? $last=0 : $last = $results - 1;
            echo"
            <div class='col-6 text-center'><a href='?Page=$last'><button class='formButton'>Prev</button></a></div>
            <div class='col-6 text-center'><a href='?Page=$next'><button class='formButton'>Next</button></a></div>
          "; ?>

        </div>

      </div>
    </div>
  </body>
</html>
