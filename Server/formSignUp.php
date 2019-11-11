<!DOCTYPE html>
<html>
    <head>
        <title>Form Sign Up</title>
        <meta http-equiv='Content-Type' content='text/html; charset=utf-8'>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <script type="text/javascript" src="scripts/forms.js"></script>
    </head>
    <body>
        <?php
        require_once('rootPath.php');
        $rootPath = RootPath;

        @session_start();

        $valueUser = "";
        $valuePhone = "";


        ?>
        <center>
            <br>
            <form method="POST" action="processFormSignUp.php" onsubmit="return FormSignUp(this)">
                <!-- Username -->
                <label for="user">Username</label>
                <input type="text" id="user" placeholder="" name="user" value="<?php echo $valueUser;?>" maxlength="12">
                <p>Username shold be at least 6 characters. And can contain any letters, numbers, - or _ </p>

                <br>
                <br>
                <!-- Phone number -->
                <label for="phone">Phone Number</label>
                <input type="text" id="phone" name="phone" placeholder="" value="<?php echo $valuePhone; ?>">
                <p>Please provide your Phone Number</p>

                <br>
                <br>
                <!-- Password-->
                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="">
                <p>Password should be at least 5 characters. And have at least one capital-case, one lower-case and one number</p>

                <br>
                <!-- Password -->
                <label for="password_confirm">Password (Confirm)</label>
                <input type="password" id="password_confirm" name="password1" placeholder="">
                <p>Please confirm password</p>

                <br>
                <!-- Buttons -->
                <input type="submit" value="Submit">
                <input type="reset" name="Reset" value="Reset">
            </form>
        </center>
    </body>
</html>
