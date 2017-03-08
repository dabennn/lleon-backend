var template = (function(){
	var template = {
		list:`<div class="list-wrapper">
				<div class="article">
					<h3 style="padding-left:20px">Article</h3>
					<ul class="list arti-list"></ul>
				</div>
				<div class="note">
					<h3 style="padding-left:20px">Note</h3>
					<ul class="list note-list"></ul>
				</div>
			</div>`,
		edit:`<div class="edit-wrapper">
				<form action="" class="form">
					<div>
						标题：<input class="title" type="text" name="title">
					</div>
					<div>
						类型：<input class="type" type="radio" name="type" value="article">文章
						<input class="type" type="radio" name="type" value="note">笔记
					</div>
					<div>
						分类：<input class="category" type="text" name="category">
						<span style="position:relative"><span class="options"></span></span>
					</div>
					<div>摘要：<input class="excerpt" type="text" name="excerpt"></div>
					<div class="editor-wrapper">
						<textarea class="editor-left" name="content" id="content" cols="30" rows="10"></textarea>
						<div class="editor-right">
							<article class="article markdown-body"></article>
						</div>
					</div>
					<div class="foot">
						<button class="btn publish" data-status="1">发布</button>
						<button class="btn save" data-status="2">保存</button>
						<button class="btn cancel">取消</button>
						<span style="font-size:0.8em">当前状态：<span class="status">待发布</span></span>
					</div>
				</form>
			</div>`,
		cache:`<div class="cache-wrapper">
				<h3 style="padding-left:20px">当前缓存</h3>
				<ul class="cache-list"></ul>
			</div>`
	};

	function getTemplate(name){
		return template[name];
	}

	return getTemplate;
})();