//@version: test
//@for: jquery
function print(str){ console.log(str); }                                //print默认弹出打印机程序，强制覆盖

(function(){
    var objectName = 'my';
    window[objectName] = {}

    function registerFunc(funcname, funcaddr){                          //注册函数
        if(window[funcname] !== undefined){                             //如果冲突，加入到命名空间对象objectName中
            console.log(funcname + ' conflict detective!');
            window[objectName][funcname] = funcaddr;
        }else{                                                           //否则直接注册到windows对象里
            console.log(funcname + ' join window object ok !')
            window[funcname] = funcaddr;
        }
    }

    registerFunc('write', write);
    registerFunc('urlBaseName', urlBaseName);
    registerFunc('urlPathName', urlPathName);
    registerFunc('getCookie', getCookie);
    registerFunc('setCookie', setCookie);
    registerFunc('delCookie', delCookie);
    registerFunc('getRequest', getRequest);
    registerFunc('replaceAll', replaceAll);
    registerFunc('toUrlParams', toUrlParams);
    registerFunc('ajaxGet', ajaxGet);
    registerFunc('ajaxPost', ajaxPost);
    registerFunc('getStyle', getStyle);
    registerFunc('isArray', isArray);
    registerFunc('isEmptyObject', isEmptyObject);

    function write(str){
        document.write(str);
    }
    function getRequest(key){
        var GET = new Object();
        var paramArr = location.search.substr(1).split('&');
        for(var i in paramArr){
            var arr = paramArr[i].split('=');
            GET[arr[0]] = arr[1]; 
        }
        if(key) return GET[key];
        return GET;
    }
    function getCookie(key){
        var cookieObj = new Object();
        var cookieArr = document.cookie.split('; ');
        for(var i in cookieArr){
            var cookiePart = cookieArr[i].split('=');
            cookieObj[cookiePart[0]] = cookiePart[1];
        }
        if(key) return cookieObj[key];
        return cookieObj;
    }
    function delCookie(key){
        var exp = new Date();
        exp.setTime(exp.getTime() - 1);
        var cval=getCookie(key);
        if(cval!=null)
            document.cookie= key + "=" + cval + ";expires=" + exp.toGMTString();
    }
    function setCookie(key,value,expiredays){
        var exdate=new Date()
        exdate.setDate(exdate.getDate()+expiredays)
        document.cookie=key+ "=" +escape(value)+
        ((expiredays==null) ? "" : ";expires="+exdate.toGMTString())
    }
    function urlBaseName(path){
        var name = path || location.href;
        name = name.split('/').pop().split('?')[0];
        return name;
    }
    function urlPathName(path){
        var arr = path || location.href;
        arr = arr.split('/');
        arr.pop();
        return arr.join('/')+'/';
    }
    function replaceAll(str, oldStr, newStr){
        var arr = str.split(oldStr);
        var str = arr.join(newStr);
        return str;
    }
    function ajaxGet(url, paramObj, callBackFunc){
        var xmlhttp;
        var paramStr = toUrlParams(paramObj);
        url = url + "?" + paramStr;
        if(window.XMLHttpRequest) xmlhttp = new XMLHttpRequest();
        else xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
        xmlhttp.onreadystatechange = function(){
            if(xmlhttp.readyState == 4 && xmlhttp.status == 200)
                callBackFunc(xmlhttp.responseText);
        }
        xmlhttp.open("GET", url, true);
        xmlhttp.send();
    }
    function ajaxPost(url, paramObj, callBackFunc){
        var xmlhttp;
        var paramStr = toUrlParams(paramObj);
        if (window.XMLHttpRequest) xmlhttp = new XMLHttpRequest();
        else xmlhttp = new ActiveXObject("Microsoft.XMLHTTP"); 
        xmlhttp.onreadystatechange=function(){
            if(xmlhttp.readyState==4 && xmlhttp.status==200)
                callBackFunc(xmlhttp.responseText);
        }
        xmlhttp.open("POST", url, true);
        xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
        xmlhttp.send(paramStr);
    }
    function toUrlParams(obj){
        var arr = [];
        for(var i in obj)
            arr.push(i + "=" + obj[i]);
        var params = arr.join("&");
        return params;
    }
    function getStyle(elem, attr){
        if(elem.currentStyle)
            return elem.currentStyle[attr];
        else
            return window.getComputedStyle(elem,null)[attr];
    }
    function isArray(obj){
        if(obj instanceof Array) return true;
        return false;
    }
    function isEmptyObject(obj){
        var i;
        for(i in obj);
        if(i === undefined )return true;
        return false;
    }
})();
