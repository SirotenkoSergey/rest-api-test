<?php
namespace app\controllers;

use app\models\Client;
use app\models\Delivery;
use app\models\Thing;

use yii\rest\ActiveController;

class ClientController extends ActiveController
{
    public function actionIndex() {
        return User::getAll();
    }

    public function actionAddClient() {
        $newClient = $_POST['newClient'];
        $client = new Client();
        $client->name = $newClient['name'];
        $client->phone = $newClient['phone'];
        $client->save();

        foreach($newClient['deliveries'] as $delivery) {
            $newDelivery = new Delivery();
            $newDelivery->clientId = $client['id']
            $newDelivery->adress = $delivery['adress'];
            $newDelivery->timeFrom = $delivery['timeFrom'];
            $newDelivery->timeTo = $delivery['timeTo'];
            $newDelivery->save();
        }

        foreach($newClient['things'] as $thing) {
            $newThing = new Thing();
            $newThing->clientId = $client['id']
            $newThing->name = $thing['name'];
            $newThing->price = $thing['price'];
        }

        return $client;
    }

    public function actionUpdateClientRecords() {
        $client = $_POST['client'];
        //===clear old records===
        Delivery::deleteAll(['clientId' => $client['id']]);
        Thing::deleteAll(['clientId' => $client['id']]);
        //===add new records
        foreach($client['deliveries'] as $delivery) {
            $newDelivery = new Delivery();
            $newDelivery->clientId = $client['id']
            $newDelivery->adress = $delivery['adress'];
            $newDelivery->timeFrom = $delivery['timeFrom'];
            $newDelivery->timeTo = $delivery['timeTo'];
            $newDelivery->save();
        }

        foreach($client['things'] as $thing) {
            $newThing = new Thing();
            $newThing->clientId = $client['id']
            $newThing->name = $thing['name'];
            $newThing->price = $thing['price'];
        }
    }
}
?>