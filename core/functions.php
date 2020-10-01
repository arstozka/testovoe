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

function countElements()
{
    global $link;
    $query = "SELECT COUNT(*) FROM ankets";
    $result = mysqli_query($link, $query)
    or die("countElements fatal error: " . mysqli_error($link));
    $row = mysqli_fetch_row($result);
    return $row[0]; // всего записей
}

function getList($filterParams = [], $orderParams = [], $paginationParams = [])
{
    global $link;
    $filterRow = "";
    $orderRow = "";
    $paginationRow = "LIMIT 5 OFFSET 0";
    if (!empty($filterParams)) {
        $filterRow = "WHERE ";
        $keys = array_keys($filterParams);

        foreach ($filterParams as $key => $filterParam) {
            if (empty($filterParam)) {
                continue;
            }

            if ($key == $keys[0]) {
                $filterRow .= "`{$key}`='" . $filterParam . "' ";
                continue;
            }
            $filterRow .= "AND `{$key}`='" . $filterParam . "' ";

        }
    }

    if (!empty($orderParams)) {
        $orderRow = "ORDER BY `ankets` . `{$orderParams['SORTFIELD']}` {$orderParams['SORTBY']}";
    }

    if (!empty($paginationParams)) {
        $paginationRow = "LIMIT {$paginationParams['LIMIT']} OFFSET {$paginationParams['OFFSET']}";
    }

    $query = "SELECT * FROM ankets $filterRow $orderRow $paginationRow";

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
    $images = [];
    if (isset($_FILES) && !empty($_FILES)) {
        foreach ($_FILES as $key => $file) {
            if(!empty($file['name'])) {
                $images[$key] = resizeImage($file, ($key === "AVATAR") ? "Y" : "");
            }
        }

    }
    foreach ($data as $key => $item) {
        switch ($key) {
            case "submit":
            case "SUBMIT":
                continue;
            case "AVATAR":
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
    $anketa_id = saveData($query);

    foreach ($images as $key => $image) {
        if ($key === "AVATAR") {
            $image_ids[] = saveImage($image, "Y");
        }
        $image_ids[] = saveImage($image, "");
    }
    if (!empty($image_ids)) {
        foreach ($image_ids as $image_id) {
            saveImageIds($anketa_id, $image_id);
        }
    }

}

function saveImageIds($anketa_id, $image_id)
{
    global $link;
    $query = "INSERT INTO `ankets_images` (`anketa_id`, `image_id`) VALUES ('" . $anketa_id . "', '" . $image_id . "')";
    $result = mysqli_query($link, $query)
    or die("saveImageIds fatal error:" . mysqli_error($link));
}

function saveData($query = "")
{
    global $link;
    if (!empty($query)) {
        $result = mysqli_query($link, $query)
        or die("saveData fatal error: " . mysqli_error($link));
        return mysqli_insert_id($link);
    }
    return false;
}

function saveImage($file, $is_avatar)
{
    global $link;

    $query = "INSERT INTO `images` (`image`, `ISAVATAR`) VALUES ('" . $file . "','" . $is_avatar . "' )";

    $result = mysqli_query($link, $query)
    or die("saveImage fatal error: " . mysqli_error($link));
    return mysqli_insert_id($link);
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
    global $messages;
    $newWidth = ($isavatar) ? 60 : 600;
    $newHeight = ($isavatar) ? 60 : 700;
    $info = getimagesize($file['tmp_name']);
    $width = $info[0];
    $height = $info[1];
    $type = $info['mime'];

    switch ($type) {
        case 'image/png':
            $img = imagecreatefrompng($file['tmp_name']);
            imagesavealpha($img, true);
            break;
        case 'image/jpeg':
            $img = imagecreatefromjpeg($file['tmp_name']);
            break;
        default:
            $messages[] = "неизвестный тип файла картинки";
            return false;
    }

    $temp = imagecreatetruecolor($newWidth, $newHeight);
    if ($type === 'image/png') {
        imagealphablending($temp, true);
        imageSaveAlpha($temp, true);
        $transparent = imagecolorallocatealpha($temp, 0, 0, 0, 127);
        imagefill($temp, 0, 0, $transparent);
        imagecolortransparent($temp, $transparent);
    }

    $tw = ceil($newHeight / ($height / $width));
    $th = ceil($newWidth / ($width / $height));
    if ($tw < $newWidth) {
        imageCopyResampled(
            $temp,
            $img,
            ceil(($newWidth - $tw) / 2),
            0,
            0,
            0,
            $tw,
            $newHeight,
            $width,
            $height
        );
    } else {
        imageCopyResampled(
            $temp,
            $img,
            0,
            ceil(($newHeight - $th) / 2),
            0,
            0,
            $newWidth,
            $th,
            $width,
            $height
        );
    }

    return $temp;
}