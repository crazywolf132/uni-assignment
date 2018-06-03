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
      <div class='col-3 hide-tablet hide-mobile'></div>
      <div id='content' class='col-6 liftOff'>

        <?php include('assets/displays/logo.php'); ?>

        <section id='nav'>
          <nav class='hide-mobile'>
            <ul>
              <a href="index.php"><li>HOME</li></a>
              <a href="sessions.php"><li>SESSIONS</li></a>
              <a href="all.php"><li>ALL</li></a>
              <a href="#"><li id='active'>REPORT</li></a>
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
          <h3 class='primary-text text-center'>Answers to Task 5<h3>
          <hr class='white-hr' />
          <br />
          <p>
            <ol class='white-text'>
              <li class='smb'>HTML is also known as Hyper Text Markup Langauge... Many people confuse it with a real programming language. It is the core of the website, Every element must be added via some sort of HTML.</li>
              <li class='smb'>CSS is also known as Cascading Style Sheet. This is the code that gives the otherwise ugly HTML, its beauty. It tells the browser where and how to render the elements of the page.</li>
              <li class='smb'>Hotlinking an image IS bad. It is where you dont have an image locally (in respect to where the rest of the code is)... but instead is on a different server or website, and you are using their bandwith to display it on your website.</li>
              <li class='smb'>PHP is a server side language. It is used to make connects to the DATABASE... create requests, handle requests, sessions etc. Developers use it to add an otherwise non-existent functionality to their website.</li>
              <li class='smb'>PHP and HTML can both relate to eachother as they can both be in-control of the page's content.</li>
              <li class='smb'>The difference between POST and GET is vastly different. A POST request sends the data to the specified the location with the intent of not being seen by the user in the middle. A GET Request is used to display data on a requested page. You SHOULD use POST requests on pages such as a user Login. You should also use GET reuqests on pages such a search page.</li>
              <li class='smb'>Untrusted data, is any data that is controlled by the client. The reason it is "untrusted" is simply because we don't actually know what the clients intents are. Trusted data is any data that comes straight from calculations or results from a DATABASE on the server side. This is because generally the user has no way of interacting with these results...</li>
              <li class='smb'>SQL inject, also known as SQLI... is the practice of exploiting a failure in the database connection and using it to inject untrusted data directly into the database. An example of this is with a login page. If the developer directly inputs the untrusted data into the SQL request, there is the vulnerability of being able to use a " to escape the SQL querry and enter your own data.</li>
              <li class='smb'>Well, if you are talking about the website we just built. It should be. Though we are using a local version of SQLite... with the password as "Admin"... so i would say that the database itself is very vulnerable. The way i have coded my site... as far as i am aware, it is SQLI proof. Though, there are no promisses as I havent fully had time to test every single way of SQLI. If you are talking about the university website... I would assume so, otherwise someone is going to lose their job...</li>
              <li class='smb'>XSS or Cross Site Scripting, is where a client is able to inject scripts that can run when anyone views the page. An example of this is if someone inputs a `&lt;script&gt;` tag into an input box of a review... (If the developer hasnt already setup a way to avoid that.)</li>
              <li class='smb'>Once again, if you are talking about my website that i have just built. Then yes, I have made sure to include the appropriate messures to prevent it. IF you are talking about the uni website, again. I would assume so.</li>
              <li class='smb'>Cookies are generally Strings of information kept buy the website inside of your browser. The only information they need to keep on you is your browser and their expiry aswell as their message. Sites can use them to temporarly hold information. Such as an error message. Eg. "Incorrect username/password" to then display to the user.</li>
              <li class='smb'>Cookies are not good or Bad. They are just part of how the browser works. Sites all across the internet use them for different purposes... Disabling your cookies may lead to some unexpected results as some websites may need them to function properly, but some websites might only use them to track your internet usage to better surgest ads.</li>
              <li class='smb'>I must say, you arent the most clear on your questions. If you are talking about space on the clients computer... If they have cache disabled... Then there would be none. If they dont, then it will be whatever the size of the compressed images and style sheet are. If we are talking about on the server... then its roughly 12mb (including DB).</li>
              <li class='smb'>HTTP Status codes, are codes that are returned to the client on response to the request made to the server. These can vary from a good status code to a bad server not found status code.</li>
              <li class='smb'>
                <ol>
                  <li class='smb'>200: This means that all is good and there are no problems with this request.</li>
                  <li class='smb'>302: This request is doing a temporary redirection of the current traffic.</li>
                  <li class='smb'>400: This means that the current request to the server is not being served or it simply will not process it at all.</li>
                  <li class='smb'>403: This means that the client who sent the request generally does not have the correct permissions to view the content.</li>
                  <li class='smb'>404: This means the server could not find whatever the client has asked for.</li>
                  <li class='smb'>500: This means that the server has encounted a server-side error and there is not simply one status code to return... so it returns this.</li>
                </ol>
              </li>
               <li class='smb'>I completed all of the bonus tasks.... Here is a list of extras i added. I added all the extras because i plan on releasing this on github and updating after this semester. I plan to make a real project from it and make it a CMS.</li>
                <ol>
                  <li class='smb'>Working barcode system for each ticket. This will add a real barcode of the ticketID to each ticket.</li>
                  <li class='smb'>A system to purchase multiple tickets at a single time. This is simply because what if someone wanted to by 4 tickets for them and their friends?</li>
                  <li class='smb'>A real login system. This allows for me to remove some of the usless features such as the username system on the booking screen.</li>
                  <li class='smb'>A system to allow users to change their password.</li>
                  <li class='smb'>A way for the users to see all their tickets they have ever bought.</li>
                  <li class='smb'>A user page so they can access their tickets, see how much they have spent over the time... and edit their account.</li>
                  <li class='smb'>A system to just search just the movieID on the admin page, to see the details about all the avaliable sessions for it.</li>
                  <li class='smb'>Mobile Responsive layout...</li>
                  <li class='smb'>A system to hide all sessions that are completely full.</li>
                  <li class='smb'>A system that will tell you if the movie is avaliable tomorrow or next-week or next-month (on the homepage).</li>
                  <li class='smb'>A system to hide the Book Movie button if there are no more sessions for that movie.</li>
                  <li class='smb'>A switchable login screen. It will display a logout view if you are already logged in... to prevent double session's.</li>
                </ol>  
            </ol>
          </p>
        </section>

      </div>
      <div class='col-3 hide-tablet hide-mobile'></div>
    </div>
  </body>
</html>
