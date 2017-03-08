<?php
session_start();

if(!isset($_SESSION['username'])){
	header('Location:login.html');
	exit('请先登录');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Menu</title>
	<link rel="stylesheet" href="./static/css/list.css">
	<link rel="stylesheet" href="./static/css/edit.css">
	<link rel="stylesheet" href="./static/css/cache.css">
	<link rel="stylesheet" href="./static/css/github-markdown.css">
	<link rel="stylesheet" href="./static/styles/atom-one-light.css">
	<style>
		html,body{
			height: 100%;
			margin: 0;
			padding: 0;	
		}
		a{
			color: #000;
		}
		.app{
			display: flex;
			height: 100%;
		}
		.menu-left{
			flex: 0 1 200px;
			background: rgb(250,250,250);
		}
		h3.menu-title{
			text-align: center;
			margin: 0;
			padding: 18px 0;
			border-bottom: 1px solid rgb(238,238,238);
		}
		.menu-left ul{
			list-style: none;
			margin: 0;
			padding: 0;
		}
		.menu-item{
			font-size: 18px;
		}
		.menu-item a {
	    display: block;
	    padding: 10px 0 10px 30px;
	    text-decoration: none;
		}
		.menu-item a:hover{
			background: rgb(238,238,238);
			color: #fff;
		}
		.active{
			background: rgb(238,238,238);
			color: #fff;
		}
		.menu-right{
			height: 100%;
			flex: 1;
		}
	</style>
</head>
<body>
	<div class="app">
		<div class="menu-left">
			<h3 class="menu-title">用户菜单</h3>
			<ul>
				<li class="menu-item">
					<a class="menu-link" href="#/list">文章列表</a>
				</li>
				<li class="menu-item">
					<a class="menu-link" href="#/edit">创建文章</a>
				</li>
				<li class="menu-item">
					<a class="menu-link" href="#/cache">缓存操作</a>
				</li>
				<li class="menu-item">
					<a class="menu-link" href="request/login.php?log=out">退出</a>
				</li>
			</ul>
		</div>
		<div class="menu-right">
		</div>
	</div>
	<script src="./static/js/router.js"></script>
	<script src="./static/js/template.js"></script>
	<script src="./static/js/create.js"></script>
	<script src="./static/js/marked.js"></script>
	<script src="./static/js/highlight.pack.js"></script>
	<script>
		var oRight = document.querySelector('.menu-right');
    var xhr = new XMLHttpRequest();

    var routeHash = '';

    //开始路由配置
		Router.route('/list', function() {
			menuHighlight();
  		oRight.innerHTML = template('list');

			var url = 'http://localhost/backend/request/list.php?page=list';
	    xhr.open('GET',url,true);
	    xhr.send();

	    xhr.onreadystatechange = function(){
	    	if(xhr.readyState === 4 && xhr.status === 200){
	    		var res = JSON.parse(xhr.responseText);
	    		if(res.code !== 200){
	    			alert(res.message);
	    		}
	    		//渲染页面
	    		var aArti = res.data.article,
	    				aNote = res.data.note;

	    		var oArtiList = document.querySelector('.arti-list');
	    		var oNoteList = document.querySelector('.note-list');

	    		for(var i=0;i<aArti.length;i++){
						oArtiList.innerHTML += createArtList(aArti[i]);
					}
	    		for(var j=0;j<aNote.length;j++){
						oNoteList.innerHTML += createArtList(aNote[j]);
					}
	    		//渲染完成

	    		var aLink = document.querySelectorAll('.link');
	    		for(var k=0;k<aLink.length;k++){
		    		aLink[k].onclick = function(e){
		    			routeHash = '?id='+this.dataset.id;
		    		}
	    		}

	    		var aBtn = document.querySelectorAll('.list-btn');
	    		for(var i=0;i<aBtn.length;i++){
	    			aBtn[i].onclick = function(){
	    				var self = this;
	    			
    					xhr.open('GET',makeUrl(self.dataset.id,self.innerText),true);
    					xhr.send();

    					xhr.onreadystatechange = function(){
    						if(xhr.readyState === 4 && xhr.status == 200 && xhr.responseText === 'success'){
    							Router.refresh();//重载页面，刷新样式
    						}
    					}
	    			}
	    		}
	    	}
	    }
		});

		Router.route('/edit', function(param) {
			menuHighlight();
			oRight.innerHTML = template('edit');
			var oContent = document.querySelector('#content');
			var oArticle = document.querySelector('.article');
			var handle = 'insert';
			var text_id = '';
			marked.setOptions({
		  	renderer: new marked.Renderer(),
			  gfm: true,
			  tables: true,
			  breaks: false,
			  pedantic: false,
			  sanitize: false,
			  smartLists: true,
			  smartypants: false,
			  highlight: function (code) {
			    return hljs.highlightAuto(code).value;
			  }
			});

			oContent.onkeyup = function(){
				oArticle.innerHTML = marked(oContent.value);
			}

			oContent.onkeydown = function(e){
	      if (e.keyCode == 9) {
	          e.preventDefault();
	          var indent = '  ';
	          var start = this.selectionStart;
	          var end = this.selectionEnd;
	          var selected = window.getSelection().toString();
	          selected = indent + selected.replace(/\n/g, '\n' + indent);
	          this.value = this.value.substring(0, start) + selected
	                  + this.value.substring(end);
	          this.setSelectionRange(start + indent.length, start
	                  + selected.length);
	      }
			}

			var aType = document.querySelectorAll('.type');
			aType[0].onclick = getOptions;
			aType[1].onclick = getOptions;//获取分类数据

			var oCategory = document.querySelector('.category');
			oCategory.onfocus = function(){
				document.querySelector('.options').style.display = 'inline-block';
			}
			oCategory.onblur = function(){
				document.querySelector('.options').style.display = 'none';
			}

			var oPublish = document.querySelector('.publish');
			var oSave = document.querySelector('.save');
			var oCancel = document.querySelector('.cancel');

			if(param){
				handle = 'update';
				text_id = param.substring(param.search(/\=/)+1);
				var url = 'http://localhost/backend/request/fetchtext.php?id='+text_id;
				xhr.open('GET',url,true);
				xhr.send();

				xhr.onreadystatechange = function(){
					if(xhr.readyState === 4 && xhr.status === 200){
						var res = JSON.parse(xhr.responseText);
						var oTitle = document.querySelector('.title'),
								oCategory = document.querySelector('.category'),
								oStatus = document.querySelector('.status');
						if(res.code === 200){
							res = res.data;

							oTitle.value = res.title;
							oCategory.value = res.category;
							oContent.value = res.content;
							oStatus.innerText = res.status;

							if(res.type === 'article'){
								aType[0].checked = true;
								var oExcerpt = document.querySelector('.excerpt');
								oExcerpt.value = res.excerpt;
							}else{
								aType[1].checked = true;
							}
						}
					}
				}
			}

			oPublish.onclick = save.bind(oPublish,handle,text_id);
			oSave.onclick = save.bind(oSave,handle,text_id);
			oCancel.onclick = function(e){
				e.preventDefault();
				window.location.hash = '#/list';
			}

		});

		Router.route('/cache', function() {
			menuHighlight();
			oRight.innerHTML = template('cache');

			var url = 'http://localhost/backend/request/cachelist.php?cache=now';
			xhr.open('GET',url,true);
			xhr.send();

			xhr.onreadystatechange = function(){
				if(xhr.readyState === 4 && xhr.status === 200){
					var res = JSON.parse(xhr.responseText);
					if(res.code !== 200){
						alert(res.message);
					}

					var aData = res.data;
					var oCacheList = document.querySelector('.cache-list');

					for(var i=0;i<aData.length;i++){
						oCacheList.innerHTML += createCacheList(aData[i]);
					}//渲染完成

					var aBtn = document.querySelectorAll('.cache-btn');
					for(var j=0;j<aBtn.length;j++){
						aBtn[j].onclick = function(){
							var self = this;
							url = 'http://localhost/backend/request/cachelist.php?cache=del&filename='+self.dataset.filename;
							xhr.open('GET',url,true);
							xhr.send();

							xhr.onreadystatechange = function(){
								if(xhr.readyState === 4 && xhr.status === 200){
									Router.refresh();
								}
							}
						}
					}
				}
			}
		});

		function menuHighlight(){
			var aMenuLink = document.querySelectorAll('.menu-link');
			var href = '';
			for(var i=0;i<aMenuLink.length;i++){
				href = aMenuLink[i].href.split('#')[1];
				if(location.hash.indexOf(href) !== -1){
					for(var j=0;j<aMenuLink.length;j++){
						aMenuLink[j].classList = ['menu-link'];
					}
					aMenuLink[i].classList.add('active');
				}
			}
		}

		function makeUrl(id,text){
			var url = 'http://localhost/backend/request/list.php';
			if(text === '删除'){
				url += '?status=0&id=';
				url += id;
			}else if(text === '还原'){
				url += '?status=1&id=';
				url += id;
			}
			return url;
		}

		function getOptions(){
			var self = this;
			var oExcerpt = document.querySelector('.excerpt');
			if(self.value === 'article'){
				oExcerpt.disabled = false;
			}else if(self.value === 'note'){
				oExcerpt.disabled = true;
			}
			var url = 'http://localhost/backend/request/edit.php?type='+self.value;
			xhr.open('GET',url,true);
			xhr.send();

			xhr.onreadystatechange = function(){
				if(xhr.readyState === 4 && xhr.status === 200){
					var res = JSON.parse(xhr.responseText).data;

					var oOptions = document.querySelector('.options');

					if(oOptions.innerHTML){
						oOptions.innerHTML = '';
					}

					for(var i=0;i<res.length;i++){
						var option = '<div>'+res[i]+'</div>';
						oOptions.innerHTML += option;
					}
				}
			}
		}

		function save(handle,text_id,e){
			e.preventDefault();
			var self = this;
			var form = document.querySelector('.form');
			var url = 'http://localhost/backend/request/edit.php';
			var formData = new FormData(form);
			formData.append('status',self.dataset.status);
			formData.append('handle',handle);

			if(text_id){
				formData.append('id',text_id);
			}

			xhr.open('POST',url,true);
			xhr.send(formData);

			xhr.onreadystatechange = function(){
				if(xhr.readyState === 4 && xhr.status === 200){
					alert(xhr.responseText);
					window.location.hash = '#/list';
				}
			}
		}

	</script>
</body>
</html>