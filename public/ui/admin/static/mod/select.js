layui.link(LCMS['url']['public']+'static/layui/exts/formSelects/formSelects-v4.css?ver='+LCMS.config.ver);$('.lcms-form-select').each(function(index){top.LCMSUIINDEX++;var selectId='LCMSSELECT'+top.LCMSUIINDEX,verify=$(this).attr('lay-verify');if($(this).attr('xm-select')){selectId=$(this).attr('xm-select')}else{$(this).attr('xm-select',selectId)};if(verify&&verify.indexOf("required")!=-1){$(this).attr('lay-verify',verify+'|'+selectId)}});layui.extend({formSelects:'formSelects/formSelects-v4.min'}).use(['formSelects'],function(){var formSelects=layui.formSelects;$('.lcms-form-select').each(function(){var _self=$(this),selectId=_self.attr('xm-select'),val=_self.attr('xm-select-val');_self.parent('.layui-input-block').parent('.layui-form-item').attr('pane','true');formSelects.config(selectId,{searchName:'keyword',keyName:'title',keyVal:'value',keySel:'selected',keyDis:'disabled',keyChildren:'children',delay:500,direction:'auto',response:{statusCode:1,statusName:'code',msgName:'msg',dataName:'data'},beforeSuccess:function(selectId,url,searchVal,result){if(result){if(!result.data){result={code:0,msg:'没有选项',data:{}}}}else{result={code:0,msg:'请求失败',data:{}}}return result},success:function(selectId,url,searchVal,result){var linkage=false;if(result.data!=null&&result.data[0]){for(var i=0;i<result.data.length;i++){if(typeof(result.data[i].children)!="undefined"){linkage=true;break}};if(!_self.attr('linkage')){linkage=false};formSelects.data(selectId,'local',{linkageWidth:100,arr:result.data,linkage:linkage})};if(!_self.attr('ajaxed')){_self.attr('ajaxed',1);formSelects.value(selectId,val.toString().split(','))}}},true);formSelects.value(selectId,val.toString().split(','))})});