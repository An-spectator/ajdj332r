if($('.lcms-form-tips').length>0){$('.lcms-form-tips').each(function(index){var that=$(this),tindex;that.hover(function(){tindex=layer.tips(that.attr('data-tips'),$(this),{tips:[1,'#303133'],time:0})},function(){layer.close(tindex)})})};if($('.lcms-form-switch').length>0){layui.form.on('switch(lcms-form-switch)',function(data){var elem=$(data.elem);var url=elem.attr('data-url');if(url){LJS._post(url,{checked:data.elem.checked?1:''},function(res){if(res.code=='1'){LJS._tips(res.msg);elem.attr("value",res.data?res.data:1)}else{LJS._tips(res.msg,0);elem.prop("checked",data.elem.checked?false:true);layui.form.render()};layer.closeAll('tips')},'json',true)}})};layui.form.on('submit(lcmsformsubmit)',function(lform){var formData=$(lform.form).data();$(lform.form).find('.lcms-form-input').each(function(){var ipt=$(this);lform.field[ipt.attr('name')]=ipt.val().replace(/'/g,"&#039;").replace(/"/g,"&quot;")});$(lform.form).find('.lcms-form-tags').each(function(){var ipt=$(this).children('input');lform.field[ipt.attr('name')]=ipt.attr('data-value').replace(/'/g,"&#039;").replace(/"/g,"&quot;")});$(lform.form).find('.lcms-form-checkbox').each(function(){var ipt=$(this).children('input');ipt.each(function(){lform.field[$(this).attr('name')]=lform.field[$(this).attr('name')]?1:0})});$(lform.form).find('.lcms-form-switch').each(function(){var ipt=$(this).children('input');if(lform.field[ipt.attr('name')]){if(lform.field[ipt.attr('name')]=='1'){lform.field[ipt.attr('name')]=1}}else{lform.field[ipt.attr('name')]=0}});$(lform.form).find('.lcms-form-editor').each(function(){var editor=$(this).children('div'),content;if(editor.length>0){if(typeof lcms_editor_getbody=='function'){content=lcms_editor_getbody(editor.attr('id'))}else{content=lform.field[$(this).attr('data-name')]}}else{content=$(this).children('script').html()};lform.field[$(this).attr('data-name')]=content?$.base64.encode(content.replace(new RegExp(LCMS.url.site+'upload','g'),'../upload')):''});var isFunc=function(funcName){try{if(typeof(eval(funcName))=="function"){return true}}catch(e){}return false};var formPost=function(){LJS._post(lform.form.action,lform.field,function(res){if(res.code=='1'){res.msg&&LJS._tips(res.msg);if(res.go){switch(res.go){case'close':LJS._lazydo(function(){var tables=$('table.lcms-form-table',parent.document),trees=$('table.lcms-form-table-tree',parent.document);if(tables.length>0||trees.length>0){tables.each(function(){parent.layui.table.reloadData($(this).attr('id'))});trees.each(function(){parent.layui.treeGrid.query($(this).attr('id'))});LJS._closeframe()}else{LJS._closeframe(true)}});break;case'reload':case'reload-page':LJS._lazydo(function(){window.location.reload()});break;case'reload-parent':LJS._lazydo(function(){parent.window.location.reload()});break;case'reload-top':LJS._lazydo(function(){top.window.location.reload()});break;case'goback':LJS._lazydo(function(){window.location.href=document.referrer});break;default:LJS._lazydo(function(){window.location.href=res.go});break}}}else if(res.code=='2'){res.msg&&LJS._tips(res.msg);LJS._lazydo(function(){if(res.go&&isFunc(res.go)){eval(res.go)(res.data)}},res.msg?0:1)}else{res.msg&&LJS._tips(res.msg,0)}})};if(formData.onsubmit&&isFunc(formData.onsubmit)){eval(formData.onsubmit)(lform.field,function(rtn,field){if(rtn==true){if(field){lform.field=field};formPost()}})}else{formPost()}return false});