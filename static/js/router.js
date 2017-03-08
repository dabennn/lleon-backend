(function(){

	function Router() {
    this.routes = {};
    this.currentUrl = '';
	}
	//route 存储路由更新时的回调到回调数组routes中，回调函数将负责对页面的更新
	Router.prototype.route = function(path, callback) {
		if(path.search(/\?/) !== -1){
			path = path.substring(0,path.search(/\?/));
			this.routes[path] = callback || function(){};
		}else{
   		this.routes[path] = callback || function(){};//给不同的hash设置不同的回调函数
		}
	};
	//refresh 执行当前hash对应的回调函数，更新页面
	Router.prototype.refresh = function() {
		var currentHash = location.hash;
		if(location.hash.search(/\?/) !== -1){
			this.currentUrl = location.hash.substring(1,location.hash.search(/\?/));
    	this.routes[this.currentUrl](currentHash);
		}else{
    	this.currentUrl = location.hash.slice(1) || '/list';
    	this.routes[this.currentUrl]();//根据当前的hash值来调用相对应的回调函数
		}
	};

	Router.prototype.init = function() {
    window.addEventListener('load', this.refresh.bind(this), false);
    window.addEventListener('hashchange', this.refresh.bind(this), false);
	}

	window.Router = new Router();
	window.Router.init();
	
})();
