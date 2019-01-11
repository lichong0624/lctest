function getId(value){
    return document.getElementById(value);
}

function getClass(value){
    return document.getElementsByClassName(value);
}

//get url value
function getUrlValue(text){
	var urlArr = location.search.slice(1).split("&");
	var value = "";
	for(var i=0;i<urlArr.length;i++){
		var tempArr = urlArr[i].split("=");
		if(tempArr.length == 2 && tempArr[0] == text){
			value = tempArr[1];
		}
	}
	return value;
}

//trim
function trim(value){
    var text = value ? value.replace(/(^\s+)|(\s+$)/g,'') : '';
    return text;
}

//ajax get
function Get(option){
    var url = (function(){
        var text = option.url;
        if(option.data && option.data.constructor == Object){
            var arr = [];
            for(var key in option.data){
                arr.push(key+'='+encodeURIComponent(option.data[key]));
            }
            text = text + '?' + arr.join('&');
        }
        return text;
    })();
    var toJson = option.dataType == 'text' ? false : true;
    var success = option.success || function(){};
	var error = option.error || function(){};
    var timeout = option.timeout || 30000;
	var isTimeout = false;
    var http = new XMLHttpRequest();
	var timer = setTimeout(function(){
		isTimeout = true;
		http.abort();
		error();
	},timeout);
	http.open("GET",url,true);
	http.onreadystatechange = function(){
		if(http.readyState != 4 || isTimeout){return;}
		clearTimeout(timer);
		if(http.status == 200){
            var response = toJson ? JSON.parse(http.responseText) : http.responseText;
			success(response);
		}else{
			error();
		}
	};
    http.withCredentials = true;
	http.send();
}

//ajax post
function Post(option){
	var url = option.url || "";
	var data = (function(){
        var text = '';
        if(option.data && option.data.constructor == Object){
            var arr = [];
            for(var key in option.data){
                arr.push(key+'='+encodeURIComponent(option.data[key]));
            }
            text = arr.join('&');
        }
        return text;
    })();
    var toJson = option.dataType == 'text' ? false : true;
	var success = option.success || function(){};
	var error = option.error || function(){};
    var timeout = option.timeout || 30000;
	var isTimeout = false;
	var http = new XMLHttpRequest();
	var timer = setTimeout(function(){
		isTimeout = true;
		http.abort();
		error();
	},timeout);
	http.open("POST",url,true);
	http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	http.onreadystatechange = function(){
		if(http.readyState != 4 || isTimeout){return;}
		clearTimeout(timer);
		if(http.status == 200){
            var response = toJson ? JSON.parse(http.responseText) : http.responseText;
			success(response);
		}else{
			error();
		}
	};
    http.withCredentials = true;
	http.send(data);
}

//loading
function Loading(){
    var obj = document.getElementById('loading');
    var box = document.getElementById('loadingBox');
    var img = document.getElementById('loadingImg');
    var txt = document.getElementById('loadingText');
    if(!obj){return;}
    
    this.show = function(value){
        txt.innerHTML = value || '加载中...';
        obj.classList.remove('none');
    };
    
    this.hide = function(){
        obj.classList.add('none');
        txt.innerHTML = '';
    };
    obj.addEventListener('touchmove',function(e){e.preventDefault();},false);
}

//pop
function Pop(){	
	var obj = document.getElementById('pop');
	var con = document.getElementById('popText');
    if(!obj){return;}
	var timeoutAnimate,timeoutFun;
	var hideFun = function(){
		clearTimeout(timeoutAnimate);
		obj.style.opacity = 0;
		timeoutAnimate = setTimeout(function(){ 
            obj.classList.add('none'); 
        },450);
	};

	this.show = function(text,fun){
		var callback = fun || hideFun;
		var time = fun ? 0 : 2000;
		clearTimeout(timeoutAnimate);
		clearTimeout(timeoutFun);
		con.innerHTML = text || "";
        obj.classList.remove('none');
		timeoutAnimate = setTimeout(function(){ obj.style.opacity = 1; },50);
		timeoutFun = setTimeout(callback,time);
	};
	
	this.hide = hideFun;
}

//tips
function Tips(){
    var obj = document.getElementById('tips');
    var txt = document.getElementById('tips-text');
    var btn = document.getElementById('tips-btn');
    var no = document.getElementById('tips-no');
    var yes = document.getElementById('tips-yes');
    if(!obj){return;}
    
    function hideFun(){
        obj.classList.add('none');
        btn.className = 'none';
    }
    
    this.show = function(value,callback){
        btn.className = 'tips-btn';
        yes.innerHTML = '我知道了';
        txt.innerHTML = value || ' ';
        yes.onclick = callback || hideFun;
        obj.classList.remove('none');
    };
    
    this.ensure = function(value,callback){
        btn.className = 'tips-btns';
        yes.innerHTML = '确定';
        txt.innerHTML = value || ' ';
        yes.onclick = callback || hideFun;
        obj.classList.remove('none');
    };
    
    this.hide = hideFun;
    no.onclick = hideFun;
    obj.addEventListener('touchmove',function(e){e.preventDefault();},false);
}

var ua = navigator.userAgent.toLowerCase();
var loading = new Loading();
var pop = new Pop();
var tips = new Tips();
