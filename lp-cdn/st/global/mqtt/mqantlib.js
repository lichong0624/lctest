'use strict';
/**
 * Created by liangdas on 17/2/25.
 * Email 1587790525@qq.com
 */

var hashmap = function () {
}
hashmap.prototype = {
    constructor: hashmap,
    add: function (k, v) {
        if (!this.hasOwnProperty(k)) {
            this[k] = v;
        }
    },
    remove: function (k) {
        if (this.hasOwnProperty(k)) {
            delete this[k];
        }
    },
    update: function (k, v) {
        this[k] = v;
    },
    has: function (k) {
        var type = typeof k;
        if (type === 'string' || type === 'number') {
            return this.hasOwnProperty(k);
        } else if (type === 'function' && this.some(k)) {
            return true;
        }
        return false;
    },
    clear: function () {
        for (var k in this) {
            if (this.hasOwnProperty(k)) {
                delete this[k];
            }
        }
    },
    empty: function () {
        for (var k in this) {
            if (this.hasOwnProperty(k)) {
                return false;
            }
        }
        return true;
    },
    each: function (fn) {
        for (var k in this) {
            if (this.hasOwnProperty(k)) {
                fn.call(this, this[k], k, this);
            }
        }
    },
    map: function (fn) {
        var hash = new Hash;
        for (var k in this) {
            if (this.hasOwnProperty(k)) {
                hash.add(k, fn.call(this, this[k], k, this));
            }
        }
        return hash;
    },
    filter: function (fn) {
        var hash = new Hash;
        for (var k in this) {

        }
    },
    join: function (split) {
        split = split !== undefined ? split : ',';
        var rst = [];
        this.each(function (v) {
            rst.push(v);
        });
        return rst.join(split);
    },
    every: function (fn) {
        for (var k in this) {
            if (this.hasOwnProperty(k)) {
                if (!fn.call(this, this[k], k, this)) {
                    return false;
                }
            }
        }
        return true;
    },
    some: function (fn) {
        for (var k in this) {
            if (this.hasOwnProperty(k)) {
                if (fn.call(this, this[k], k, this)) {
                    return true;
                }
            }
        }
        return false;
    },
    find: function (k) {
        var type = typeof k;
        if (type === 'string' || type === 'number' && this.has(k)) {
            return this[k];
        } else if (type === 'function') {
            for (var _k in this) {
                if (this.hasOwnProperty(_k) && k.call(this, this[_k], _k, this)) {
                    return this[_k];
                }
            }
        }
        return null;
    }
};

var MQANT = function () {
}
MQANT.prototype = {
    constructor: window.MQANT,
    curr_id: 0,
    client: null,
    waiting_queue: new hashmap(),
    init: function (prop) {
        // prop["onFailure"] = prop["onFailure"] || function () {
        //     console.log("onFailure");
        // };
        // prop["onConnectionLost"] = prop["onConnectionLost"] || function (responseObject) {
        //     if (responseObject.errorCode !== 0) {
        //         console.log("onConnectionLost:" + responseObject.errorMessage);
        //         console.log("连接已断开");
        //     }
        // }
        prop["useSSL"] = prop["useSSL"] || false
        // this.client = new Paho.MQTT.Client(prop["host"], prop["port"], prop["client_id"]);
        this.client = mqtt.connect("ws://" + prop['host'] + ':' + prop['port'], {
            protocolId: 'MQIsdp',
            protocolVersion: 3,
            keepalive: 5,
            connectTimeout: 30 * 1000
        });
        // this.client.connect({
        //     onSuccess: prop["onSuccess"],
        //     onFailure: prop["onFailure"],
        //     mqttVersion: 3,
        //     useSSL: prop["useSSL"],
        //     cleanSession: true,
        // });//连接服务器并注册连接成功处理事件
        // this.client.onConnectionLost = prop["onConnectionLost"];//注册连接断开处理事件
        // this.client.onMessageArrived = onMessageArrived;//注册消息接收处理事件

        prop["onSuccess"] && this.client.on('connect', prop["onSuccess"]);
        prop["onOffline"] && this.client.on('offline', prop["onOffline"]);
        prop["onClose"] && this.client.on('close', prop["onClose"]);
        prop["onError"] && this.client.on('error', prop["onError"]);
        this.client.on('message', function (topic, message, packet) {

            var msg = byteToString(message);
            console.log("get msg is : "+msg);
            // var msg = String.fromCharCode.apply(null, message);
            var callback = self.waiting_queue.find(topic);
            if (typeof (callback) != "undefined") {
                //有等待消息的callback 还缺一个信息超时的处理机制
                var h = topic.split("/")
                if (h.length > 2) {
                    //这个topic存在msgid 那么这个回调只使用一次
                    self.waiting_queue.remove(topic)
                }
                msg = JSON.parse(msg);
                callback(msg);
            }
        });

        var self = this;
        // function onMessageArrived(message) {
        //     var callback = self.waiting_queue.find(message.destinationName);
        //     if (typeof (callback) != "undefined") {
        //         //有等待消息的callback 还缺一个信息超时的处理机制
        //         var h = message.destinationName.split("/")
        //         if (h.length > 2) {
        //             //这个topic存在msgid 那么这个回调只使用一次
        //             self.waiting_queue.remove(message.destinationName)
        //         }
        //         callback(message);
        //     }
        // }
    },
    /**
     * 向服务器发送一条消息
     * @param topic
     * @param msg
     * @param callback
     */
    request: function (topic, msg, callback) {
        msg = typeof(msg) === 'string' ? msg : JSON.stringify(msg);
        this.curr_id = this.curr_id + 1;
        topic += "/" + this.curr_id; //给topic加一个msgid 这样服务器就会返回这次请求的结果,否则服务器不会返回结果
        var payload = msg;
        this.on(topic, callback);
        this.client.publish(topic, payload, { qos: 1 });
    },
    /**
     * 向服务器发送一条消息,但不要求服务器返回结果
     * @param topic
     * @param msg
     */
    requestNR: function (topic, msg) {
        msg = typeof(msg) === 'string' ? msg : JSON.stringify(msg);

        var payload = msg;
        this.client.publish(topic, payload, { qos: 1 });
    },
    /**
     * 监听指定类型的topic消息
     * @param topic
     * @param callback
     */
    on: function (topic, callback) {
        //服务器不会返回结果
        this.waiting_queue.add(topic, callback); //添加这条消息到等待队列
    }
}

var mqant = new MQANT();
mqant.connect = function (config) {
    mqant.init(config);
};
window.mqant = mqant;

function byteToString(arr) {
    if(typeof arr === 'string') {
        return arr;
    }
    var str = '',
        _arr = arr;
    for(var i = 0; i < _arr.length; i++) {
        var one = _arr[i].toString(2),
            v = one.match(/^1+?(?=0)/);
        if(v && one.length == 8) {
            var bytesLength = v[0].length;
            var store = _arr[i].toString(2).slice(7 - bytesLength);
            for(var st = 1; st < bytesLength; st++) {
                store += _arr[st + i].toString(2).slice(2);
            }
            str += String.fromCharCode(parseInt(store, 2));
            i += bytesLength - 1;
        } else {
            str += String.fromCharCode(_arr[i]);
        }
    }
    return str;
}



