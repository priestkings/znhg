<?php
/**
 * User: Xany <762632258@qq.com>
 * Date: 2017/7/1
 * Time: 23:33
 */

namespace app\modules\mch\models;


use app\models\Business;
use app\models\Card;
use app\models\Cat;
use app\models\Goods;
use app\models\GoodsPic;
use app\models\Order;
use app\models\OrderDetail;
use app\models\User;
use yii\data\Pagination;

class BusinessListForm extends Model
{
    public $store_id;
    public $keyword;
    public $cat_id;
    public $page;
    public $limit;

    public $sort;
    public $sort_type;

    public function rules()
    {
        return [
            [['page'],'default','value'=>1],
            [['limit'],'default','value'=>20]
        ];
    }

    public function search1()
    {
        if(!$this->validate()){
            return $this->getModelError();
        }

        $query = Card::find()->where(['is_delete'=>0,'store_id'=>$this->store_id]);

        $count = $query->count();

        $p = new Pagination(['totalCount'=>$count,'pageSize'=>$this->limit]);
        $list = $query->offset($p->offset)->limit($p->limit)->orderBy(['addtime'=>SORT_DESC])->asArray()->all();

        return [
            'list'=>$list,
            'row_count'=>$count,
            'pagintion'=>$p
        ];

    }

    public function search()
    {
        if (!$this->validate())
            return $this->getModelError();
        $query = Business::find()->alias('g')->where([
            'g.status' => 1,
            'g.is_exchange' => 0,
            'g.is_delete' => 0,
        ])->orderBy('g.addtime DESC');
        if ($this->store_id)
            $query->andWhere(['g.store_id' => $this->store_id]);
        if ($this->keyword)
            $query->andWhere(['LIKE', 'g.name', $this->keyword]);
        $count = $query->count();
        $pagination = new Pagination(['totalCount' => $count, 'pageSize' => $this->limit, 'page' => $this->page - 1]);
        $list = $query
            ->limit($pagination->limit)
            ->offset($pagination->offset)
            ->asArray()->all();

        foreach ($list as $i => $item) {
            if (!$item['pic_url']) {
                $list[$i]['avatar_url'] =  User::findOne(['id' => $item['user_id'], 'store_id' => $this->store_id])->avatar_url;
                $list[$i]['nickname'] =  User::findOne(['id' => $item['user_id'], 'store_id' => $this->store_id])->nickname;
                $list[$i]['wechat_open_id'] =  User::findOne(['id' => $item['user_id'], 'store_id' => $this->store_id])->wechat_open_id;
                $list[$i]['hld'] =  User::findOne(['id' => $item['user_id'], 'store_id' => $this->store_id])->hld;
                $list[$i]['coupon'] =  User::findOne(['id' => $item['user_id'], 'store_id' => $this->store_id])->coupon;
                $list[$i]['integral'] =  User::findOne(['id' => $item['user_id'], 'store_id' => $this->store_id])->integral;
                $list[$i]['user_id'] =  User::findOne(['id' => $item['user_id'], 'store_id' => $this->store_id])->id;

            }
        }


        return [
            'list'=>$list,
            'row_count'=>$count,
            'pagination'=>$pagination
        ];
    }

    private function numToW($sales)
    {
        if($sales < 10000){
            return $sales;
        }else{
            return round($sales/10000,2).'W';
        }
    }
}