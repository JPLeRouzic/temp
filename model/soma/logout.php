<?php

ini_set('display_errors', 'On');
error_reporting(E_ALL);

session_start();

session_destroy();

header('Location: ../index.php');