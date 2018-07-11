<?php
    session_start();

    class RegClass{
        public function __construct($appid, $appkey, $tampleid){
            # status code:
            # @0001  验证码发送成功
            # @1001  验证码校验成功
            # @1004  用户注册成功
            # @4001  验证码校验失败
            # @4002  用户名重复
            # @5001  验证码发送失败
            
            $this->status = array(
                "sendOk"=>"0001",
                "sendNo"=>"5001",
                "codeOk"=>"1001",
                "codeNo"=>"4001",
                "nameNo"=>"4002",
                "regiOk"=>"1004"
            );
            
            $this->appid = $appid;
            $this->appkey = $appkey;
            $this->tampleid = $tampleid;
            mysql_connect("localhost","root","yourpassword");  #自行更改
            mysql_select_db("lamp");                        #自行更改
            mysql_set_charset('utf8');
        }

        public function httpPost($url, $data){
            $con = curl_init((string)$url);
            curl_setopt($con, CURLOPT_POST, true);
            curl_setopt($con, CURLOPT_POSTFIELDS, $data);
            curl_setopt($con, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($con, CURLOPT_TIMEOUT, 5);
            return curl_exec($con);
        }

        public function sendSMS($mobile){
            $timestamp = time();
            $random = rand(1010,9899);
            $url = "https://yun.tim.qq.com/v5/tlssmssvr/sendsms?sdkappid=$this->appid&random=$random"; #自行更改
            $sig = hash("sha256","appkey=$this->appkey&random=$random&time=$timestamp&mobile=$mobile");
            $data = '{
                "ext"    : "",
                "extend" : "",
                "params" : [ "", "'.$random.'", "1" ],
                "sig"    : "'.$sig.'",
                "sign"   : "意翎科技",
                "tel"    : { "mobile": "'.$mobile.'", "nationcode": "86" },
                "time"   : '.$timestamp.',
                "tpl_id" : '.$this->tampleid.'
            }';
            #$this->show($data);                                    //debug
            $rep = json_decode($this->httpPost($url, $data));
            if($rep->errmsg === "OK") return $random;
            else{ $this->show($rep); return false; }
        }

        public function show($var){
            echo "<pre>";
            print_r($var);
            echo "</pre>";
        }

        public function checkName($name){
            $sql = "select * from users where username = '$name'";
            $res = mysql_query($sql);
            if($row = mysql_fetch_assoc($res)) return false;
            else return true;
        }

        public function checkAttack($str){
            if(!preg_match("/^[a-zA-Z0-9@\.]+$/", $str)) return false;
            $str = trim($str);
            $str = stripslashes($str);
            $str = htmlspecialchars($str);
            return $str; 
        }

        public function goBack($str){
            echo "<script> alert('$str'); history.go(-1);</script>";
            exit;
        }
    }

    extract($_GET);
    extract($_POST);

    $appid = "yourappid";
    $appkey = "yourappsecret";
    $tampleid = "152488";
    $reg = new RegClass($appid, $appkey, $tampleid);

    if($send && $phone){
        $random = $reg->sendSMS($phone);
        $_SESSION["P".$phone] = array(
            "isMatched" => false,
            "checkCode" => $random,
            "timeStamp" => time()
        );
        if($random) echo $reg->status["sendOk"];
        else echo $reg->status["sendNo"];
    }

    if($recv && $phone){
        if($_SESSION["P".$phone]["checkCode"] == $recv){
            echo $reg->status["codeOk"];
            $_SESSION["P".$phone]["isMatched"] = true;
        }else
            echo $reg->status["codeNo"];
    }

    if($check && $name){
        if(!checkName($name))
            echo $reg->status["nameNo"];
    }

    if($name && $pass && $mail && $phone){
        if(!$_SESSION["P".$phone]["isMatched"])
            $reg->goBack("请先校验手机验证码");
        
        if(!$reg->checkName($name))
            $reg->goBack("用户名重复");
        

        $pass = md5($pass);
        $name = $reg->checkAttack($name);
        $mail = $reg->checkAttack($mail);
        $phone = $reg->checkAttack($phone);

        if(!$name || !$mail || !$phone)
            $reg->goBack("存在非法字符");

        $ins = "insert into users values(NULL, '$name', '$pass', '$mail', '$phone')";
        $res = mysql_query($ins);
        if(!$res)
            $reg->goBack("注册失败");
        else
            $reg->goBack("注册成功");
    }
?>
