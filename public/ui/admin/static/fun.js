LJS._getjs(LCMS.url.static+'plugin/base64.min.js','','1');LJS._getjs(LCMS.url.static+'plugin/sortable.min.js','','1');LJS._getjs(LCMS.url.static+'Lrz4/lrz.bundle.js','','1');typeof top.LCMSUIINDEX=='undefined'&&(top.LCMSUIINDEX=0);var LCMSTIPS=LJS._getQuery('lcmstips');LCMSTIPS&&layui.notice.info(LCMSTIPS);if($('#APPNAV').length>0){if(self!=top){$('#APPNAV a').on('click',function(){top.history.pushState(null,null,'?'+$.base64.encode($(this).attr('href').match(/\?(.*)/)[0]))})};top==parent&&$('#APPNAV .button').addClass('active');$('#APPNAV .button').on('click',function(){$('#APPNAV .background').fadeIn(200);$('#APPNAV .menu').stop().animate({'left':0},100)});$('#APPNAV .background').on('click',function(){$('#APPNAV .background').fadeOut(200);$('#APPNAV .menu').stop().animate({'left':'-160px'},100)})};if($('#APPTAB').length>0){if(self!=top){$('#APPTAB a').on('click',function(){top.history.pushState(null,null,'?'+$.base64.encode($(this).attr('href').match(/\?(.*)/)[0]))})}};if(!!navigator.userAgent.match(/\(i[^;]+;( U;)? CPU.+Mac OS X/)){$('html,body').css({'max-width':window.screen.width})};if(LCMS.config.oss!='local'){var OSS_date=new Date();var OSS_base={datey:OSS_date.getFullYear().toString()+(OSS_date.getMonth()+1<10?'0'+(OSS_date.getMonth()+1):OSS_date.getMonth()+1),mime:function(name){var arr=name.split('.');return arr[arr.length-1].toLowerCase()},stop:function(type){if(type=='image'&&!LCMS.config.isupload.img){LJS._tips('您没有权限上传图片',0);return true}else if(type=='file'&&!LCMS.config.isupload.file){LJS._tips('您没有权限上传文件',0);return true}}};OSS_base.init=function(name,type){OSS_base.format=OSS_base.mime(name);OSS_base.name=LJS._date("ddHHiiss",OSS_date)+LJS._randstr(6)+'.'+OSS_base.format;OSS_base.file='upload/'+LCMS.ROOTID+'/'+type+'/'+OSS_base.datey+'/'+OSS_base.name};OSS_base.power=function(File){OSS_base.size=File.size;if(LCMS.config.mimelist.indexOf(OSS_base.format)==-1){return{code:0,msg:'禁止上传此格式文件'}}if(LCMS.config.attsize<Math.round(OSS_base.size/1000)){return{code:0,msg:'文件大小超过'+LCMS.config.attsize+'KB'}}};switch(LCMS.config.oss){case'qiniu':LJS._getjs(LCMS.url.site+'core/plugin/Qiniu/static/qiniu.min.js','','1');var OSS_qiniu=qiniu;LJS._get(LCMS.url.admin+'?n=upload&c=index&a=qiniu&action=token',function(res){if(res.code=='1'){OSS_base.token=res.data.token}},'json',true);var OSS_upload=async function(type,File,success,error){type=type?type:'image';if(OSS_base.stop(type)){error&&error();return false}OSS_base.init(File.name,type);var power=OSS_base.power(File);if(power){LJS._tips(power.msg,0);error&&error(power);return false};OSS_qiniu.upload(File,OSS_base.file,OSS_base.token,{},{useCdnDomain:true,disableStatisticsReport:true,retryCount:1}).subscribe({error(res){LJS._tips(res.message,0);error&&error({code:0,msg:res.message})},complete(){LJS._post(LCMS.url.admin+'index.php?n=upload&c=index&a=qiniu',{action:'success',type:type,name:OSS_base.name,file:OSS_base.file,datey:OSS_base.datey,size:OSS_base.size},function(res){if(res.code=='1'){success&&success(res)}else{LJS._tips(rst.msg,0);error&&error(res)}},'json')}})};break;case'tencent':LJS._getjs(LCMS.url.site+'core/plugin/Tencent/static/cos-js-sdk-v5.min.js','','1');var OSS_tencent;LJS._get(LCMS.url.admin+'?n=upload&c=index&a=tencent&action=token',function(res){if(res.code=='1'){OSS_base.token=res.data;var Token=res.data,Credentials=res.data.Credentials;if(Credentials){OSS_tencent=new COS({SecretId:Credentials.TmpSecretId,SecretKey:Credentials.TmpSecretKey,XCosSecurityToken:Credentials.Token,})}}},'json',true);var OSS_upload=function(type,File,success,error){type=type?type:'image';if(OSS_base.stop(type)){error&&error();return false};OSS_base.init(File.name,type);var power=OSS_base.power(File);if(power){LJS._tips(power.msg,0);error&&error(power);return false};if(OSS_tencent==undefined){LJS._tips('上传接口配置错误',0);error&&error({code:0,msg:'上传接口配置错误'});return false};OSS_tencent.putObject({Bucket:OSS_base.token.Bucket,Region:OSS_base.token.Region,Key:OSS_base.file,Body:File},function(err,data){if(err){LJS._tips(err,0);error&&error(err)}else{LJS._post(LCMS.url.admin+'index.php?n=upload&c=index&a=tencent',{action:'success',type:type,name:OSS_base.name,file:OSS_base.file,datey:OSS_base.datey,size:OSS_base.size},function(res){if(res.code=='1'){success&&success(res)}else{LJS._tips(rst.msg,0);error&&error(res)}},'json')}})};break;case'aliyun':var OSS_aliyun;LJS._get(LCMS.url.admin+'?n=upload&c=index&a=aliyun&action=token',function(res){if(res.code=='1'){OSS_base.token=res.data}},'json',true);var OSS_upload=function(type,File,success,error){type=type?type:'image';if(OSS_base.stop(type)){error&&error();return false}OSS_base.init(File.name,type);var power=OSS_base.power(File);if(power){LJS._tips(power.msg,0);error&&error(power);return false};if(!OSS_base.token){LJS._tips('上传接口配置错误',0);error&&error({code:0,msg:'上传接口配置错误'});return false};var formData=new FormData();formData.append('key',OSS_base.file);formData.append('policy',OSS_base.token.policy);formData.append('OSSAccessKeyId',OSS_base.token.AccessKeyId);formData.append('signature',OSS_base.token.signature);formData.append('success_action_status','200');formData.append('file',File);$.ajax({url:OSS_base.token.api,data:formData,processData:false,contentType:false,type:'POST',success:function(res,status,xhr){if(xhr.status==200){LJS._post(LCMS.url.admin+'index.php?n=upload&c=index&a=aliyun',{action:'success',type:type,name:OSS_base.name,file:OSS_base.file,datey:OSS_base.datey,size:OSS_base.size},function(res){if(res.code=='1'){success&&success(res)}else{LJS._tips(rst.msg,0);error&&error(res)}},'json')}},error:function(xhr){LJS._tips('上传接口无法访问，请检查云存储的跨域CORS设置',0);error&&error({code:0,msg:'上传接口无法访问，请检查云存储的跨域CORS设置'})}})};break}};var LOC_upload=function(type,File,success,complete){type=type?type:'image';var upload=function(formData){$.ajax({url:LCMS.url.admin+'index.php?n=upload&c=index&a=local&type='+type,data:formData,processData:false,contentType:false,type:'POST',dataType:"json",success:function(res){success&&success(res)},error:function(){LJS._tips('上传失败，可能是你的服务器不支持上传大文件',0)},complete:function(){complete&&complete()}})};var mime=File.type;if(mime=='image/jpg'||mime=='image/jpeg'){var quality=LCMS.config.attquality>0||LCMS.config.attwebp>0?parseFloat((LCMS.config.attquality/100).toFixed(1)):0.7;lrz(File,{width:quality==1?null:1980,quality:quality}).then(function(res){upload(res.formData)})}else{var formData=new FormData();formData.append('file',File);upload(formData)}};LJS._getjs('form.js',$('.layui-form'));LJS._getjs('select.js',$('.lcms-form-select'));LJS._getjs('selectN.js',$('.lcms-form-selectN'));LJS._getjs('tags.js',$('.lcms-form-tags'));LJS._getjs('color.js',$('.lcms-form-colorpicker'));LJS._getjs('slider.js',$('.lcms-form-slider'));LJS._getjs('date.js',$('.lcms-form-date'));LJS._getjs('upload.js',$('.lcms-form-upload-img'));LJS._getjs('file.js',$('.lcms-form-upload-file'));LJS._getjs('editor.js',$('.lcms-form-editor'));LJS._getjs('table.js',$('.lcms-form-table'));LJS._getjs('tree.js',$('.lcms-form-table-tree'));LJS._getjs('radio.js',$('.lcms-form-radio-tab'));LJS._getjs('spec.js',$('.lcms-form-spec'));LJS._getjs('icon.js',$('.lcms-form-icon'));LJS._getjs(LCMS.url.own_path+'admin/tpl/static/fun.js?ver='+(LCMS.app&&LCMS.app.ver?LCMS.app.ver:'1.0.0'));layui.util.fixbar();