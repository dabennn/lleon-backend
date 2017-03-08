function createArtList(obj){
	var title = obj.title,
			date = obj.date,
			status = obj.status,
			id = obj.text_id;
	var btnValue = status === '0' ? '还原' : '删除';
	var btnClass = status === '0' ? 'btn-resto' : 'btn-del';

	if(status === '0'){
		status = '已删除';
	}else if(status === '1'){
		status = '已发布';
	}else if(status === '2'){
		status = '待发布';
	}else{
		status = '状态出错';
	}

	var liTemplate = `<li>
							<a href="#/edit?id=`+id+`" class="link" data-id="`+id+`">`+title+`</a>
							<span class="date">`+date+`</span>
							<span class="status">`+status+`</span>
							<button class="btn `+btnClass+` list-btn" data-id="`+id+`">`+btnValue+`</button>
						</li>`;

	return liTemplate;
}

function createCacheList(obj){
	var filename = obj.filename,
			description = obj.description,
			time = obj.time;

	var liTemplate = `<li>
						<span class="filename">`+filename+`</span>
						<span class="description">`+description+`</span>
						<span class="cachetime">`+time+`</span>
						<button class="btn btn-del cache-btn" data-filename="`+filename+`">删除</button>
					</li>`;
					
	return liTemplate;
}