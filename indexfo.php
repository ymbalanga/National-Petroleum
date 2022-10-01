<?php session_start(); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

        <title>Application | National Petroleum </title>


            <link rel="stylesheet" type="text/css" href="css/bootstrap.css">
           
            <link href="//fonts.googleapis.com/css?family=Josefin+Sans:100,100i,300,300i,400,400i,600,600i,700,700i" rel="stylesheet">
             <link href="//fonts.googleapis.com/css?family=PT+Sans:400,400i,700,700i" rel="stylesheet">
    
            <link href="css/font-awesome.css" rel="stylesheet">
            <link href="css/style.css" rel='stylesheet' type='text/css'/>
            <link rel="stylesheet" type="text/css" href="css/metro.css"/>
            <link rel="stylesheet" type="text/css" href="css/metro-icon.css"/>
            <script type="text/javascript" src="js/jquery.js"></script>
            <script type="application/x-javascript">
  addEventListener("load", function () {
   setTimeout(hideURLbar, 0);
  }, false);

  function hideURLbar() {
   window.scrollTo(0, 1);
  }
 </script>
            <!-- <style type="text/css">
                html, body {
                    height: 100%;
                    width: 100%;
                    padding: 0;
                    margin: 0;
                }       
                .login-form {
                    width: 29rem;
                    height: 22rem;
                    position: fixed;
                    top: 40%;
                    margin-top: -10.375rem;
                    left: 50%;
                    margin-left: -14rem;
                    background-color: #ffffff;
                    border-radius: 10px;



                }          
                #full-screen-background-image {
                    z-index: -999;
                    min-height: 100%;
                    min-width: 1024px;
                    width: 100%;
                    height: auto;
                    position: fixed;
                    top: 0;
                    left: 0;
                }

            </style> -->
    </head>
    <body class="bg-cyan">
     <h1 class="wthree">Appraise Register Form</h1>
 <div class="login-section-agileits">
  <h3 class="form-head">register online today, Its'free!</h3>
  <form action="#" method="post">
   <div class="w3ls-icon">
    <span class="fa fa-user" aria-hidden="true"></span>
    <input type="text" class="lock" name="name" placeholder="username" required="" />
   </div>
   <div class="w3ls-icon">
    <span class="fa fa-envelope" aria-hidden="true"></span>
    <input type="email" class="user" name="email" placeholder="Email" required="" />
   </div>
   <div class="w3ls-icon">
    <span class="fa fa-lock" aria-hidden="true"></span>
    <input type="password" class="lock" id="password1" name="password" placeholder="Password" required="" />
   </div>
   <div class="w3ls-icon">
    <span class="fa fa-lock" aria-hidden="true"></span>
    <input type="password" class="lock" id="password2" name="confirm password" placeholder="Confirm Password" required="" />
   </div>
   <input type="submit" value="register now">
  </form>
 </div>
 <p class="footer-agile">© 2017 Appraise Register Form. All Rights Reserved | Design by
  <a href="http://w3layouts.com/"> W3layouts</a>
 </p>

       <!--  <img alt="Devlab Fond image" src="img/stat.jpg" id="full-screen-background-image" /> 
        <div class="login-form padding20 block-shadow">

            <form method="post" action="">
                <h4 class="text-light text-shadow">Check Out | National Petroleum</h4>
                <hr class="thin"/>
                <br />
                <div class="input-control text full-size" data-role="input">
                    <label for="user_login">Login:</label>
                    <input type="text" name="login" id="user_login">
                        <button class="button helper-button clear"><span class="mif-cross"></span></button>
                </div>
                <br />
                <br />
                <div class="input-control password full-size" data-role="input">
                    <label for="user_password">Password:</label>
                    <input type="password" name="motdepasse" id="user_password">
                        <button class="button helper-button reveal"><span class="mif-looks"></span></button>
                </div>
                <br />
                <br />
                <div class="form-actions">
                    <button type="submit" class="button primary" name="valider">Connexion</button>

                </div>
            </form>&nbsp;&nbsp; -->
            <?php
            if (isset($_POST['valider'])) {
                if (!empty($_POST['login']) && !empty($_POST['motdepasse'])) {
                    $login = strip_tags(addslashes($_POST['login']));
                    $motdepasse = strip_tags($_POST['motdepasse']);

                    require 'db.php';
                    $req = $pdo->prepare("SELECT * FROM users WHERE login= :login and pass= :pass");
                    $req->bindParam(":login", $login, PDO::PARAM_STR);
                    $req->bindParam(":pass", $motdepasse, PDO::PARAM_STR);

                    $req->execute();

                    $em = $req->rowCount();
                    if ($em == 1) {

                        $found = $req->fetch(PDO::FETCH_OBJ);

                        if ($found->etat == 1) {
                            echo "<p style='color:red; width:400px' class='alert alert-danger'>This User is Deactived !!!</p>";
                            exit();
                        }
                        $_SESSION['login'] = $found->login;
                        $_SESSION['pass'] = $found->pass;
                        $_SESSION['role'] = $found->role;


                        //setcookie("login", $login, time() + 60 * 60 * 60, null, null, false, true);
                        //setcookie("pass", $motdepasse, time() + 60 * 60 * 60, null, null, false, true);

                        header("location:choicestation.php");
                    } else {
                        echo "<p style='color:red; width:400px' class='alert alert-danger'>Bad login and Password !!!</p>";
                    }
                } else {

                    if (!empty($_POST['login']) && is_numeric($_POST['login'])) {
                        echo "<p style='color:red; width:400px' class='alert alert-danger'>The login includes only letters !!!</p>";
                    }
                    if (empty($_POST['login']) && empty($_POST['motdepasse'])) {
                        echo "<p style='color:red; width:400px' class='alert alert-danger'>Please fill in all fields !!!</p>";
                    }
                    if (!empty($_POST['login']) && empty($_POST['motdepasse'])) {
                        echo "<p style='color:red; width:400px' class='alert alert-danger'>Please enter the password !!!</p>";
                    }
                    if (empty($_POST['login']) && !empty($_POST['motdepasse'])) {
                        echo "<p style='color:red; width:400px' class='alert alert-danger'>Please enter your login !!!</p>";
                    }
                }
            }
            ?>
            <!-- <br/>
        </div> -->
    </body>

</html>