var newvalue=function(arr={}){var list=[];arr.each(function(){list.push($(this).attr('data-src'))});return list.join('|')};$('.lcms-form-upload-img-list').on('click','div._del',function(){var _li=$(this).parent('._icon').parent('._li');var box=_li.parent('.lcms-form-upload-img-list');_li.remove();box.parent('.layui-input-block').siblings('input').val(newvalue(box.find('img')))});$('.lcms-form-upload-img').each(function(index){top.LCMSUIINDEX++;var that=$(this);var list=that.children('input').val().split('|');var tpl='<div class="_li"><a href="{src}" target="_blank"><img class="layui-upload-img" src="{src}" data-src="{datasrc}"/></a><div class="_icon"><div class="_del"><i class="layui-icon layui-icon-close"></i></div></div></div>';for(var i=0;i<list.length;i++){if(list[i]){var box=that.children('.layui-input-block').children('.lcms-form-upload-img-list');var src=list[i];if(LCMS.config.oss!='local'&&src.indexOf('://')==-1&&src.indexOf('../upload/')!=-1){src=LCMS.config.cdn+src.replace('../','')};box.append(LJS._tpl(tpl,{'src':src,'datasrc':list[i]}))}};var id='LCMSUPLOADSORTABLE-'+top.LCMSUIINDEX;that.children('.layui-input-block').children('.lcms-form-upload-img-list').attr('id',id);that.children('.layui-input-block').children('.lcms-form-upload-btn').attr('data-id',id);Sortable.create(document.getElementById(id),{filter:'._del',onUpdate:function(evt){var id=evt.srcElement.id;$('#'+id).parent('.layui-input-block').siblings('input').val(newvalue($('#'+id).find('img')))}})});$('.lcms-form-upload-btn ._box').on('click',function(){var many=$(this).attr('data-many');var id=$(this).parent('.lcms-form-upload-btn').attr('data-id');if($(window).width()<540){var area=['100%','100%']}else{var area=['550px','550px']};layer.open({type:2,title:'图库',area:area,resize:false,scrollbar:false,content:'index.php?n=upload&c=gallery&many='+many+'&id='+id})});var addimg=function(list){var many=$('.lcms-form-upload-gallery').attr('data-many');var id=$('.lcms-form-upload-gallery').attr('data-id');var that=$('#'+id)};$('.lcms-form-upload-btn ._up').on('click',function(){layer.close(layer.index)});$('.lcms-form-upload-btn ._up').each(function(index){top.LCMSUIINDEX++;var up=$(this),id='LCMSUPLOADBTN'+top.LCMSUIINDEX,galleryid=$(this).parent('.lcms-form-upload-btn').attr('data-id'),many=$(this).attr('data-many'),local=$(this).attr('data-local'),accept=$(this).attr('data-accept'),that=$('#'+galleryid);var multiple=many=='1'?' multiple="multiple"':'';var tpl='<input type="file"'+multiple+' accept="'+(accept?accept:"image/*")+'" local="'+local+'" name="editorfile" style="position:absolute;left:0;top:0;width:100%;height:100%;opacity:0;cursor:pointer;font-size:0;">';up.attr('id',id);up.append(tpl);var do_upload=function(input,Files,index){var File=Files[index],local=input.attr('local'),many=input.attr('multiple');var Type=local=='1'?'local':LCMS.config.oss;if(File==undefined){return};up.children('._loading').css('display','inline-block');var tpl_img='<div class="_li"><a href="{src}" target="_blank"><img class="layui-upload-img" src="{src}" data-src="{datasrc}"/></a><div class="_icon"><div class="_del"><i class="layui-icon layui-icon-close"></i></div></div></div>';switch(Type){case'qiniu':case'tencent':case'aliyun':OSS_upload('image',File,function(res){if(many=='multiple'){that.append(LJS._tpl(tpl_img,{'src':res.data.src,'datasrc':res.data.datasrc}))}else{that.html(LJS._tpl(tpl_img,{'src':res.data.src,'datasrc':res.data.datasrc}))};that.parent('.layui-input-block').siblings('input').val(newvalue(that.find('img')));LJS._tips(res.msg);up.children('._loading').hide();input.remove();up.append(tpl);do_upload(input,Files,index+1)},function(res){up.children('._loading').hide();input.remove();up.append(tpl);do_upload(input,Files,index+1)});break;case'local':LOC_upload('image',File,function(res){if(res.code=="1"){if(many=='multiple'){that.append(LJS._tpl(tpl_img,{'src':res.data.src,'datasrc':res.data.src}))}else{that.html(LJS._tpl(tpl_img,{'src':res.data.src,'datasrc':res.data.src}))};that.parent('.layui-input-block').siblings('input').val(newvalue(that.find('img')));LJS._tips(res.msg)}else{LJS._tips(res.msg,0)}},function(){up.children('._loading').hide();input.remove();up.append(tpl);do_upload(input,Files,index+1)});break}};up.on('click','input',function(){var input=$(this);input.off('change').on('change',function(){var Files=this.files;do_upload(input,Files,0)})});up.siblings('._other').on('click',function(){layer.prompt({title:'请输入第三方图片链接',formType:0},function(src,index){layer.close(index);if(src&&src.indexOf('://')!=-1){var tpl='<div class="_li"><a href="{src}" target="_blank"><img class="layui-upload-img" src="{src}" data-src="{src}"/></a><div class="_icon"><div class="_del"><i class="layui-icon layui-icon-close"></i></div></div></div>';if(many=='1'){that.append(LJS._tpl(tpl,{'src':src}))}else{that.html(LJS._tpl(tpl,{'src':src}))};that.parent('.layui-input-block').siblings('input').val(newvalue(that.find('img')))}else{LJS._tips('输入的图片链接错误',0)}})})});