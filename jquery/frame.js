(function(){

    var objectName = 'my';
    window[objectName] = {}

    function registerFunc(funcname, funcaddr){                          //注册函数
        if(window[funcname] !== undefined){                               //如果冲突，加入到命名空间对象objectName中
            console.log(funcname + 'conflict detective!');
            window[objectName][funcname] = funcaddr;
            console.log(window[objectName])
        }else{                                                           //否则直接注册到windows对象里
            console.log(funcname + 'join window object ok !')
            window[funcname] = funcaddr;
        }
    }

    registerFunc('write', write);
    registerFunc('print', print);
    registerFunc('replaceAll', replaceAll);
    registerFunc('baseUrlName', baseUrlName);
    registerFunc('onlyPathName', onlyPathName);
    registerFunc('getRequestData', getRequestData);


    function write(str){
        document.write(str);
    }
    function print(str){
        console.log(str);
    }
    function getRequestData(){
        var GET = new Object();
        var paramArr = location.search.substr(1).split('&');
        for(var i in paramArr){
            var arr = paramArr[i].split('=');
            GET[arr[0]] = arr[1]; 
        }
        return GET;
    }
    function baseUrlName(path){
        var name = path || location.href;
        name = name.split('/').pop().split('?')[0];
        return name;
    }
    function onlyPathName(path){
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
    
})();
