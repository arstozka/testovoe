<?php
@include_once "core/config.php";
checkLoggedIn("yes");
$pageTitle = 'Список анкет';

if (isset($_GET)) {
    $filterParams = [];
    $orderParams = [];
    foreach ($_GET as $key => $param) {
        if (empty($param)) {
            continue;
        }
        if ($key === 'SORTFIELD' || $key === 'SORTBY') {
            $orderParams[$key] = $param;
            continue;
        }
        $filterParams[$key] = $param;
    }
    $arResult = getList($filterParams, $orderParams);
} else {
    $arResult = getList();
}
if (empty($arResult)) {
    $messages[] = "пустой список анкет";
}
showListpage($arResult);
function showListpage($arResult)
{
    global $messages;
    global $pageTitle;
    @include_once "core/header.php";
    ?>
    <div class="container">
        <section class="lots">
            <div class="lots__header">
                <h2>Все анкеты</h2>
            </div>
            <div class="lots__filter">
                <form class="form form--filter" method="get" action="<?= $_SERVER['PHP_SELF'] ?>">
                    <div class="filter__row">
                        <div class="form__item">
                            <label for="filter-lastname">Искать по фамилии:</label>
                            <input type="text" id="filter-lastname"
                                   name="LASTNAME"
                                   placeholder="Например: Иванов">
                        </div>
                    </div>
                    <div class="filter__row">
                        <div class="form__item">
                            <label for="sortfield">Отсортировать по:</label>
                            <select name="SORTFIELD" id="sortfield">
                                <option value="LASTNAME">Фамилии</option>
                                <option value="BIRTHDAY"> Дате рождения</option>
                            </select>
                        </div>
                        <div class="form__item">
                            <label for="sortway">Расположить:</label><select name="SORTBY" id="sortby">
                                <option value="asc">по возрастанию</option>
                                <option value="desc"> по убыванию</option>
                            </select>
                        </div>
                    </div>
                    <div class="filter__row">
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
                    <button class="button" type="submit">Применить</button>
                    <a href="<?= $_SERVER['PHP_SELF'] ?>" class="button">Сбросить фильтр</a>
                </form>
            </div>
            <ul class="lots__list">
                <? if ($messages) {
                    displayErrors($messages);
                    return;
                } ?>
                <? foreach ($arResult as $item): ?>

                    <li class="lots__item lot">
                        <div class="lot__image">
                            <? if ($item['AVATAR'] && is_int($item['AVATAR'])) {
                                getImageById("AVATAR");
                            } else {
                                echo '<img src="core/img/noavatar.png" width="350" height="260" alt="Аватар">';
                            } ?>
                        </div>
                        <div class="lot__info">
                            <h3 class="lot__title">
                                <?= $item['LASTNAME'] ?> <?= $item['NAME'] ?> <?= $item['FATHERNAME'] ?>
                            </h3>
                            <a class="button button--lot" href="/detail.php?ID=<?= $item['id'] ?>">Подробнее</a>
                        </div>
                    </li>
                <? endforeach; ?>
            </ul>
            <ul class="pagination-list">
                <li class="pagination-item pagination-item-prev"><a>Назад</a></li>
                <li class="pagination-item pagination-item-active"><a>1</a></li>
                <li class="pagination-item"><a href="#">2</a></li>
                <li class="pagination-item"><a href="#">3</a></li>
                <li class="pagination-item"><a href="#">4</a></li>
                <li class="pagination-item pagination-item-next"><a href="#">Вперед</a></li>
            </ul>
        </section>

    </div>
    <? @include_once "core/footer.php";
}

?>
