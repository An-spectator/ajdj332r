window.UEDITOR_HOME_URL=LCMS.url.static+"Neditor/";layui.link(LCMS.url.static+"Neditor/themes/notadd/css/neditor.min.css?v="+LCMS.config.ver);LJS._getjs(LCMS.url.static+"Neditor/neditor.cfg.js","","1");LJS._getjs(LCMS.url.static+"Neditor/neditor.all.js","","1");LJS._getjs(LCMS.url.static+"Neditor/neditor.service.js","","1");LJS._getjs(LCMS.url.static+"Neditor/i18n/zh-cn/zh-cn.js","","1");LJS._getjs(LCMS.url.static+"Neditor/third-party/browser-md5-file.min.js","","1");window.lcms_editor_getbody=function(id){var editor=UE.getEditor(id);if(editor.queryCommandState("source")!=0){editor.execCommand("source")}var content=editor.getContent();return content?content:""};window.addEventListener("message",function(e){var data=e.data;if(data&&data.type){switch(data.type){case"lcms-editor-addimage":for(var i=0;i<data.list.length;i++){var src=data.list[i],alt=data.list[i].split("/")[5];if(LCMS.config.oss!="local"){src=LCMS.config.cdn+src;src=src.replace("../","")}window.LCMSEDITORNOW.execCommand("inserthtml",'<img src="'+src+'" alt="'+alt+'" />')}break;case"lcms-editor-addivideo":window.LCMSEDITORNOW.execCommand("inserthtml",'<p class="player-iframe">'+data.content+"</p>");break;case"lcms-editor-addvideo":if(LCMS.config.oss!="local"){if(data.field.src.indexOf("://")==-1){data.field.src=LCMS.config.cdn+data.field.src.replace("../","")}if(data.field.poster&&data.field.poster.indexOf("://")==-1){data.field.poster=LCMS.config.cdn+data.field.poster.replace("../","")}}var params=data.field.autoplay>0?' autoplay="autoplay"':"";params+=data.field.loop>0?' loop="loop"':"";window.LCMSEDITORNOW.execCommand("inserthtml",'<p class="player-video"><video class="edui-faked-video" src="'+data.field.src+'" poster="'+data.field.poster+'" width="'+data.field.width+'" height="'+data.field.height+'" controls="controls"'+params+"></video></p>");break;case"lcms-editor-addattachment":var name=decodeURIComponent(data.file.substring(data.file.lastIndexOf("/")+1));var mime=name.substring(name.lastIndexOf(".")+1),icon="";switch(mime){case"chm":icon="chm.png";break;case"exe":icon="exe.png";break;case"pdf":icon="pdf.png";break;case"psd":icon="psd.png";break;case"txt":icon="txt.png";break;case"rar":case"zip":case"7z":icon="rar.png";break;case"doc":case"docx":case"wps":icon="doc.png";break;case"xls":case"xlsx":case"et":icon="xls.png";break;case"ppt":case"pptx":case"dps":icon="ppt.png";break;case"mp3":case"wav":case"wma":icon="mp3.png";break;case"jpg":case"jpeg":case"gif":case"bmp":case"png":case"webp":icon="jpg.png";break;case"mv":case"mp4":case"mpg":case"avi":icon="mp4.png";break;default:icon="default.png";break}if(LCMS.config.oss!="local"&&data.file.indexOf("://")==-1){data.file=LCMS.config.cdn+data.file;data.file=data.file.replace("../","")}window.LCMSEDITORNOW.execCommand("inserthtml",'<p class="attachment-box" style="position:relative!important;box-sizing:border-box!important;padding:5px 5px 5px 45px!important;border:1px #EBEEF5 solid!important;font-weight:bold!important;height:44px!important;line-height:34px!important;overflow:hidden!important;text-overflow:ellipsis!important;display:-webkit-box!important;-webkit-line-clamp: 1;-webkit-box-orient: vertical;"><img style="position:absolute!important;left:5px!important;top:5px!important;width:30px!important;height:33px!important;margin:0!important;padding:0!important" src="'+LCMS.url.static+"Neditor/dialogs/attachment/fileTypeImages/"+icon+'" /><a href="'+data.file+'" title="'+name+'">'+name+"</a></p>");break;case"lcms-editor-addmap":window.LCMSEDITORNOW.execCommand("inserthtml",'<p class="map-iframe">'+data.content+"</p>");break}}});UE.registerUI("myinsertimage gallery insertvideo attachment map myinsertcode source",function(editor,uiName){var uiTitle={myinsertimage:{title:"上传图片",className:"edui-for-insertimage",onclick:function(){LJS._iframe("index.php?t=sys&n=upload&c=gallery&a=upload&many=1","上传图片",1,["550px","550px"])},},gallery:{title:"图库",className:"edui-for-simpleupload",onclick:function(){LJS._iframe("index.php?t=sys&n=upload&c=gallery&many=1&id=LCMSEDITOR","图库",1,["550px","550px"])},},insertvideo:{title:"上传视频",onclick:function(){LJS._iframe("index.php?t=sys&n=upload&c=gallery&a=ivideo","视频",1,["550px","550px"])},},attachment:{title:"上传附件",onclick:function(){LJS._iframe("index.php?t=sys&n=upload&c=gallery&a=attachment","上传附件",1,["300px","165px"])},},map:{title:"天地图",onclick:function(){LJS._iframe(LCMS.url.public+"static/Map/tianditu/editor.html?ver="+LCMS.config.ver,"天地图",1,["550px","550px"])},},myinsertcode:{title:"行内代码",className:"edui-for-code",onclick:function(){var range=editor.selection.getRange();if(range.endContainer.parentNode.nodeName!="CODE"&&range.cloneContents()){var text=range.cloneContents().textContent;editor.execCommand("inserthtml","<code>"+text+"</code>")}},},source:{title:"查看源代码",onclick:function(){editor.execCommand("source")},},};var btn=new UE.ui.Button({name:uiName,title:uiTitle[uiName].title,className:uiTitle[uiName].className,onclick:function(){window.LCMSEDITORNOW=editor;uiTitle[uiName].onclick()},});editor.addListener("selectionchange",function(){var state=editor.queryCommandState(uiName);if(state==-1){btn.setDisabled(true);btn.setChecked(false)}else{btn.setDisabled(false);btn.setChecked(state)}});return btn});$(".lcms-form-editor").each(function(index){const _this=$(this);var options={toolbars:[],insertorderedlist:{},insertunorderedlist:{},catcherUrlPrefix:"",catcherFieldName:"files",catcherActionName:"uploadcatcher",catchRemoteImageEnable:true,paragraph:{h2:"H2-标题1",h3:"H3-标题2",h4:"H4-标题3",p:"P-段落",div:"DIV-块"},fontsize:[10,12,14,16,18,20,24,36],zIndex:1,autoFloatEnabled:true,autoHeightEnabled:true,allowDivTransToP:false,initialFrameWidth:null,initialFrameHeight:320,iframeCssUrl:LCMS.url.public+"ui/admin/static/editor.css?"+LCMS.config.ver};if(window.screen.width>=768){options.toolbars=[["undo","redo","fullscreen","paragraph","fontsize","|","bold","italic","underline","strikethrough","autotypeset","removeformat","formatmatch","myinsertcode","blockquote","pasteplain","|","touppercase","tolowercase","forecolor","backcolor","insertorderedlist","insertunorderedlist","|","justifyleft","justifycenter","justifyright","justifyjustify","|","inserttable","|","imagenone","imageleft","imageright","imagecenter","|","link","myinsertimage","gallery","insertvideo","map","scrawl","attachment","insertframe","|","horizontal","insertcode","spechars","searchreplace","|","source","insertimage"]]}else{options.contextMenu=[];options.toolbars=[["paragraph","fontsize","|","bold","italic","underline","strikethrough","myinsertcode","blockquote","|","forecolor","backcolor","insertorderedlist","insertunorderedlist","|","justifyleft","justifycenter","justifyright","|","inserttable","|","link","myinsertimage","gallery","insertvideo","attachment","|","horizontal"]]}LJS._lazydo(function(){top.LCMSUIINDEX++;var id="LCMSEDITOR"+top.LCMSUIINDEX,editor="editor"+top.LCMSUIINDEX;_this.children("script").attr("id",id);editor=UE.getEditor(id,options);index==0&&LJS._lazydo(function(){editor.focus()},500);editor.addListener("focus",function(){top.LCMSEDITORFOCUSID=id})},300)});