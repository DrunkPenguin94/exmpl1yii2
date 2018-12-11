<?php
/**
 * Created by PhpStorm.
 * User: Drunk penguin
 * Date: 21.03.2018
 * Time: 20:44
 */
use yii\helpers\HtmlPurifier;
?>



<div class="ads_c_container orders">
    <div class="ads_cont">
<?php
foreach($model as $value) {
    ?>



            <a href="<?= $value->link?>" class="ads_c-a" target="_blank">
                <div class="ads-c-block">
                    <img src="<?= $value->linkImg?>"/>
                    <div class="ads-c-text"><?=HtmlPurifier::process(reductionText($value->text,130))?></div>
                </div>
            </a>






    <?php
}
?>
<?php
for($value=0;$value<$count_null;$value++){
    ?>




        <div class="ads-c-block">
            <img src="/img/zaglush-adv.jpg"/>
            <div class="ads-c-text">Здесь может быть ваша реклама</div>
        </div>







    <?php
}
?>
    </div>
</div>
