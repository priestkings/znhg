<?php

namespace app\modules\mch\models\crowdc;

use Yii;
use Codeception\PHPUnit\ResultPrinter\HTML;

/**
 * This is the model class for table "{{%order_form}}".
 *
 * @property integer $id
 * @property integer $store_id
 * @property integer $order_id
 * @property string $key
 * @property string $value
 * @property integer $is_delete
 */
class OrderForm extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%crowdc_order_form}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['store_id', 'order_id', 'is_delete'], 'integer'],
            [['key', 'value'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'store_id' => 'Store ID',
            'order_id' => 'Order ID',
            'key' => '表单key',
            'value' => '表单value',
            'is_delete' => 'Is Delete',
        ];
    }
    public function beforeSave($insert)
    {
        $this->value = \yii\helpers\Html::encode($this->value);
        return parent::beforeSave($insert);
    }
}
