var newvalue=function(arr={}){var list=[];return arr.each((function(){list.push($(this).attr("data-src"))})),list.join("|")},getWebpByImageFile=function(imageFile,callback){if(LCMS.config.attwebp>0&&(-1!==imageFile.type.indexOf("image/jpeg")||-1!==imageFile.type.indexOf("image/png"))){var base64ToFile=function(base64,fileName){for(var arr=base64.split(","),type=arr[0].match(/:(.*?);/)[1],bstr=atob(arr[1]),n=bstr.length,u8arr=new Uint8Array(n);n--;)u8arr[n]=bstr.charCodeAt(n);return new File([u8arr],fileName,{type:type})},imageFileReader=new FileReader;imageFileReader.readAsDataURL(imageFile),imageFileReader.onload=function(e){var image=new Image;image.src=e.target.result,image.onload=function(){var canvas=document.createElement("canvas");canvas.width=image.width,canvas.height=image.height,canvas.getContext("2d").drawImage(image,0,0),callback(base64ToFile(canvas.toDataURL("image/webp"),imageFile.name.split(".")[0]+".webp"))}}}else callback(imageFile)};$(".lcms-form-upload-img-list").on("click","div._del",(function(){var _li=$(this).parent("._icon").parent("._li"),box=_li.parent(".lcms-form-upload-img-list");_li.remove(),box.parent(".layui-input-block").siblings("input").val(newvalue(box.find("img")))})),$(".lcms-form-upload-img").each((function(index){top.LCMSUIINDEX++;for(var that=$(this),list=that.children("input").val().split("|"),tpl='<div class="_li"><a href="{src}" target="_blank"><img class="layui-upload-img" src="{src}" data-src="{datasrc}"/></a><div class="_icon"><div class="_del"><i class="layui-icon layui-icon-close"></i></div></div></div>',i=0;i<list.length;i++)if(list[i]){var box=that.children(".layui-input-block").children(".lcms-form-upload-img-list"),src=list[i];"local"!=LCMS.config.oss&&-1==src.indexOf("://")&&-1!=src.indexOf("../upload/")&&(src=LCMS.config.cdn+src.replace("../","")),box.append(LJS._tpl(tpl,{src:src,datasrc:list[i]}))}var id="LCMSUPLOADSORTABLE-"+top.LCMSUIINDEX;that.children(".layui-input-block").children(".lcms-form-upload-img-list").attr("id",id),that.children(".layui-input-block").children(".lcms-form-upload-btn").attr("data-id",id),Sortable.create(document.getElementById(id),{filter:"._del",onUpdate:function(evt){var id=evt.srcElement.id;$("#"+id).parent(".layui-input-block").siblings("input").val(newvalue($("#"+id).find("img")))}})})),$(".lcms-form-upload-btn ._box").on("click",(function(){var many=$(this).attr("data-many"),id=$(this).parent(".lcms-form-upload-btn").attr("data-id");LJS._iframe("index.php?n=upload&c=gallery&many="+many+"&id="+id,"图库",1,["550px","550px"])})),$(".lcms-form-upload-btn ._up").on("click",(function(){layer.close(layer.index)})),$(".lcms-form-upload-btn ._up").each((function(index){top.LCMSUIINDEX++;var up=$(this),id="LCMSUPLOADBTN"+top.LCMSUIINDEX,galleryid=$(this).parent(".lcms-form-upload-btn").attr("data-id"),many=$(this).attr("data-many"),local=$(this).attr("data-local"),accept=$(this).attr("data-accept"),that=$("#"+galleryid),multiple,tpl='<input type="file"'+("1"==many?' multiple="multiple"':"")+' accept="'+(accept||"image/*")+'" local="'+local+'" name="editorfile" style="position:absolute;left:0;top:0;width:100%;height:100%;opacity:0;cursor:pointer;font-size:0;">';up.attr("id",id),up.append(tpl);var do_upload=function(input,Files,index){var File=Files[index],local=input.attr("local"),many=input.attr("multiple"),Type="1"==local?"local":LCMS.config.oss;if(null!=File){up.children("._loading").css("display","inline-block");var tpl_img='<div class="_li"><a href="{src}" target="_blank"><img class="layui-upload-img" src="{src}" data-src="{datasrc}"/></a><div class="_icon"><div class="_del"><i class="layui-icon layui-icon-close"></i></div></div></div>';getWebpByImageFile(File,(function(File){switch(Type){case"qiniu":case"tencent":case"aliyun":OSS_upload("image",File,(function(res){"multiple"==many?that.append(LJS._tpl(tpl_img,{src:res.data.src,datasrc:res.data.datasrc})):that.html(LJS._tpl(tpl_img,{src:res.data.src,datasrc:res.data.datasrc})),that.parent(".layui-input-block").siblings("input").val(newvalue(that.find("img"))),LJS._tips(res.msg),up.children("._loading").hide(),input.remove(),up.append(tpl),do_upload(input,Files,index+1)}),(function(res){up.children("._loading").hide(),input.remove(),up.append(tpl),do_upload(input,Files,index+1)}));break;case"local":LOC_upload("image",File,(function(res){"1"==res.code?("multiple"==many?that.append(LJS._tpl(tpl_img,{src:res.data.src,datasrc:res.data.src})):that.html(LJS._tpl(tpl_img,{src:res.data.src,datasrc:res.data.src})),that.parent(".layui-input-block").siblings("input").val(newvalue(that.find("img"))),LJS._tips(res.msg)):LJS._tips(res.msg,0)}),(function(){up.children("._loading").hide(),input.remove(),up.append(tpl),do_upload(input,Files,index+1)}))}}))}};up.on("click","input",(function(){var input=$(this);input.off("change").on("change",(function(){var Files=this.files;do_upload(input,Files,0)}))})),up.siblings("._other").on("click",(function(){var input=$(this).siblings("._up").children('input'),tpl='<div class="_li"><a href="{src}" target="_blank"><img class="layui-upload-img" src="{src}" data-src="{src}"/></a><div class="_icon"><div class="_del"><i class="layui-icon layui-icon-close"></i></div></div></div>',pindex=layer.prompt({title:"支持以下方式粘贴图片",formType:2},(function(src){layer.close(pindex),src&&-1!=src.indexOf("://")?LJS._post(LCMS.url.own+"n=upload&c=index&a=editor",{files:[src]},(function(res){res&&res.list?"SUCCESS"==res.list[0].state?("1"==many?that.append(LJS._tpl(tpl,{src:res.list[0].url})):that.html(LJS._tpl(tpl,{src:res.list[0].url})),that.parent(".layui-input-block").siblings("input").val(newvalue(that.find("img")))):res.list[0].msg?LJS._tips(res.list[0].msg,0):LJS._tips("图片上传失败",0):LJS._tips("图片上传失败",0)}),"json"):LJS._tips("图片链接错误",0)})),textArea=$("#layui-layer"+pindex).find("textarea");textArea.attr("placeholder","1、输入图片链接地址\n2、粘贴网页里复制的一张图\n3、粘贴QQ、微信等软件截图或一张聊天图\n4、粘贴电脑文件夹里复制的多张图\n5、粘贴WORD文档里复制的一张图"),textArea.bind("paste",(function(e){if(e.originalEvent.clipboardData&&e.originalEvent.clipboardData.items){for(var i=0;i<e.originalEvent.clipboardData.items.length;i++){var item=e.originalEvent.clipboardData.items[i];if("file"==item.kind&&-1!==item.type.indexOf("image/")){var File=item.getAsFile();0===File.size&&LJS._tips("文件获取失败",0),do_upload(input,[File],0),textArea.unbind("paste"),layer.close(pindex)}}}}))}))}));