<template>  
  <div class="client-list-wrapper">
    <h1 class="page-title">Список клиентов</h1>
    <table>
        <thead>
            <tr>
                <td>Имя клиента</td>
                <td>Номер телефона</td>
                <td>Опции</td>
                <td>Вещи</td>
                <td>Доставки</td>
            </tr>
        </thead>
        <tbody>
            <tr class="client-item" v-for="client in clients" v-bind:key="client.id">
                <td class="client-name">{{ client.name }}</td>
                <td class="client-phone"><b>{{ client.phone }}</b></td>
                <td class="client-control">
                    <button v-on:click="addDelivery(client.id)">Добавить доставку</button>
                    <button v-on:click="addThing(client.id)">Добавить вещь</button>
                </td>
                <td class="client-things">
                    <ul>
                        <li v-for="thing in client.things" v-bind:key="thing.id">{{ thing.name }} <b>{{ thing.price }} p.</b></li>
                    </ul>
                </td>
                <td class="client-deliveries">
                    <ul>
                        <li v-for="delivery in client.deliveries" v-bind:key="delivery.id">
                            <p><b>Адрес доставки: </b> {{ delivery.adress }}</p>
                            <p><b>Дата доставки(с): </b> {{ delivery.timeFrom }}</p>
                            <p><b>Дата доставки(по): </b> {{ delivery.timeTo }}</p>
                            <p v-if="client.deliveries.filter(x => x.deliveryId == delivery.id).length">
                                <b>Доставляемая вещь: </b> 
                                <span v-for="deliveryThing in client.deliveries.filter(x => x.deliveryId == delivery.id)" v-bind:key="deliveryThing.id">
                                    {{deliveryThing.name}}
                                </span>  
                            </p>
                        </li>
                    </ul>
                </td>
            </tr>
        </tbody>
    </table>

    <button v-on:click="addClient()">Добавить клиента</button>

    <div class="modal-wrapper add-delivery_wrapper" v-if="newDelivery">
        <div class="modal-inner add-delivery_form">
            <button class="close-modal" v-on:click="canselAddDelivery()">x</button>
            <div class="form-title">
                <h3>Новая доставка</h3>
            </div>
            <div class="form-row">
                <label>
                    <span>Выберите дату</span>
                    <input type="datetime-local" v-model="newDelivery.timeFrom" />
                </label>
            </div>
             <div class="form-row">
                <label>
                    <span>Выберите дату</span>
                    <input type="datetime-local" v-model="newDelivery.timeTo" />
                </label>
            </div>
            <div class="form-row">
                <label>
                    <span>Адресс доставки</span>
                    <input v-model="newDelivery.address" />
                </label>
            </div>
            <!--<div class="form-row" v-if="clients.find(x => x.id == newDelivery.clientId).things.length">
                <label>
                    <span>Доставляемая вещь</span>
                    <select v-model="newDelivery.thingId">
                        <option 
                            v-for="deliveryThing in clients.find(x => x.id == newDelivery.clientId).things" 
                            v-bind:key="deliveryThing.id" 
                            :value="deliveryThing.id">
                            {{deliveryThing.name}}
                        </option>
                    </select>
                </label>
            </div>-->
            <div class="form-row">
                <button  v-on:click="saveDelivery()">Добавить доставку</button>
            </div>
        </div>
    </div>

    <div class="modal-wrapper add-delivery_wrapper" v-if="newThing">
        <div class="modal-inner add-delivery_form">
            <button class="close-modal" v-on:click="canselAddThing()">x</button>
            <div class="form-title">
                <h3>Новая вещь</h3>
            </div>
            <div class="form-row">
                <label>
                    <span>Наименование</span>
                    <input type="text" v-model="newThing.name" />
                </label>
            </div>
            <div class="form-row">
                <label>
                    <span>Цена</span>
                    <input type="number" v-model="newThing.price" />
                </label>
            </div>

            <div class="form-row">
                <button  v-on:click="saveThing()">Добавить вещь</button>
            </div>
        </div>
    </div>

    <div class="modal-wrapper add-client_wrapper" v-if="newClient">
        <div class="modal-inner add-client_wrapper_form">
            <button class="close-modal" v-on:click="canselAddClient()">x</button>
            <div class="form-title">
                <h3>Новый клиент</h3>
            </div>
            <div class="form-row">
                <label>
                    <span>Имя клиента</span>
                    <input type="text" v-model="newClient.name" />
                </label>
            </div>
            <div class="form-row">
                <label>
                    <span>Номер телефона</span>
                    <input type="text" v-model="newClient.phone" />
                </label>
            </div>

            <div class="form-row">
                <button  v-on:click="saveClient()">Добавить клиента</button>
            </div>
        </div>
    </div>

  </div>
</template>

<script>

var Delivery = function(clientId) {
    this.clientId = clientId;
    //this.id = Math.floor(Math.random() * 10000);
};
Delivery.prototype = {
    adress:      new String(),
    timeFrom:    null,
    timeTo:      null,
    thingId:     null,
};

var Thing = function(clientId) {
    this.clientId = clientId;
    //this.id = Math.floor(Math.random() * 10000);
};
Thing.prototype = {
    deliveryId:     null,
    name:           new String(),
    price:          new Number(),
};

var Client = function() {
    //this.id = Math.floor(Math.random() * 10000);
    this.deliveries = new Array();
    this.things = new Array();
};
Client.prototype = {
    name:       new String(),
    phone:      new String(),
};

import axios from 'axios'

export default {
  name: 'Clients',
  data () {
    return {
        clients: [],
        newClient: null,
        selectedClientId: null,
        newDelivery: null,
        newThing: null,
    }
  },
  created() {
    this.getClients_api();
  }
  methods: {
        //---delivery-controls-area---
        addDelivery: function(clientId) {
            this.newDelivery = new Delivery(clientId);
        },
        canselAddDelivery: function() {
            this.newDelivery = null
        },
        saveDelivery: function() {
            if(!this.newDelivery.time && !this.newDelivery.adress) {
                alert("Форма не заполнена");
                return;
            }
            this.newDelivery.time = new Date(this.newDelivery.time);
            this.clients.find(x => x.id == this.newDelivery.clientId).deliveries.push(this.newDelivery);
            this.updateClientData_api(this.clients.find(x => x.id == this.newDelivery.clientId));
            this.newDelivery = null;
        },
        //---thing-controls-area---
        addThing: function(clientId) {
            this.newThing = new Thing(clientId);
        },
        canselAddThing: function() {
            this.newThing = null
        },
        saveThing: function() {
            this.clients.find(x => x.id == this.newThing.clientId).things.push(this.newThing);
            this.updateClientData_api(this.clients.find(x => x.id == this.newThing.clientId));
            this.newThing = null;
        }, 
        //---client-controls-area---
        addClient: function() {
            this.newClient = new Client();
        },
        saveClient: function() {
            if(!this.newClient.name || !this.newClient.phone) {
                alert("Форма не заполнена");
                return;
            }
            this.newClient = this.saveClientData_api(this.newClient);
            this.clients.push(this.newClient);
            this.newClient = null;
        },
        canselAddClient: function() {
            this.newClient = null;
        },
        //--call-API-methods---
        getClients_api: function() {
            axios.get("http://local-api-host/client/").then(res => {
                this.clients = res;
            }).catch(e => {
                alert('При получении списка клиентов произошла ошибка.');
            });
        },
        saveClientData_api: function(clientData) {
            axios.post("http://local-api-host/client/add-client", {'newClient': clientData }).then(res => {
                clientData.id = res;
                alert('Клиент добавлен');
            }).catch(e => {
                alert('При сохранении данных произошла ошибка.');
            });
            return clientData;
        },
        updateClientData_api: function(clientData) {
            axios.post("http://local-api-host/client/update-client-record", {'client': clientData }).then(res => {
                alert('Данные по клиенту обновлены');
            }).catch(e => {
                alert('При сохранении данных произошла ошибка.');
            });
        }
  }
}
</script>

<style>
* {
    box-sizing: border-box;
    margin: 0px;
    padding: 0px;
}
h1, h2, h3, p, span, b, td {
    font-family: "Arial";
}
h1 {
    margin-bottom: 15px;
    padding-left: 10px;
    font-weight: 400;
}
td {
    padding: 10px;
    vertical-align: top;
    text-align: left;
}
ul > li {
    margin-bottom: 8px;
}
table {
    width: 100%;
}
button {
    padding: 12px 16px;
    font-size: 16px;
    border: none;
    border-radius: 5px;
}
.client-list-wrapper {
    padding: 15px;
}

.modal-wrapper {
    position: fixed;
    top: 0px;
    left: 0px;
    width: 100%;
    height: 100%;
    background: rgba(255,255,255,0.75);
    display: flex;
    align-items: center;
    justify-content: center;
}

.modal-wrapper .modal-inner {
     box-shadow: -2px -2px 12px #bfbfbf;
     padding: 15px;
     border-radius: 5px;
     position: relative;
}

.form-row {
    margin-top: 10px;
}

.form-row input, .form-row select {
    margin-left: 10px;
    padding: 12px 16px;
    border: none;
    background: #f1f1f1;
    border-radius: 5px;
}

.form-row label > span {
    display: inline-block;
    width: 120px;
}

.modal-wrapper .modal-inner .close-modal {
    position: absolute;
    top: 5px;
    right: 5px;
    background: transparent;
    border: none;
    cursor: pointer;
    font-size: 16px;
}

</style>