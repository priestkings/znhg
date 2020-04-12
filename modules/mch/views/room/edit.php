<?php
/**
 * User: Xany <762632258@qq.com>
 * Date: 2017/11/25
 * Time: 15:42
 */

$urlManager = Yii::$app->urlManager;
$this->title = '直播间编辑';
$this->params['active_nav_group'] = 12;
$returnUrl = Yii::$app->request->referrer;
if (!$returnUrl)
    $returnUrl = $urlManager->createUrl(['mch/room/index']);
?>
<div class="panel mb-3">
    <div class="panel-header"><?= $this->title ?></div>
    <div class="panel-body">
        <form class="form auto-form" method="post" autocomplete="off" return="<?= $returnUrl ?>">
            <div class="form-body">
                <div class="form-group row">
                    <div class="form-group-label col-3 text-right">
                        <label class="col-form-label required">直播间名称</label>
                    </div>
                    <div class="col-9">
                        <input class="form-control" name="name" value="<?= $model->name ?>">
                        <div class="fs-sm text-danger">在直播间编辑--选择直播间时显示</div>
                    </div>
                </div>

                <div class="form-group row">
                    <div class="form-group-label col-3 text-right">
                        <label class="col-form-label required">腾讯id</label>
                    </div>
                    <div class="col-9">
                        <input class="form-control" name="room_id" value="<?= $model->room_id ?>">
                        <div class="fs-sm text-danger">腾讯id</div>
                    </div>
                </div>


                <div class="form-group row">
                    <div class="form-group-label col-3 text-right">
                        <label class="col-form-label required">live_status</label>
                    </div>
                    <div class="col-9">
                        <input class="form-control" name="live_status" value="<?= $model->live_status ?>">
                        <div class="fs-sm text-danger">live_status</div>
                    </div>
                </div>

                <div class="form-group row">
                    <div class="form-group-label col-3 text-right">
                        <label class="col-form-label required">直播间图片</label>
                    </div>
                    <div class="col-9">

                        <div class="upload-group">
                            <div class="input-group">
                                <input class="form-control file-input" name="pic_url" value="<?=$model->pic_url?>">
                            <span class="input-group-btn">
                                <a class="btn btn-secondary upload-file" href="javascript:" data-toggle="tooltip"
                                   data-placement="bottom" title="上传文件">
                                    <span class="iconfont icon-cloudupload"></span>
                                </a>
                            </span>
                            <span class="input-group-btn">
                                <a class="btn btn-secondary select-file" href="javascript:" data-toggle="tooltip"
                                   data-placement="bottom" title="从文件库选择">
                                    <span class="iconfont icon-viewmodule"></span>
                                </a>
                            </span>
                            <span class="input-group-btn">
                                <a class="btn btn-secondary delete-file" href="javascript:" data-toggle="tooltip"
                                   data-placement="bottom" title="删除文件">
                                    <span class="iconfont icon-close"></span>
                                </a>
                            </span>
                            </div>
                            <div class="upload-preview text-center upload-preview">
                                <span class="upload-preview-tip">88&times;88</span>
                                <img class="upload-preview-img" src="<?=$model->pic_url?>">
                            </div>
                        </div>

                    </div>
                </div>
                <div class="form-group row">
                    <div class="form-group-label col-3 text-right">
                        <label class="col-form-label required">直播间描述</label>
                    </div>
                    <div class="col-9">
                        <input class="form-control" name="content" value="<?= $model->content ?>">
                        <div>用于直播间营销</div>
                        <div class="text-danger fs-sm">直播间会在用户打赏后，自动发放给主播</div>
                    </div>
                </div>
                <div class="form-group row">
                    <div class="form-group-label col-3 text-right">
                    </div>
                    <div class="col-9">
                        <a class="btn btn-primary auto-form-btn" href="javascript:">保存</a>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
<script>
    $(document).on('click', '.del', function () {
        var a = $(this);
        $.myConfirm({
            content: a.data('content'),
            confirm: function () {
                $.ajax({
                    url: a.data('url'),
                    type: 'get',
                    dataType: 'json',
                    success: function (res) {
                        if (res.code == 0) {
                            window.location.reload();
                        } else {
                            $.myAlert({
                                title: res.msg
                            });
                        }
                    }
                });
            }
        });
        return false;
    });
</script>
