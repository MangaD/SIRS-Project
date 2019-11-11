<?php
define("ConfigFile", $_SERVER["CONTEXT_DOCUMENT_ROOT"] . DIRECTORY_SEPARATOR . "config" . DIRECTORY_SEPARATOR . ".htconfig.xml");

$ligacao;
$configDataBase;

function loadConfigurationDataBase($configFile) {
    global $configDataBase;

    if ($configDataBase == NULL) {
        $aux = simplexml_load_file($configFile)
                or die("Can't read data base configuration file.");

        $configDataBase = $aux->DataBase[0];
    }
}

function dbConnect($configFile, $setCharSet = true) {
    global $configDataBase;
    global $ligacao;

    loadConfigurationDataBase($configFile);

    $host = strval($configDataBase->host);
    $port = intval($configDataBase->port);
    $username = strval($configDataBase->username);
    $password = strval($configDataBase->password);

    $hostFQN = "$host:$port";

    $ligacao = mysqli_connect($hostFQN, $username, $password)
            or die("Could not connect to data base server ($hostFQN)");

    if ($setCharSet == true) {
        mysqli_set_charset($ligacao, 'utf8');
    }
}

function dbDisconnect() {
    global $ligacao;
    mysqli_close($ligacao);
}
?>
