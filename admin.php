<?php include('assets/displays/admin.php'); include('assets/code/helper.php'); ?>
<html>
  <?php include('assets/displays/head.php'); ?>
  <body id='adminBackground'>
    <div class='row'>
      <div class='col-2 hide-mobile'></div>
      <div id='content' class='col-8 liftOff'>

        <?php include('assets/displays/logo.php'); ?>

        <section id='nav'>
          <nav class='hide-mobile'>
            <ul>
              <a href='index.php'><li id='active'>HOME</li></a>
              <a href='sessions.php'><li>SESSIONS</li></a>
              <a href='all.php'><li>ALL</li></a>
              <a href='#'><li>LOGOUT</li></a>
            </ul>
          </nav>
          <nav class='mobile-only'>
            <ul>
              <a href='#'>CLICK ME</a>
            </ul>
          </nav>
        </section>

        <br />

        <section id='adminHeader'>
          <h3 class='white-text text-center'>Welcome back, Steve!</h3>
          <hr class='orange-hr' />
          <br />
          <?php pageHandler(); ?>
        </section>

      </div>
    </div>
  </body>
</html>

