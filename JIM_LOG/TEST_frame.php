<!DOCTYPE HTML>
<html>
   <head>
   <meta charset="utf-8">
   <title>簡單測試</title>
      <script type="text/javascript">
        var roomId_key =<?=$_GET["room"];?>;
       function WebSocketTest() {
     if ("WebSocket" in window) {
         //alert("您的浏览器支持 WebSocket!");

         // 打开一个 web socket
         //var ws = new WebSocket("ws://172.17.10.30:9511");
        //var ws = new WebSocket("ws://172.17.10.30/majong_websocket");
        var ws = new WebSocket("ws://172.17.10.31:9511");
         ws.onopen = function() {
             // Web Socket 已连接上，使用 send() 方法发送数据
           var sendObj = {
                 roomId: roomId_key,
                 player: 1,
                 type: 101,
                 event: 'JoinRoom',
                 data: ""
             };
             ws.send(JSON.stringify(sendObj));

             var sendObj = {
                 roomId: roomId_key,
                 player: 1,
                 type: 0,
                 event: 'initCard',
                 data: ""
             };
             ws.send(JSON.stringify(sendObj));
             ///ws.send("发送数据");
             //alert("数据发送中...");
         };

         ws.onmessage = function(evt) {
             var received_msg = evt.data;
             //obj = JSON.parse(received_msg);
             //alert(obj);

             //alert("数据已接收..."+ received_msg );

             obj = JSON.parse(received_msg);
             //alert(obj);
             //document.getElementById("demo2").innerHTML = obj.event;
             if (obj.event == 'initCard') {
                 document.getElementById("demo").innerHTML = "產生牌盒" + evt.data;
                 //alert("抽牌");
                 var sendObj = {
                     roomId: roomId_key,
                     player: 1,
                     type: 1,
                     event: 'getCard',
                     data: 0
                 };
                 ws.send(JSON.stringify(sendObj));
             }
             if (obj.event == 'getCard') {
                 document.getElementById("demo2").innerHTML = "開局+取牌" + evt.data;

var n1 = JSON.parse("" + obj.Round + "");
var n = JSON.stringify(n1);
var n1 = JSON.parse("" + obj.Round + "");
var n = JSON.stringify(n1);

var endCard1 = JSON.parse("[" + obj.cardData.endCard + "]");
var EDlength = endCard1.length ;
//alert(EDlength ) ;
if(EDlength > 0  )
{
    var start = new Date().getTime();
    while (new Date().getTime() < start + 300); // JS 模擬sleep 每0.1秒打一張
    switch (n) {
            case '1234':
    var player_hand = JSON.parse("[" + obj.cardData.player1 + "]");
                 //alert(player_hand[13]);
                  var sendObj = {
                     roomId: roomId_key,
                     player: 1,
                     type: 2,
                     event: 'outCard',
                     data: player_hand[13]
                 };
                 ws.send(JSON.stringify(sendObj));
            break;
            case '2341':
 var player_hand = JSON.parse("[" + obj.cardData.player2+ "]");
                 //alert(player_hand[13]);
                  var sendObj = {
                     roomId: roomId_key,
                     player: 2,
                     type: 2,
                     event: 'outCard',
                     data: player_hand[13]
                 };
                 ws.send(JSON.stringify(sendObj));
            break;
            case '3412':
 var player_hand = JSON.parse("[" + obj.cardData.player3 + "]");
                 //alert(player_hand[13]);
                  var sendObj = {
                     roomId: roomId_key,
                     player: 3,
                     type: 2,
                     event: 'outCard',
                     data: player_hand[13]
                 };
                 ws.send(JSON.stringify(sendObj));
            break;
            case '4123':
 var player_hand = JSON.parse("[" + obj.cardData.player4 + "]");
                 //alert(player_hand[13]);
                  var sendObj = {
                     roomId: roomId_key,
                     player: 4,
                     type: 2,
                     event: 'outCard',
                     data: player_hand[13]
                 };
                 ws.send(JSON.stringify(sendObj));
            break;

            default:
            //alert("NO");

            }
    }else{
        var d = new Date()
     var dd = d.toLocaleString() ;
     //alert( dd ) ;
         if( dd < "2017-5-25 14:30:00")
     {

             var sendObj = {
                 roomId: roomId_key,
                 player: 1,
                 type: 0,
                 event: 'initCard',
                 data: ""
             };
             ws.send(JSON.stringify(sendObj));
             ///ws.send("发送数据");
             //alert("数据发送中...");
     }
    }

             }
             if (obj.event == 'outCard') {
                 document.getElementById("demo3").innerHTML = evt.data;
                 //if(obj.Round == '1234' )
                 var n1 = JSON.parse("" + obj.Round + "");
                 var n = JSON.stringify(n1);
             }

         };

         ws.onclose = function() {
             // 关闭 websocket
             alert("连接已关闭...");
         };

     } else {
         // 浏览器不支持 WebSocket
         alert("您的浏览器不支持 WebSocket!");
     }
 }

      </script>

   </head>
   <body onload="javascript:WebSocketTest()">

    <p id="demo"></p>
    <p id="demo2"></p>
    <p id="demo2_1"></p>
    <p id="demo3"></p>
    <p id="demo4"></p>
    <p id="list"></p>

   </body>
</html>