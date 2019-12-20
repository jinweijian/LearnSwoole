<!DOCTYPE html>
<html lang="zh-CN">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="">
        <meta name="keywords" content="">
        <title>视频弹幕Demo</title>
        <style>
            .canvas-barrage {
    position: absolute;
    width: 960px;
                height: 540px;
                pointer-events: none;
                z-index: 1;
            }
            .ui-input {
    height: 20px;
                width: 856px;
                line-height: 20px;
                border: 1px solid #d0d0d5;
                border-radius: 4px;
                padding: 9px 8px;
            }
            .ui-button {
    display: inline-block;
    background-color: #ff5d4d;
                line-height: 28px;
                text-align: center;
                border-radius: 4px;
                color: #fff;
                font-size: 14px;
            }
        </style>
    </head>
    <body>
            <p>

                <input class="ui-input" id="beid" name="value" value="1049" required>
            </p>
            <p>
                <input class="ui-input" id="msg" name="value" value="发送弹幕" required>
            </p>
            <p>
                <input class="ui-input" id="testId" name="value" value="10037" required>
            </p>
            <p>
                <input class="ui-button" type="button" id="sendBtn" value="发送弹幕">
            </p>

</body>

<script>
    if (!("WebSocket" in window)) {
        alert("您的浏览器不支持 WebSocket!");
    }
    // 弹幕数据
    var dataBarrage = [{
        value: '',
        time: 0, // 单位秒
        speed: 0,
        fontSize: 0
    }];

    var itemsColor = ['#FFA54F','#FF4040','#EE1289', '#8E8E38', '#3A5FCD', '#00EE76', '#388E8E', '#76EEC6', '#87CEFF', '#7FFFD4'];

    var eleCanvas = document.getElementById('canvasBarrage');
    var eleVideo = document.getElementById('videoBarrage');

    //         var barrage = new CanvasBarrage(eleCanvas, eleVideo, {
    // data: dataBarrage
    //         });

    var wsServer = 'ws://192.168.56.103:9501/';
    var ws = new WebSocket(wsServer);
    var testId = document.getElementById('testId').value;

    ws.onopen = function (evt) {

        console.log('链接参数:' + evt);
        if (ws.readyState == 1) {

            ws.send(JSON.stringify({
                beid: document.getElementById('beid').value,
                msg: '登陆一下哦',
                testId: testId,
                type: 'login',
            }));
            console.log('WebSocket 连接成功...');
        } else {
            console.log('WebSocket 连接失败...');
        }
    };

    ws.onmessage = function (evt) {

        // barrage.add({
        //     value: evt.data,
        //     time: eleVideo.currentTime,
        //     speed: 5,
        //     color: itemsColor[Math.floor(Math.random()*itemsColor.length)]
        //     // 其它如 fontSize, opacity等可选
        // });
        console.log('服务器请求回来的数据： ' + evt.data);
    };

    ws.onerror = function (evt) {
        alert('WebSocket 发生错误');
        console.log(evt);
    };

    ws.onclose = function() {
        alert('WebSocket 连接关闭');
        console.log('WebSocket 连接关闭...');
    };

    var sendBtn = document.getElementById('sendBtn');
    sendBtn.onclick = function(){
        if (ws.readyState == 1) {
            ws.send(JSON.stringify({
                beid: document.getElementById('beid').value,
                msg: document.getElementById('msg').value,
                testId: testId,
                type: 'speak'
            }));
        } else {
            alert('WebSocket 连接失败');
        }
    }
</script>
</html>