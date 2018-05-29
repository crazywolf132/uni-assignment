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
              <a href="#"><li id='active'>LOGIN</li></a>
            </ul>
          </nav>
        </section>

        <section id='core'>
          <h3 class='white-text text-center'>Welcome Back</h3>
          <hr class='white-hr' />
          <br />
          <div class='row'>
            <div class='col-3'></div>
            <div class='col-6'>
              <form class='white-text'>
                <input type='text' placeholder="Username" required='true' name="username" id='LoginFeild' />
                <input type='password' placeholder="Password" required='true' name="password" id='PasswordFeild' />
                <button type="submit" id='LoginButton' class='primary-item'>Login</button>
              </form>
              <a href='register.php' id='RegisterButton' class='text-center primary-item'>Register</a>

            </div>
            <div class='col-3'></div>
          </div>
        </section>

      </div>
    </div>
  </body>
</html>
