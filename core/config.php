<?php

// Запускаем сессию

session_start();

// Отладка ошибок

error_reporting(E_ALL);

// подключим наш functions.php
@include_once "functions.php";

// В этом массиве будем хранить сообщения от БД

$messages = [];

// Данные для подключения к БД

$dbhost = "192.168.1.165";
$dbuser = "root";
$dbpass = "";
$dbname = "testovoe";

//Вызовем функцию подключения к БД

connectToDB();