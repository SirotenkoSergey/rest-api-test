<?php

namespace app\models;

class Client extends \yii\base\BaseObject implements \yii\web\IdentityInterface {

    public $id;
    public $name;
    public $phone;
    public $deliveries;
    public $things;

    public static function getAll()
    {
        $result = [];
        foreach (self::$clients as $client) {
            $result[] = [
                'id'            => $client['id'],
                'name'          => $client['username'],
                'phone'         => $client['phone'],
                'deliveries'    => Deliveriy::find()->where(['clientId' => $client['id']),
                'things'        => Thing::find()->where(['clientId' => $client['id']),
            ];
        }
        return $result;
    }
}

?>