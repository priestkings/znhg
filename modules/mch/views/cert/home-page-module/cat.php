<?php
/**
 * User: Xany <762632258@qq.com>
 * Date: 2017/8/31
 * Time: 18:05
 */
?>
<div class="home-block">
    <div class="block-content">
        <?php if (isset($cat)): ?>
            <div class="block-name">分类：<?= $cat->name ?></div>
        <?php else: ?>
            <div class="block-name">所有分类</div>
        <?php endif; ?>
    </div>
    <img class="block-img" src="<?= Yii::$app->request->baseUrl ?>/statics/images/cat-bg.png">
</div>