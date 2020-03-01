<?php
/**
 * User: Xany <762632258@qq.com>
 * Date: 2017/7/24
 * Time: 18:42
 */

namespace app\modules\mch\models\crowdapply;


use app\models\OrderDetail;
use app\models\Store;
use app\models\User;
use app\modules\mch\models\Model;

class OrderSendForm extends Model
{
    public $store_id;
    public $order_id;
    public $express;
    public $express_no;
    public $words;

    public function rules()
    {
        return [
            [['express', 'express_no','words'], 'trim'],
            [['express', 'express_no',], 'required','on'=>'EXPRESS'],
            [['order_id'],'required'],
            [['express', 'express_no',], 'string',],
            [['express', 'express_no',], 'default','value'=>''],
        ];
    }

    public function attributeLabels()
    {
        return [
            'express' => '快递公司',
            'express_no' => '快递单号',
            'words' => '商家留言',
        ];
    }

    public function save()
    {
        if (!$this->validate())
            return $this->getModelError();
        $order = Order::findOne([
            'is_delete' => 0,
            'store_id' => $this->store_id,
            'id' => $this->order_id,
            'is_pay' => 1,
        ]);
        if (!$order) {
            return [
                'code' => 1,
                'msg' => '订单不存在或已删除',
            ];
        }
//        if ($order->is_send == 1) {
//            return [
//                'code' => 1,
//                'msg' => '该订单已发货',
//            ];
//        }
        $order->is_use = 1;
        $order->express_no = $this->express_no;
        $order->use_time = time();
        if ($order->save()) {
            try {
                $wechat_tpl_meg_sender = new WechatTplMsgSender($this->store_id, $order->id, $this->getWechat());
                $wechat_tpl_meg_sender->sendMsg();
            } catch (\Exception $e) {
                \Yii::warning($e->getMessage());
            }
            return [
                'code' => 0,
                'msg' => '使用成功',
            ];
        } else {
            return [
                'code' => 1,
                'msg' => '操作失败',
            ];
        }

    }

    /**
     * @deprecated 已废弃
     */
    private function sendMessage($order)
    {
        $user = User::findOne($order->user_id);
        if (!$user)
            return;
        /* @var FormId $form_id */
        $form_id = FormId::find()->where(['order_no' => $order->order_no])->orderBy('addtime DESC')->one();
        $wechat = $this->getWechat();
        if (!$wechat)
            return;
        if (!$form_id)
            return;
        $store = Store::findOne($this->store_id);
        if (!$store || !$store->order_send_tpl)
            return;

        $goods_list = OrderDetail::find()
            ->select('g.name,od.num')
            ->alias('od')->leftJoin(['g' => Goods::tableName()], 'od.goods_id=g.id')
            ->where(['od.order_id' => $order->id, 'od.is_delete' => 0])->asArray()->all();

        $msg_title = '';
        foreach ($goods_list as $goods) {
            $msg_title .= $goods['name'];
        }


        $access_token = $this->wechat->getAccessToken();
        $api = "https://api.weixin.qq.com/cgi-bin/message/wxopen/template/send?access_token={$access_token}";
        $data = (object)[
            'touser' => $user->wechat_open_id,
            'template_id' => $store->order_send_tpl,
            'form_id' => $form_id->form_id,
            'page' => 'pages/order/order?status=2',
            'data' => (object)[
                'keyword1' => (object)[
                    'value' => $msg_title,
                    'color' => '#333333',
                ],
                'keyword2' => (object)[
                    'value' => $order->express,
                    'color' => '#333333',
                ],
                'keyword3' => (object)[
                    'value' => $order->express_no,
                    'color' => '#333333',
                ],
            ],
        ];
        $data = json_encode($data, JSON_UNESCAPED_UNICODE);
        $wechat->curl->post($api, $data);
        $res = json_decode($wechat->curl->response, true);
        if (!empty($res['errcode']) && $res['errcode'] != 0) {
            \Yii::warning("模板消息发送失败：\r\ndata=>{$data}\r\nresponse=>" . json_encode($res, JSON_UNESCAPED_UNICODE));
        }
    }
}