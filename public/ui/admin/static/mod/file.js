$(".lcms-form-upload-file-btn ._up").each(function(index){top.LCMSUIINDEX++;var url,up=$(this),id="LCMSUPLOADFILEBTN"+top.LCMSUIINDEX,mime=$(this).attr("data-mime"),accept=$(this).attr("data-accept"),local=$(this).attr("data-local"),ipt=$(this).parent(".lcms-form-upload-file-btn").siblings("input");var tpl='<input type="file" accept="'+(accept?accept:mime=="file"?"*":mime+"/*")+'" local="'+local+'" name="editorfile" style="position:absolute;left:0;top:0;width:100%;height:100%;opacity:0;cursor:pointer;font-size:0;">';up.attr("id",id);up.append(tpl);up.on("click","input",function(){var input=$(this);input.off("change").on("change",function(){up.children("._loading").css("display","inline-block");var File=this.files[0],local=$(input).attr("local");var Type=local=="1"?"local":LCMS.config.oss;switch(Type){case"qiniu":case"tencent":case"aliyun":OSS_upload("file",File,function(res){if(res.code=="1"){ipt.val(res.data.datasrc);LJS._tips(res.msg)}else{LJS._tips(res.msg,0)}up.children("._loading").hide();input.remove();up.append(tpl)},function(res){up.children("._loading").hide();input.remove();up.append(tpl)});break;case"local":LOC_upload("file",File,function(res){if(res.code=="1"){ipt.val(res.data.src);LJS._tips(res.msg)}else{LJS._tips(res.msg,0)}},function(){up.children("._loading").hide();input.remove();up.append(tpl)},local>0?1:0);break}})})});