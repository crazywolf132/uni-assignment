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
              <a href="login.php"><li>LOGIN</li></a>
            </ul>
          </nav>
          <nav class='mobile-only'>
            <ul>
              <a href="#">CLICK ME</a>
            </ul>
          </nav>
        </section>

        <section id='search'>
          <div class='row'>
            <div class='col-1'></div>
            <div class='col-10'>
              <form action='all.php' method='get'>
                <input type='text' name='Search' placeholder="Search For Movie..." id='searchBar' class='searchBar' />
              </form>
            </div>
            <div class='col-1'></div>
          </div>
        </section>
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
