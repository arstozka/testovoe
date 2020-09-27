<?php

function connectToDB()
{
    global $link, $dbhost, $dbuser, $dbpass, $dbname;
    ($link = mysqli_connect("$dbhost", "$dbuser", $dbpass)) || die("Ошибка соединения с базой данных");
    mysqli_select_db($link, "$dbname") || die("Невозможно открыть базу");
}

function displayErrors($messages)
{
    print("<b>Возникли следующие ошибки:</b>\n<ul>\n");

    foreach ($messages as $msg) {
        print("<li>$msg</li>\n");
    }
    print("</ul>\n");
}

function checkLoggedIn($status)
{
    switch ($status) {
        case "yes":
            if (!isset($_SESSION["loggedIn"])) {
                header("Location: login.php");
                exit;
            }
            break;
        case "no":
            if (isset($_SESSION["loggedIn"]) && $_SESSION["loggedIn"] === true) {
                header("Location: list.php");
            }
            break;
    }
    return true;
}

function cleanAuthSession($login, $password)
{
    $_SESSION["login"] = $login;
    $_SESSION["password"] = $password;
    $_SESSION["loggedIn"] = true;
}

function flushAuthSession()
{
    unset($_SESSION["login"]);
    unset($_SESSION["password"]);
    unset($_SESSION["loggedIn"]);
    session_destroy();
    return true;
}

function checkPass($login, $password)
{
    global $link;
    $query = "SELECT  login, password FROM users WHERE login='$login' and password='$password'";

    $result = mysqli_query($link, $query)
    or die("checkPass fatal error: " . mysqli_error($link));

    if (mysqli_num_rows($result) == 1) {
        return mysqli_fetch_array($result);
    }
    return false;
}

function getList($filterParams = [])
{
    global $link;
    $filterRow = "";
    if (!empty($filterParams)) {
        $filterRow = "WHERE ";
        foreach ($filterParams as $key => $filterParam) {
            $filterRow .= "{$key}=" . $filterParam;
        }
    }
    $query = "SELECT * FROM ankets $filterRow";
    $result = mysqli_query($link, $query)
    or die("getList fatal error: " . mysqli_error($link));
    if (mysqli_num_rows($result) >= 1) {
        $arResult = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $arResult[] = $row;
        }
        return $arResult;
    }
    return false;
}

function deleteAnketa($id = NULL)
{
    global $link;
    if (!$id && !empty($id)) {
        $messages[] = "При попытке удалить анкету произошла ошибка";
        return;
    }
    $query = "DELETE FROM ankets WHERE id=$id";
    $result = mysqli_query($link, $query)
    or die("getImage fatal error: " . mysqli_error($link));
    header("Location: list.php");
}

function birthdayFormat($string)
{
    $months = array(1 => 'января', 'февраля', 'марта', 'апреля', 'мая', 'июня', 'июля', 'августа', 'сентября', 'октября', 'ноября', 'декабря');
    return date('d ' . $months[date('n')] . ' Y', strtotime($string));
}

function fieldValidator($field_descr, $field_data, $field_type, $min_length = "", $max_length = "", $field_required = 1)
{
    global $messages;

    if (!$field_data && !empty($field_data) && $field_required) {
        return;
    }
    $field_ok = false;

    $data_types = [
        "email" => "/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/",
        "date" => "/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/",
        "digit" => "/^[0-9]$/",
        "number" => "/^[0-9]+$/",
        "string" => "",
        "alpha" => '/^[a-zA-Zа-яёА-ЯЁ\s\-]+$/u',
        "alphanumeric" => "/^[a-zA-Z0-9]+$/",
    ];

    if ($field_required && empty($field_data)) {
        $messages[] = "Поле $field_descr является обязательным.";
        return;
    }

    if ($field_type == "string") {
        $field_ok = true;
    } else {
        $field_ok = preg_match($data_types[$field_type], $field_data);
    }

    if ($field_ok && ($min_length > 0)) {
        if (strlen($field_data) < $min_length) {
            $messages[] = "$field_descr должен быть не короче $min_length символов.";
            return;
        }
    }
    if ($field_ok && ($max_length > 0)) {
        if (strlen($field_data) > $max_length) {
            $messages[] = "$field_descr должен быть не длиннее $max_length символов.";
            return;
        }
    }
    if (!$field_ok) {
        $messages[] = "Введите корректный $field_descr";
        return;
    }
}

function prepareFormData($data)
{
    $preparedData = [];
    if(isset($_FILES) && !empty($_FILES)){
        //foreach ()
    pars($_FILES);

    }
    foreach ($data as $key => $item) {
        switch ($key) {
            case "submit":
            case "SUBMIT":
                continue;
            case "AVATAR":
                $preparedData[$key] = resizeImage($item, true);
//                $preparedData[$key] = base64_encode($item);
                continue;
            case "IMAGES":
                continue;
            default:
                $preparedData[$key] = $item;
        }
    }
    $count_col = 0;
    $preparedLength = count($preparedData);
    $query = "INSERT INTO `ankets` ";
    $columns = "";
    $values = "VALUES ";
    foreach ($preparedData as $key => $item) {
        if ($count_col == 0) {
            $columns .= "(`" . $key . "`, ";
            $values .= "('" . $item . "', ";
        } elseif ($count_col === $preparedLength - 1) {
            $columns .= "`" . $key . "`) ";
            $values .= "'" . $item . "')";
        } else {
            $columns .= "`" . $key . "`, ";
            $values .= "'" . $item . "', ";
        }
        $count_col++;
    }
    $query .= $columns . $values;
    saveData($query);
}

function saveData($query = "")
{
    global $link;
    if (!empty($query)) {
        $result = mysqli_query($link, $query)
        or die("saveData fatal error: " . mysqli_error($link));
        header("Location success.php");
    }
    return false;
}

function pars($data)
{
    echo '<pre>';
    var_dump($data);
    echo '</pre>';
    die('===END===');
}

function getImageById($image_id)
{
    global $link;
    $query = "SELECT * from images WHERE id=$image_id";
    $result = mysqli_query($link, $query)
    or die("getImage fatal error: " . mysqli_error($link));
    if (mysqli_num_rows($result) === 1) {
        return mysqli_fetch_array($result);
    }
    return false;
}

function resizeImage($file, $isavatar = false)
{
    if (class_exists("Imagick")) {
        //Получаем данные
        $image = new Imagick($file);
        $width = $image->getImageWidth();
        $height = $image->getImageHeight();
        $newWidth = ($isavatar) ? 60 : 600;
        $newHeight = ($isavatar) ? 60 : 700;
        ($width > $height) ? $image->adaptiveResizeImage($newWidth, $newHeight) : $image->adaptiveResizeImage($newHeight, $newWidth);
        pars($image);
        //return base64_decode()
    } else {
        $messages[] = "Конфигурация сервера не позволят работать с изображенями";
    }
}