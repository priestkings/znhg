<?php

namespace app\modules\api\models\agent;

use Yii;

/**
 * This is the model class for table "{{%yy_goods}}".
 *
 * @property string $id
 * @property string $name
 * @property string $price
 * @property string $original_price
 * @property string $detail
 * @property string $cat_id
 * @property integer $status
 * @property string $service
 * @property string $sort
 * @property string $virtual_sales
 * @property string $cover_pic
 * @property string $addtime
 * @property integer $is_delete
 * @property string $sales
 * @property string $shop_id
 * @property string $store_id
 * @property string $coupon
 * @property string $integral
 * @property string $stock
 * @property string $buy_max
 * @property string $buy_max_day
 *
 *
 *
 */
class Goods extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%agent_goods}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'price', 'original_price', 'detail', 'service', 'store_id'], 'required'],
            [['level','price', 'original_price','stock'], 'number'],
            [['detail', 'cover_pic'], 'string'],
            [['cat_id', 'status', 'sort', 'virtual_sales', 'addtime', 'is_delete', 'sales', 'store_id'], 'integer'],
            [['name','shop_id'], 'string', 'max' => 255],
            [['service'], 'string', 'max' => 2000],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => '身份名称',
            'price' => '预约金额',
            'original_price' => '原价',
            'detail' => '身份详情，图文',
            'cat_id' => '身份分类',
            'status' => '上架状态【1=> 上架，2=> 下架】',
            'service' => '服务选项',
            'sort' => '身份排序 升序',
            'virtual_sales' => '虚拟销量',
            'cover_pic' => '身份缩略图',
            'addtime' => '添加时间',
            'is_delete' => '是否删除',
            'sales' => '实际销量',
            'stock' => '库存',
            'shop_id' => '门店id',
            'store_id' => 'Store ID',
            'coupon' => 'Store ID',
            'integral' => 'Store ID',
            'buy_max' => '最多允许购买',
            'buy_max_day' => '每日最多购买',
            'return_coupon' => '欢乐豆',
            'return_integral' => '积分',
            'level' => '等级',
        ];
    }

    /**
     * @return static[]
     * 身份图集
     */
    public function goodsPicList()
    {
        return GoodsPic::findAll(['goods_id'=>$this->id,'is_delete'=>0]);
    }
}
