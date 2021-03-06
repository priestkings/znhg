<?php

namespace app\modules\api\models\crowdapply;

use Yii;

/**
 * This is the model class for table "{{%yy_goods_pic}}".
 *
 * @property integer $id
 * @property integer $goods_id
 * @property string $pic_url
 * @property integer $is_delete
 */
class GoodsPic extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%crowdapply_goods_pic}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['goods_id', 'is_delete'], 'integer'],
            [['pic_url'], 'required'],
            [['pic_url'], 'string'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'goods_id' => 'Goods ID',
            'pic_url' => 'Pic Url',
            'is_delete' => 'Is Delete',
        ];
    }
}
