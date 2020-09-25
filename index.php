<?php
include_once("core/config.php");
$pageTitle = "Анкетирование";
if (isset($_POST["submit"])) {
    fieldValidator("Фамилия", $_POST['LASTNAME'], "alpha", 3, 25);
    fieldValidator("Дата рождения", $_POST['BIRTHDAY'], "date");

    if ($messages) {
        showFormPage();
        exit;
    }

    if (!($arData = prepareFormData($_POST))) {
        $messages[] = "При подготовке данных возникла ошибка.";
    }

    if ($messages) {
        doLoginPage();
        exit;
    }
}
showFormPage();

function showFormPage()
{
    global $messages;
    global $pageTitle;
    @include_once 'core/header.php';
    ?>
    <form id="new-anketa" class="form container" action="<?= $_SERVER["PHP_SELF"]; ?>" method="post">
        <h2>Добавить анкету</h2>
        <?php
        if ($messages) {
            displayErrors($messages);
        }
        ?>
        <fieldset id="step1" class="current"> <!-- form--invalid -->
            <div class="form__container-three">
                <div class="form__item ">
                    <label for="lastname">Фамилия*</label>
                    <input id="lastname" type="text" name="LASTNAME" placeholder="Например: Иванов">
                </div>
                <div class="form__item ">
                    <label for="name">Имя</label>
                    <input id="name" type="text" name="NAME" placeholder="Например: Сергей">
                </div>
                <div class="form__item">
                    <label for="fathername">Отчество</label>
                    <input id="fathername" type="text" name="FATHERNAME" placeholder="Например: Иванович">
                </div>
            </div>
            <div class="form__container-two">
                <div class="form__item">
                    <label for="gender">Ваш пол*</label>
                    <select id="gender" name="GENDER">
                        <option>Мужской</option>
                        <option>Женский</option>
                    </select>
                    <span class="form__error">Укажите пол</span>
                </div>
                <div class="form__item">
                    <label for="birthday">Дата рождения*</label>
                    <input class="form__input-date" id="birthday" type="date" name="BIRTHDAY"
                           value="<?= date('Y-m-d') ?>">
                </div>
            </div>
            <span class="form__error form__error--bottom">Пожалуйста, исправьте ошибки в форме.</span>
            <div class="form__button-group">
                <a href="javascript:void(0)" class="button " data-verify-step="step1"
                   data-next-step="step2">Далее</a>
            </div>
        </fieldset>
        <fieldset id="step2" class="">
            <div class="form__container-two">
                <div class="form__item form__item--file"> <!---form__item--invalid --->
                    <label>Аватар</label>
                    <div class="preview" id="avatar-preview">
                        <button class="preview__remove" type="button">x</button>
                        <div class="preview__img">
                            <img src="" width="113" height="113" alt="Изображение аватара">
                        </div>
                    </div>
                    <div class="form__input-file">
                        <input class="visually-hidden" type="file" id="avatar" name="AVATAR" value=""
                               accept=".png, .jpg, .jpeg" size="102400">
                        <label for="avatar">
                            <span>+ Загрузить</span>
                        </label>
                    </div>
                    <div class="form__error">Размер файла превышает 100 Кб</div>
                </div>
                <div class="form__item">
                    <label for="color">Любимый цвет*</label>
                    <input class="form__input-color" id="color" type="color" name="COLOR">
                </div>
            </div>
            <div class="form__button-group">
                <a href="javascript:void(0)" class="button button--secondary"
                   data-next-step="step1">Назад</a>
                <a href="javascript:void(0)" class="button" data-verify-step="step2"
                   data-next-step="step3">Далее</a>
            </div>
        </fieldset>
        <fieldset id="step3" class="">
            <div class="form__container-four">
                <div class="form__item">
                    <div class="form__input-checkbox">
                        <input id="skill1" type="checkbox" name="USIDCHOVOST" value="Y">
                        <label for="skill1" class="">
                            Усидчивость
                        </label>
                    </div>
                </div>
                <div class="form__item">
                    <div class="form__input-checkbox">
                        <input id="skill2" type="checkbox" name="OPRYATNOST" value="Y">
                        <label for="skill2" class="">
                            Опрятность
                        </label>
                    </div>
                </div>
                <div class="form__item">
                    <div class="form__input-checkbox">
                        <input id="skill3" type="checkbox" name="SAMOOBUCHAEMOST" value="Y">
                        <label for="skill3" class="">
                            Самообучаемость
                        </label>
                    </div>
                </div>
                <div class="form__item">
                    <div class="form__input-checkbox">
                        <input id="skill4" type="checkbox" name="TRUDOLUBIE" value="Y">
                        <label for="skill4" class="">
                            Трудолюбие
                        </label>
                    </div>
                </div>
            </div>
            <div class="form__item form__item--wide">
                <label for="personality">Личные качества</label>
                <textarea id="personality" name="PERSONALITY"
                          placeholder="Опишите свои сильные и слабые стороны"></textarea>
            </div>
            <span class="form__error form__error--bottom">Пожалуйста, исправьте ошибки в форме.</span>
            <div class="form__button-group">
                <a href="javascript:void(0)" class="button button--secondary"
                   data-next-step="step2">Назад</a>
                <a href="javascript:void(0)" class="button" data-verify-step="step3"
                   data-next-step="step4">Далее</a>
            </div>
        </fieldset>
        <fieldset id="step4">
            <div class="form__item form__item--file gallery"> <!-- form__item--uploaded -->
                <label>Ваша галерея</label>
                <div class="preview">
                    <button class="preview__remove" type="button">x</button>
                    <div class="preview__img">
                        <img src="" width="113" height="113" alt="Изображение галереи">
                    </div>
                </div>
                <div class="form__input-file">
                    <input class="visually-hidden" type="file" id="photo" name="IMAGES" value="">
                    <label for="photo">
                        <span>+ Загрузить</span>
                    </label>
                </div>
            </div>

            <div class="form__button-group">
                <a href="javascript:void(0)" class="button button--secondary"
                   data-next-step="step3">Назад</a>
                <button type="submit" name="submit" class="button">Отправить анкету</button>
            </div>
        </fieldset>
    </form>

    <?
    @include "core/footer.php";
}

?>
