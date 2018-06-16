<?php $ip=$_SERVER[REMOTE_ADDR];?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title></title>
    <style>
    #people{
        background:white;
        border-radius: 10px;
        padding: 30px;
        color: red;
        width:15%;
        height:430px;
        position:absolute;
        top:10px;
        font-size:14px;
        line-height:20px;
        left:74%;
        padding-top:70px;
    }

    #people-title{
        background:width;
        border:10px;
        padding: 30px;
        color:red;
        width:15%;
        position:absolute;
        top:10px;
        font-size:14px;
        line-height:20px;
        left:74%;
    }


    #screen{
        background:white;
        border-radius: 30px;
        padding: 30px;
        color: skyblue;
        width:65%;
        height:500px;
        overflow:scroll;
    }

    #data{
        background: white;
        margin-top:20px;
        margin-left:3px;
        margin-right:0px;
        border: none;
        width:74%;
        height:30px;
        padding-left:10px;
        
    }

    input[type="button"]{
        border: None;
        width: 10%;
        height:30px;
        margin:0px;
        cursor:pointer;
    }

    input[type="button"]:hover{
        background:pink;
    }
    input[type="button"]:active{
        background:#FFCC99;
    }
    </style>
</head>
<body style="background:#99CCCC">
    <div id="screen"> </div>
    <input type="" id="data" autofocus="autofocus">
    <input type="button" id="send"  value="Send" onclick="send()">
    <input type="button" id="cls"  value="Cls" onclick="cls()">

    <div id="people"></div>
    <div id="people-title" ><center>在线人数</center></div>
    </div>

</body>

<script>
    var ws = new WebSocket("ws://118.89.51.198:4321");
    document.getElementById("screen").innerHTML = "尝试连接服务器。。。" + "<br>";

    function send(){
        var msg = document.getElementById("data").value;
        ws.send(msg);
    }

    function cls(){
        document.getElementById("screen").innerHTML = "";
    }

    function sendAjax() {
        var xmlhttp;
        if (window.XMLHttpRequest)
            xmlhttp = new XMLHttpRequest();
        else
            xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");

        xmlhttp.onreadystatechange = function(){
            if (xmlhttp.readyState == 4 && xmlhttp.status==200)
                document.getElementById("people").innerHTML = xmlhttp.responseText;
        }

        xmlhttp.open("GET","getPeople.php",true);
        xmlhttp.send();
    }

    ws.onopen = function(){
        console.log("websocket connect ok");
        ws.send("<?php echo $ip;?>");
        document.getElementById("screen").innerHTML = "服务器成功连接!!!" +  "<br>"
    }

    ws.onmessage = function(evt){
        sendAjax();
        var screen = document.getElementById("screen");
        if(evt.data.match("::people::")){
            data = evt.data.replace(/::people::/,"");
            document.getElementById("people").innerHTML = data; 
        }
        else
            screen.innerHTML += evt.data +"<br>";
        screen.scrollTop = screen.scrollHeight;
    }

    document.body.addEventListener("keydown", function(event){
        if(event.keyCode == 13)
            document.getElementById("send").click();
        if(event.keyCode == 27){
            event.preventDefault();
            document.getElementById("data").value = "";
        }
    })

</script>
</html>
