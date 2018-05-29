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

        <section id='core'>
          <?php include("./assets/code/bookMovie.php"); bookMovie(); ?>
        </section>

      </div>
    </div>
  </body>
</html>
