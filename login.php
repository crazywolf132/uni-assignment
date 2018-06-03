<?php
  include('assets/code/login/handler.php');

  if (!empty($_POST['Username']) && !empty($_POST['Password']) && !isLoggedIn()) {
    login();

    if (isLoggedIn()) {
      header("Location: index.php");
    }
  }

  if (isLoggedIn()) {
    // As this doubles as the confirmation page for logging out...
    // We are going to add a friendly touch of their name... so we need to
    // load their data.
    $user = loadUser();
  }
?>
<html>
  <?php include('assets/displays/head.php'); ?>
  <body>
    <div class='row'>
      <div class='col-3 hide-mobile'></div>
      <div id='content' class='col-6'>

        <?php include('assets/displays/logo.php'); ?>

        <section id='nav'>
          <nav>
            <ul>
              <a href="index.php"><li>HOME</li></a>
              <a href="sessions.php"><li>SESSIONS</li></a>
              <a href="all.php"><li>ALL</li></a>
              <a href="report.php"><li>REPORT</li></a>
              <a href="#"><li id='active'>LOGIN</li></a>
            </ul>
          </nav>
        </section>

        <section id='core'>
          <?php
            if (isLoggedIn()) {
              echo "<h3 class='white-text text-center'>Hey ". $user['FirstName'] ."! Are you sure?</h3>";
            } else {
              echo "<h3 class='white-text text-center'>Welcome Back</h3>";
            } 
          ?>
          <hr class='white-hr' />
          <br />
          <?php 
            if (isLoggedIn()) {
              echo "<div class='row'>
                <div class='col-3'></div>
                <div class='col-6'>
                  <a href='logout.php'><button class='formButton'>Logout</button></a>
                </div>
                <div class='col-3'></div>
              </div>";
            } else {
              if ($cookieMessage){
                echo '<a href="#"><div class="blue-around-text text-center white-text smr sml smb"><h3>'. $cookieMessage .'</h3></div></a>';
              }
              echo "<div class='row'>
                <div class='col-3'></div>
                <div class='col-6'>
                  <form class='white-text' method='POST'>
                    <input type='text' placeholder='Username' required='true' name='Username' id='LoginFeild' />
                    <input type='password' placeholder='Password' required='true' name='Password' id='PasswordFeild' />
                    <button type='submit' id='LoginButton' class='primary-item'>Login</button>
                  </form>
                  <a href='register.php' id='RegisterButton' class='text-center primary-item'>Register</a>

                </div>
                <div class='col-3'></div>
              </div>";
            }
          ?>
          
          
        </section>

      </div>
    </div>
  </body>
</html>
