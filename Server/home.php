<!DOCTYPE html>
<html>
    <head>
        <title>Home</title>
        <meta http-equiv='Content-Type' content='text/html; charset=utf-8'>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
    </head>
    <?php
        // Create file with root path
        $RootPathValue = $_SERVER["CONTEXT_DOCUMENT_ROOT"];

        $h = fopen("rootPath.php", "wt");

        $RootPathDefinition = "define(\"RootPath\", \"$RootPathValue\" );\n";

        fputs($h, "<?php\n");
        fputs($h, $RootPathDefinition);
        fputs($h, "?>\n");
        fclose($h);

        require_once('rootPath.php');
        $rootPath = RootPath;
    ?>
    <body>
      <center>
      <form action="formLogIn.php" method="POST">
          <input id="button" type="submit" value="Login">
      </form>
      <br>
      <form action="formSignUp.php" method="POST">
          <input id="button" type="submit" value="Sign up">
      </form>
    </center>
    </body>
</html>
