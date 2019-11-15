<!DOCTYPE html>
<html>
    <head>
        <title>Form Log In</title>
        <meta http-equiv='Content-Type' content='text/html; charset=utf-8'>
        <meta name="viewport" content="width=device-width, initial-scale=1">
    </head>
    <body>
        <?php
        require_once('rootPath.php');
        $rootPath = RootPath;

        session_start();

        $nextUrl = 'processFormLogIn.php';
        ?>
        <center>
          <h3>Sign in</h3>

        <form accept-charset="UTF-8" role="form" action="<?php echo $nextUrl ?>" method="POST">
            <input class="form-control" placeholder="Username" name="user" type="text">
            <br>
            <input placeholder="Password" name="password" type="password" value="">
            <br>
            <input type="submit" value="Login">
        </form>
        </center>
    </body>
</html>
