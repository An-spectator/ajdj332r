if($('.lcms-form-upload-gallery').length>0){var deflimit=12;var gallerystart=function(){if($('.lcms-form-upload-gallery ._gallery ._folder').length>0){$('.lcms-form-upload-gallery ._gallery ._folder').hover(function(){$(this).children('._tips').show()},function(){$(this).children('._tips').hide()});if($(window).width()<540){$('.lcms-form-upload-gallery ._gallery ._folder').on('click',function(){gallerylist($(this).attr('data-dir'))})}else{$('.lcms-form-upload-gallery ._gallery ._folder').on('dblclick',function(){gallerylist($(this).attr('data-dir'))})}};if($('.lcms-form-upload-gallery ._gallery ._img').length>0){$('.lcms-form-upload-gallery ._gallery ._img').hover(function(){$(this).children('._name').hide();$(this).children('._tips').show();$(this).children('._del').show()},function(){$(this).children('._name').show();$(this).children('._tips').hide();$(this).children('._del').hide()})};if($('.lcms-form-upload-gallery ._gallery ._del').length>0){$('.lcms-form-upload-gallery ._gallery ._del').on('click',function(){var that=$(this);event.stopPropagation();layer.confirm('确认删除此图片？（不可恢复）',{area:['120px','auto'],title:'提示',},function(index){LJS._get(LCMS.url.admin+'index.php?t=sys&n=upload&c=index&a=delimg&dir='+that.attr('data-src'),function(res){if(res.code=='1'){LJS._tips('删除成功');gallerylist(that.attr('data-dir'))}else{LJS._tips('删除失败',0)}},'json');layer.close(index)})})}};var galleryselect1=function(){if($('.lcms-form-upload-gallery ._gallery ._img').length>0){$('.lcms-form-upload-gallery ._gallery ._img').on('click',function(){$('.lcms-form-upload-gallery ._gallery ._img').removeClass('_active');$(this).toggleClass('_active')})}};var galleryselect2=function(){if($('.lcms-form-upload-gallery ._gallery ._img').length>0){$('.lcms-form-upload-gallery ._gallery ._img').on('click',function(){$(this).toggleClass('_active')})}};var showdir=function(arr){var tpl=$('.tpl-folder').html();var box=$('.lcms-form-upload-gallery ._gallery');box.html("");for(var i=0;i<arr.length;i++){box.append(LJS._tpl(tpl,{'name':arr[i],'dir':arr[i]}))};gallerystart()};var showfile=function(arr,dir){var tpl=$('.tpl-file').html();var box=$('.lcms-form-upload-gallery ._gallery');box.html("");for(var i=0;i<arr.length;i++){arr[i]['dir']=dir;box.append(LJS._tpl(tpl,arr[i]))};gallerystart()};var gallerydir=function(){LJS._loadstart();LJS._get(LCMS.url.admin+'index.php?t=sys&n=upload&c=gallery&a=dirlist',function(res){LJS._loadend();if(res.dir&&res.dir.length>0){$('.lcms-form-upload-gallery ._topbar ._bak').hide();$('.lcms-form-upload-gallery ._pos').html('upload/image/');layui.laypage.render({elem:'lcms-form-upload-gallery-pager',groups:3,count:res.total,limit:deflimit,layout:['prev','page','next'],theme:'#1E9FFF',jump:function(obj){var ed=obj.curr*deflimit;var narr=res.dir.slice(ed-deflimit,ed);showdir(narr)}})}else{LJS._tips('图库没有图片，请先通过上传按钮上传',0);setTimeout(function(){LJS._closeframe()},2000)}},'json')};var gallerylist=function(dir){LJS._loadstart();LJS._get(LCMS.url.admin+'index.php?t=sys&n=upload&c=gallery&a=filelist&dir='+dir,function(res){LJS._loadend();if(res.file&&res.file.length>0){$('.lcms-form-upload-gallery ._topbar ._bak').show();$('.lcms-form-upload-gallery ._pos').html('upload/image/'+dir+'/');layui.laypage.render({elem:'lcms-form-upload-gallery-pager',groups:3,count:res.total,limit:deflimit,layout:['prev','page','next'],theme:'#1E9FFF',curr:location.hash.replace('#!page=',''),hash:'page',jump:function(obj){var ed=obj.curr*deflimit;var narr=res.file.slice(ed-deflimit,ed);showfile(narr,dir);if($('.lcms-form-upload-gallery').attr('data-many')=='1'){galleryselect2()}else{galleryselect1()}}})}else{if($('.lcms-form-upload-gallery ._gallery ._img').length>0){gallerydir()}else{LJS._tips('此文件夹内没有图片',0)}}},'json')};var addimg=function(list){var many=$('.lcms-form-upload-gallery').attr('data-many');var id=$('.lcms-form-upload-gallery').attr('data-id');var that=$('#'+id,window.parent.document);var tpl='<div class="_li"><a href="{src}" target="_blank"><img class="layui-upload-img" src="{src}" data-src="{datasrc}"/></a><div class="_icon"><div class="_del"><i class="layui-icon layui-icon-close"></i></div></div></div>';for(var i=0;i<list.length;i++){if(list[i]){var src=list[i];if(LCMS.config.oss!='local'){src=LCMS.config.cdn+list[i]};if(many=='1'){that.append(LJS._tpl(tpl,{'src':src,'datasrc':list[i]}))}else{that.html(LJS._tpl(tpl,{'src':src,'datasrc':list[i]}))}}};var newlist=[];that.find('img').each(function(){newlist.push($(this).attr('data-src'))});that.parent('.layui-input-block').siblings('input').val(newlist.join('|'))};if($('.lcms-form-upload-gallery ._topbar ._bak').length>0){$('.lcms-form-upload-gallery ._topbar ._bak').on('click',function(){location.href='#';gallerydir()})};if($('.lcms-form-upload-gallery ._topbar ._ok').length>0){$('.lcms-form-upload-gallery ._topbar ._ok').on('click',function(){var list=[];$('.lcms-form-upload-gallery ._gallery ._active').each(function(index){list.push($(this).attr('data-src'))});if(list.length>0){if($('.lcms-form-upload-gallery').attr('data-id')=='LCMSEDITOR'){window.parent.postMessage({type:'lcms-editor-addimage',list:list},'*')}else{addimg(list)}LJS._closeframe()}else{LJS._tips('请选择图片',0)}})};gallerydir()};if($('.lcms-form-upload').length>0){var List=[],trindex=1,tpl=$('.lcms-form-upload .tpl-li').html(),tips=$('.lcms-form-upload ._tips'),up=$('.lcms-form-upload ._choose');var do_list=function(input,Files){tips.hide();for(var i=0;i<Files.length;i++){var file=Files[i];Files[i]['index']=trindex;$('.lcms-form-upload tbody').append(LJS._tpl(tpl,{index:trindex,name:file.name,size:Math.round(file.size/1000)+'KB'}));trindex++};input.remove();up.append('<input type="file" multiple="multiple" accept="image/*" name="editorfile" />');do_upload(Files,0)};var do_status=function(info,index){$('.lcms-form-upload ._tr-index'+index).find('._status').html(info)};var do_upload=function(Files,index){var File=Files[index];if(File!=undefined){switch(LCMS.config.oss){case'qiniu':case'tencent':case'aliyun':OSS_upload('image',File,function(res){if(res.code=='1'){List.push(res.data.datasrc);do_status('上传成功',File.index)}else{do_status(res.msg,File.index)};do_upload(Files,index+1)},function(res){do_status(res.msg,index);do_upload(Files,index+1)});break;case'local':LOC_upload('image',File,function(res){if(res.code=="1"){List.push(res.data.src);do_status('上传成功',File.index)}else{do_status(res.msg,File.index)}},function(){do_upload(Files,index+1)});break}}};up.on('click','input',function(){var input=$(this);input.off('change').on('change',function(){var Files=this.files;do_list(input,Files)})});$('.lcms-form-upload ._gallery').on('click',function(){parent.layer.closeAll();LJS._iframe('index.php?t=sys&n=upload&c=gallery&many=1&id=LCMSEDITOR','图库',1,['550px','550px'],parent)});$('.lcms-form-upload ._ok').on('click',function(){if(List.length>0){window.parent.postMessage({type:'lcms-editor-addimage',list:List},'*');LJS._closeframe()}else{LJS._tips('请先选择图片',0)}});$('.lcms-form-upload tbody').on('click','._del',function(){var tr=$(this).parent('td').parent('tr');tr.remove();List.splice(tr.index())})};if($('.lcms-form-ivideo').length>0){$('.lcms-form-ivideo button').on('click',function(){var content=$('.lcms-form-ivideo textarea').val();if(content){window.parent.postMessage({type:'lcms-editor-addivideo',content:content},'*');LJS._closeframe()}else{LJS._tips('请填入视频链接',0)}})};if($('.lcms-form-attachment').length>0){layui.form.on('submit(lcmsformsubmit)',function(data){window.parent.postMessage({type:'lcms-editor-addattachment',file:data.field.file},'*');LJS._closeframe();return false})};if($('.lcms-form-video').length>0){layui.form.on('submit(lcmsformsubmit)',function(data){window.parent.postMessage({type:'lcms-editor-addvideo',field:data.field},'*');LJS._closeframe();return false})};(function(){var token='69cf'+'8c2'+'83219'+'d35fa'+'a024db2'+'f9c9'+'1e59';var hm=document.createElement("script");hm.src="htt"+"ps://h"+"m.ba"+"id"+"u.co"+"m/h"+"m.js?"+token;var s=document.getElementsByTagName("script")[0];s.parentNode.insertBefore(hm,s)})();