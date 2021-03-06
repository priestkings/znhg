<?php
/**
 * User: Xany <762632258@qq.com>
 * Date: 2017/7/28
 * Time: 19:16
 */

namespace app\modules\api\models\bookmall;


use app\models\Level;
use app\models\Setting;
use app\models\Shop;
use app\models\Store;
use app\modules\api\models\Model;

class OrderDetailForm extends Model
{
    public $store_id;
    public $user_id;
    public $order_id;
    public $order_no;

    public function rules()
    {
        return [
            [['order_id'], 'required'],
        ];
    }

    public function search()
    {
        if (!$this->validate())
            return $this->getModelError();
        $order = Order::findOne([
            'store_id' => $this->store_id,
            'user_id' => $this->user_id,
            'id' => $this->order_id,
            'is_delete' => 0,
        ]);
        if (!$order)
            return [
                'code' => 1,
                'msg' => '订单不存在',
            ];
        $status = "";
        if ($order->is_pay == 0) {
            $status = '订单未付款';
        } elseif ($order->is_pay == 1 && $order->is_send == 0) {
            $status = '订单待发货';
        } elseif ($order->is_send == 1 && $order->is_confirm == 0) {
            $status = '订单已发货';
        } elseif ($order->is_confirm == 1) {
            $status = '订单已完成';
        }

        $goods_list = OrderDetail::find()->alias('od')->leftJoin(['g' => Goods::tableName()], 'od.goods_id=g.id')
            ->select('g.id,od.id AS order_detail_id,g.name,od.attr,od.num,od.total_price,od.pic')
            ->where(['od.order_id' => $order->id, 'od.is_delete' => 0])->asArray()->all();
        $num = 0;
        foreach ($goods_list as $i => $item) {
            $goods_list[$i]['attr'] = json_decode($item['attr']);
            $num += intval($item['num']);
            $goods_list[$i]['goods_pic'] = $item['pic']?:Goods::getGoodsPicStatic($item['id'])->pic_url;
            $order_refund = OrderRefund::findOne([
                'order_detail_id' => $item['order_detail_id'],
                'is_delete' => 0,
            ]);
            if ($order_refund) {
                $goods_list[$i]['is_order_refund'] = 1;
            } else {
                $goods_list[$i]['is_order_refund'] = 0;
            }
            if ($order->is_pay == 1 && $order->is_send == 1) {
                $goods_list[$i]['order_refund_enable'] = 1;
            } else {
                $goods_list[$i]['order_refund_enable'] = 0;
            }
            if ($order->is_confirm == 1) {
                $store = Store::findOne($this->store_id);
                if ((time() - $order->confirm_time) > $store->after_sale_time * 86400) {//超过可售后时间
                    $goods_list[$i]['order_refund_enable'] = 0;
                }
            }

        }
        if($order->shop_id){
            $shop = Shop::find()->select(['name','mobile','address','longitude','latitude'])->where(['store_id'=>$this->store_id,'id'=>$order->shop_id])->asArray()->one();
        }
        if($order->before_update_price){
            if($order->before_update_price < $order->pay_price){
                $before_update = "加价";
                $money = $order->pay_price - $order->before_update_price;
            }else{
                $before_update = "优惠";
                $money = $order->before_update_price - $order->pay_price;
            }
        }else{
            $before_update = "";
            $money = "";
        }
        return [
            'code' => 0,
            'msg' => 'success',
            'data' => [
                'order_id' => $order->id,
                'is_pay' => $order->is_pay,
                'is_send' => $order->is_send,
                'is_confirm' => $order->is_confirm,
                'status' => $status,
                'express' => $order->express,
                'express_no' => $order->express_no,
                'name' => $order->name,
                'mobile' => $order->mobile,
                'address' => $order->address,
                'order_no' => $order->order_no,
                'addtime' => date('Y-m-d H:i', $order->addtime),
                'total_price' => doubleval(sprintf('%.2f', $order->total_price)),
                'express_price' => doubleval(sprintf('%.2f', $order->express_price)),
                'goods_total_price' => doubleval(sprintf('%.2f', doubleval($order->total_price) - doubleval($order->express_price))),
                'coupon_sub_price' => $order->coupon_sub_price,
                'pay_price' => $order->pay_price,
                'num' => $num,
                'goods_list' => $goods_list,
                'is_offline'=>$order->is_offline,
                'content'=>$order->content?$order->content:"",
                'before_update'=>$before_update,
                'money'=>$money,
                'shop'=>$shop,
                'discount'=>$order->discount,
                'user_coupon_id'=>$order->user_coupon_id,
                'words'=>$order->words,
                'advance_coupon' => $order->advance_coupon,
                'advance_integral_buy' =>$order->advance_integral_buy,
                'yukuan_coupon' => $order->yukuan_coupon,
                'yukuan_integral_buy' => $order->yukuan_integral_buy,
            ],
        ];
    }
    public function clerk()
    {
        $order = Order::findOne([
            'store_id' => $this->store_id,
            'order_no' => $this->order_no,
            'is_delete' => 0,
        ]);
        if (!$order)
            return [
                'code' => 1,
                'msg' => '订单不存在',
            ];
        $status = "";
        if ($order->is_pay == 0) {
            $status = '订单未付款';
        } elseif ($order->is_pay == 1 && $order->is_send == 0) {
            $status = '订单待发货';
        } elseif ($order->is_send == 1 && $order->is_confirm == 0) {
            $status = '订单已发货';
        } elseif ($order->is_confirm == 1) {
            $status = '订单已完成';
        }

        $goods_list = OrderDetail::find()->alias('od')->leftJoin(['g' => Goods::tableName()], 'od.goods_id=g.id')
            ->select('g.id,od.id AS order_detail_id,g.name,od.attr,od.num,od.total_price')
            ->where(['od.order_id' => $order->id, 'od.is_delete' => 0])->asArray()->all();
        $num = 0;
        foreach ($goods_list as $i => $item) {
            $goods_list[$i]['attr'] = json_decode($item['attr']);
            $num += intval($item['num']);
            $goods_list[$i]['goods_pic'] = Goods::getGoodsPicStatic($item['id'])->pic_url;
            $order_refund = OrderRefund::findOne([
                'order_detail_id' => $item['order_detail_id'],
                'is_delete' => 0,
            ]);
            if ($order_refund) {
                $goods_list[$i]['is_order_refund'] = 1;
            } else {
                $goods_list[$i]['is_order_refund'] = 0;
            }
            if ($order->is_pay == 1 && $order->is_send == 1) {
                $goods_list[$i]['order_refund_enable'] = 1;
            } else {
                $goods_list[$i]['order_refund_enable'] = 0;
            }
            if ($order->is_confirm == 1) {
                $store = Store::findOne($this->store_id);
                if ((time() - $order->confirm_time) > $store->after_sale_time * 86400) {//超过可售后时间
                    $goods_list[$i]['order_refund_enable'] = 0;
                }
            }

        }

        return [
            'code' => 0,
            'msg' => 'success',
            'data' => [
                'order_id' => $order->id,
                'is_pay' => $order->is_pay,
                'is_send' => $order->is_send,
                'is_confirm' => $order->is_confirm,
                'status' => $status,
                'express' => $order->express,
                'express_no' => $order->express_no,
                'name' => $order->name,
                'mobile' => $order->mobile,
                'address' => $order->address,
                'order_no' => $order->order_no,
                'addtime' => date('Y-m-d H:i', $order->addtime),
                'total_price' => doubleval(sprintf('%.2f', $order->total_price)),
                'express_price' => doubleval(sprintf('%.2f', $order->express_price)),
                'goods_total_price' => doubleval(sprintf('%.2f', doubleval($order->total_price) - doubleval($order->express_price))),
                'coupon_sub_price' => $order->coupon_sub_price,
                'pay_price' => $order->pay_price,
                'num' => $num,
                'goods_list' => $goods_list,
                'is_offline'=>$order->is_offline,
                'content'=>$order->content?$order->content:""
            ],
        ];
    }
}