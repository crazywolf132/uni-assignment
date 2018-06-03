<?php

    session_start();
    include_once("assets/code/helper.php");

    $cookieMessage = getCookieMessage();

    function isLoggedIn() {
        if (isset($_SESSION['user_id'])) {
            return true;
        }
        return false;
    }

    function loadNavChange() {
        if (isLoggedIn()) {
            echo '<a href="user.php"><li>MY ACCOUNT</li></a>';
        } else {
            echo '<a href="login.php"><li>LOGIN</li></a>';
        }
    }


    function createUser() {

        $dbh = connectToDatabase();
        
        // We first need to see if the username exists or not...

        $stmt = $dbh->prepare("SELECT MemberID FROM Members WHERE UserName LIKE ?");
        $stmt->bindValue(1, makeOutputSafe($_POST['Username']));
        $stmt->execute();
        $res = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($res) {
            setCookieMessage("Sorry, that username is already in use!");
            redirect("register.php");
        }

        $statement = $dbh->prepare("INSERT INTO Members (Username, FirstName, LastName, Email, Postcode, Password) Values (?, ?, ?, ?, ?, ?);");
        $statement->bindValue(1,makeOutputSafe($_POST['Username']));
        $statement->bindValue(2,makeOutputSafe($_POST['FirstName']));
        $statement->bindValue(3,makeOutputSafe($_POST['LastName']));
        $statement->bindValue(4,makeOutputSafe($_POST['Email']));
        $statement->bindValue(5,makeOutputSafe($_POST['Postcode']));
        $statement->bindValue(6,password_hash(makeOutputSafe($_POST['Password']), PASSWORD_BCRYPT));

        if ($statement->execute()) {
            setCookieMessage("You can now login with your new account!");
            redirect("login.php");
        }

        // TODO: Use the stupid cookie system here to tell the user all is good...
    }

    function deleteUser() {

    }

    function login() {
        $dbh = connectToDatabase();

        $statement = $dbh->prepare("SELECT MemberID, Username, Password, FirstName, LastName, Clearance, Email FROM Members WHERE UserName = ?");
        $statement->bindValue(1, makeOutputSafe($_POST['Username']));
        $statement->execute();

        $results  = $statement->fetch(PDO::FETCH_ASSOC);

        if (count($results) > 0 && password_verify($_POST['Password'], $results['Password'])) {
            setCookieMessage("You are now logged in!");
            $_SESSION['user_id'] = $results['MemberID'];
            redirect("index.php");
        } else {
            setCookieMessage("Something went wrong, please try again!");
            redirect("login.php");
        }
    }

    function loadUser() {

        $dbh = connectToDatabase();

        $statement = $dbh->prepare("SELECT MemberID, Username, FirstName, LastName, Clearance, Email From Members WHERE MemberID = ?");
        $statement->bindValue(1, $_SESSION['user_id']);
        $statement->execute();

        $results = $statement->fetch(PDO::FETCH_ASSOC);

        if (count($results) > 0) {
            return $results;
        }

    }

?>