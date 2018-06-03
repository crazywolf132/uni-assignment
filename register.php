<?php
  include('assets/code/login/handler.php');
  $cookieMessage = getCookieMessage();
  if (!empty($_POST['Username']) && !empty($_POST['Password'])) {
    createUser();
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
          <h3 class='white-text text-center'>Welcome Back</h3>
          <hr class='white-hr' />
          <br />
          <?php
            if ($cookieMessage){
              echo '<a href="#"><div class="red-around-text text-center white-text smr sml smb"><h3>'. $cookieMessage .'</h3></div></a>';
            }
          ?>
          <div class='row'>
            <div class='col-3'></div>
            <div class='col-6'>
              <form class='white-text' method='POST'>
                <input type='text' placeholder="Firstname" required='true' name="FirstName" id='LoginFeild' />
                <input type='text' placeholder="Lastname" required='true' name="LastName" id='LoginFeild' />
                <input type='text' placeholder="Username" required='true' name="Username" id='LoginFeild' />
                <input type='email' placeholder="email@email.com" required='true' name="Email" id='LoginFeild' />
                <input type='number' placeholder="Postcode" required='true' name="Postcode" id='LoginFeild' />
                <input type='password' placeholder="Password" required='true' name="Password" id='LoginFeild' />
                <button type="submit" id='LoginButton' class='primary-item'>Register</button>
              </form>
              <a href='login.php' id='RegisterButton' class='text-center primary-item'>Login</a>

            </div>
            <div class='col-3'></div>
          </div>
        </section>

      </div>
    </div>
  </body>
</html>
