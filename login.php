<?php
include_once("core/config.php");

checkLoggedIn("no");

$pageTitle = "Страница авторизации";

if (isset($_POST["submit"])) {
    fieldValidator("login", $_POST["login"], "alphanumeric", 4, 15);
    fieldValidator("password", $_POST["password"], "string", 4, 15);
    if ($messages) {
        doLoginPage();
        exit;
    }

    if (!($row = checkPass($_POST["login"], $_POST["password"]))) {
        $messages[] = "Неверный логин/пароль, попробуйте еще раз";
    }

    if ($messages) {
        doLoginPage();
        exit;
    }

    cleanAuthSession($row["login"], $row["password"]);

    header("Location: list.php");
} else {
    doLoginPage();
}

function doLoginPage()
{
    global $messages;
    global $pageTitle;
    @include_once 'core/header.php';
    ?>
    <div class="container">

        <form action="<?= $_SERVER["PHP_SELF"]; ?>" method="POST" class="form">
            <h2>Страница авторизации</h2>
            <?php
            if ($messages) {
                displayErrors($messages);
            }
            ?>
            <div class="form__item ">
                <label for="login">Логин*</label>
                <input id="login" type="text" name="login" maxlength="15"
                       value="<?php print isset($_POST["login"]) ? $_POST["login"] : ""; ?>">
            </div>
            <div class="form__item ">
                <label for="password">Пароль*</label>
                <input id="password" type="password" name="password" maxlength="15">
            </div>
            <div class="form__button-group">
                <button class="button" name="submit" type="submit">Войти</button>
            </div>
        </form>
    </div>
    <?php
    @include_once 'core/footer.php';
}

?>