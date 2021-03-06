<?php
/**
 * User: Xany <762632258@qq.com>
 * Date: 2017/11/2
 * Time: 9:17
 */
$urlManager = Yii::$app->urlManager;
$this->title = '福利期数';
$this->params['active_nav_group'] = 4;
?>

<!--    <iframe style="height:1000px;width:1200px"  src="https://mta.qq.com/wechat_mini/base/ctr_realtime_data?app_id=500706424">-->
<!--    </iframe>>-->
        <div class="alert alert-info rounded-0">
     <div>注：福利每份奖励如果为0 系统将 总奖励/用户申请总份数*该用户拥有的份数！！</div>
    <div>注：每次用户申请后，在福利结束时间后，可以到小程序福利奖励/申请奖励，申请后请在奖金记录里点击发放</div>
    <div>注：如果福利奖励用户没有申请本次结算，将保留至下一次周期结算</div>
    <div>注福利分红每次申请为一份</div>
    <div>注：总份数超过所有用户申请后不可申请</div>
    <div>注：福利设置好需要等级</div>
    <div>注：全部禁用时候为前端下架结算中
        暂时未做导出
        <a target="_blank"
           href="<?= $urlManager->createUrl(['/mch/settlementstatistics/data/user1']) ?>">福利统计数据页面的最后6列</a>、
        <a target="_blank" href="<?= $urlManager->createUrl(['/mch/settlementstatistics/data/user']) ?>">查询自身消费情况</a>。
    </div>
</div>
<div class="panel mb-3">
    <div class="panel-header"><?= $this->title ?></div>
    <div class="panel-body">
        <a class="btn btn-primary" href="<?= $urlManager->createUrl(['mch/settlementstatistics/fuli/level-edit']) ?>">福利设置</a>
        <div class="float-right mb-4">
            <form method="get">

                <?php $_s = ['keyword'] ?>
                <?php foreach ($_GET as $_gi => $_gv):if (in_array($_gi, $_s)) continue; ?>
                    <input type="hidden" name="<?= $_gi ?>" value="<?= $_gv ?>">
                <?php endforeach; ?>

                <div class="input-group">
                    <input class="form-control"
                           placeholder="福利期数"
                           name="keyword"
                           autocomplete="off"
                           value="<?= isset($_GET['keyword']) ? trim($_GET['keyword']) : null ?>">
                    <span class="input-group-btn">
                    <button class="btn btn-primary">搜索</button>
                </span>
                </div>
            </form>
        </div>
        <table class="table table-bordered bg-white">
            <tr>
                <td>期数</td>
                <td>福利名称</td>
                <td>福利总数量</td>
                <td>福利总奖励</td>
                <td>福利每份需要张数</td>
                <td>福利每份奖励</td>
                <td>需要的等级</td>
                <td>本期结束时间 </td>
                <td>状态</td>
                <td>操作</td>
            </tr>
            <?php foreach ($list as $index => $value): ?>
                <tr>
                    <td class="nowrap"><?= $value['level'] ?></td>
                    <td class="nowrap"><?= $value['name'] ?></td>
                    <td class="nowrap"><?= $value['num'] ?></td>
                    <td class="nowrap"><?= $value['all_money'] ?></td>
                    <td class="nowrap"><?= $value['num'] ?></td>
                    <td class="nowrap"><?= $value['money'] ?></td>
                    <td class="nowrap"><?= $value['require_level'] ?></td>
                    <td class="nowrap"><?= date('Y-m-d', $value['end_fulichi_time'])?></td>
                    <td class="nowrap">
                        <?php if ($value['status'] == 1): ?>
                            <span class="badge badge-success">启用</span>
                            |
                            <a href="javascript:" class="status" data-type="0" data-id="<?= $value['id'] ?>">禁用</a>
                        <?php else: ?>
                            <span class="badge badge-danger">禁用</span>
                            |
                            <a href="javascript:" class="status" data-type="1" data-id="<?= $value['id'] ?>">启用</a>
                        <?php endif; ?>
                    </td>
                    <td class="nowrap">
                        <a class="btn btn-sm btn-primary"
                           href="<?= $urlManager->createUrl(['mch/settlementstatistics/fuli/level-edit', 'id' => $value['id']]) ?>">编辑</a>
                        <a class="btn btn-sm btn-danger del" href="javascript:" data-content="是否删除？"
                           data-url="<?= $urlManager->createUrl(['mch/settlementstatistics/fuli/level-del', 'id' => $value['id']]) ?>">删除</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
        <div class="text-center">
            <?= \yii\widgets\LinkPager::widget(['pagination' => $pagination,]) ?>
            <div class="text-muted"><?= $row_count ?>条数据</div>
        </div>
    </div>
</div>
<script>
    $(document).on('click', '.status', function () {
        var type = $(this).data('type');
        var id = $(this).data('id');
        var text = '';
        if (type == 0) {
            text = "禁用";
        } else {
            text = "启用";
        }
        $.myConfirm({
            title: '提示',
            content: '是否' + text + '？',
            confirm: function () {
                $.ajax({
                    url: "<?=$urlManager->createUrl(['mch/settlementstatistics/fuli/level-type'])?>",
                    dataType: 'json',
                    type: 'get',
                    data: {
                        type: type,
                        id: id
                    },
                    success: function (res) {
                        if (res.code == 0) {
                            window.location.reload();
                        } else {
                            $.myAlert({
                                title: '提示',
                                content: res.msg
                            });
                        }
                    }
                });
            }
        });
    });
</script>
<script>
    $(document).on('click', '.del', function () {
        var a = $(this);
        $.myConfirm({
            title: '提示',
            content: a.data('content'),
            confirm: function () {
                $.ajax({
                    url: a.data('url'),
                    dataType: 'json',
                    type: 'get',
                    success: function (res) {
                        if (res.code == 0) {
                            window.location.reload();
                        } else {
                            $.myAlert({
                                title: '提示',
                                content: res.msg
                            });
                        }
                    }
                });
            }
        });
    });
</script>
