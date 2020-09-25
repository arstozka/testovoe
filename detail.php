<?php
@include_once "core/config.php";
checkLoggedIn("yes");
$pageTitle = 'Детальная страница';
global $arResult;
if (isset($_GET["ID"])) {

    if(empty($_GET["ID"]) ){
        $messages[] = "Такой анкеты не существует";
    } else{
        $filterParams = [
        "ID"=> $_GET["ID"]
        ];
        $arResult = getList($filterParams);
        if(!$arResult){
            $messages[] = "Такой анкеты не существует";
        }
    }
} else {
    $messages[] = "Такой анкеты не существует";
}
doDetailPage($arResult);

function doDetailPage($arResult = [])
{
    global $messages;
    global $pageTitle;
    @include_once 'core/header.php'; ?>
    <section class="lot-item container">
    <? if ($messages) {
    displayErrors($messages);
} else { ?>
<?$item = $arResult[0]?>
    <h2>Анкета: <?=$item["LASTNAME"]?>  <?=$item['NAME']?> <?=$item['FATHERNAME']?></h2>
    <div class="lot-item__content">
      <div class="lot-item__left">
      <?if(!empty($item['IMAGES'])):?>
      <div class="lot-item__slider slider">
        <div class="slider__image">
          <img src="" width="100%" height="auto" alt="Слайд">
        </div>

        <div class="slider__pagination">
        </div>

      </div>
      <?endif;?>
      <?if(!empty($item['BIRTHDAY'])):?>
          <p class="lot-item__birthday">День рождения: <span><?= birthdayFormat($item['BIRTHDAY'])?></span></p>
        <?endif;?>
        <p class="lot-item__description"><?=$item['PERSONALITY']?></p>
      </div>
      <div class="lot-item__right">
        <div class="lot-item__state">
        <div class="lot-item__avatar avatar">
         <? if ($item['AVATAR'] && is_int($item['AVATAR'])) {
                            getImageById("AVATAR");
                        } else {
                            echo '<img src="core/img/noavatar.png" width="100%" height="auto" alt="Аватар">';
                        } ?>
        </div>
          <form class="lot-item__form" action="ajax/add.php" method="post">
          <div class="lot-item__form-item">
            <button type="submit" class="button">Удалить анкету</button>
          </div>
          </form>
        </div>
        <?if(isset($item['SKILLS']) || !empty($item['SKILLS'])):?>
            <div class="history">
              <h3>Навыки</h3>
              <table class="history__list">
                  <?foreach ($item['SKILLS'] as $SKILL):?>
                      <tr class="history__item">
                          <?=$SKILL?>
                      </tr>
                  <?endforeach;?>
              </table>
            </div>
        <?endif?>
      </div>
    </div>
    <?php
}
    @include_once 'core/footer.php';
}