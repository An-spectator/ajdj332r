layui.extend({treeGrid:'treeGrid/treeGrid'}).use(['treeGrid'],function(){var Treeajax=function(url,arr){LJS._post(url,{'LC':arr},function(res){if(res.code=='1'){LJS._tips(res.msg);LJS._lazydo(function(){window.location.reload()})}else{LJS._tips(res.msg,0)};layer.closeAll()})};$('.lcms-form-table-tree-box').each(function(index){var cols=[],tableId='LCMSTABLETREE'+index,that=$(this),table=$(this).children('.lcms-form-table-tree'),openall=$(this).children('.lcms-form-table-tree-openall'),form=$(this).children('form');var data=JSON.parse($.base64.decode(table.attr('data')));table.attr('id',tableId);openall.attr('data-id',tableId);data.id&&$(this).attr('id',data.id);cols[0]={type:'checkbox',sort:true};for(var i=1;i<=data.cols.length;i++){var myindex=i-1;if(data.cols[myindex].toolbar){cols[i]={title:data.cols[myindex].title,templet:function(){return data.cols[myindex].toolbar}}}else{cols[i]=data.cols[myindex]}};var treeId=data.top.split('|');layui.treeGrid.render({id:tableId,elem:'#'+tableId,idField:'id',url:data.url+'&page=1&limit=1000',treeId:treeId[1]?treeId[0]:'id',treeUpId:data.top?(treeId[1]?treeId[1]:data.top):'top_id',treeShowName:data.show?data.show:'title',isOpenDefault:true,branch:['',''],leaf:'&#xe60a;',cols:[cols],page:false});layui.treeGrid.on('tool('+tableId+')',function(obj){var data=$(this).data();switch(obj.event){case'ajax':if(data.tips){layer.alert(data.tips,{area:'120px'},function(){Treeajax(data.url,obj.data)})}else{Treeajax(data.url,obj.data)};break;case'iframe':LJS._iframe(data.url+'&id='+obj.data.id,$(this).html());break;case'href':window.location.href=data.url+'&id='+obj.data.id;break;case'switch':var elm=$(this).prev('input');var url=elm.attr('data-url');if(url.length>0){LJS._post(url,{'LC':{'id':LJS._getQuery('id',url),'name':LJS._getQuery('name',url),'value':elm.is(':checked')?'1':'0'}},function(res){if(res.code=='1'){LJS._tips(res.msg);if(res.go){switch(res.go){case'close':LJS._lazydo(function(){LJS._closeframe(true)});break;case'reload':LJS._lazydo(function(){window.location.reload()});break;case'reload-parent':LJS._lazydo(function(){parent.window.location.reload()});break;case'reload-top':LJS._lazydo(function(){top.window.location.reload()});break;default:LJS._lazydo(function(){window.location.href=res.go});break}}}else{LJS._tips(res.msg,0);elm.prop("checked",elm.is(':checked')?false:true);layui.form.render('checkbox')}})}break}});layui.treeGrid.on('edit('+tableId+')',function(obj){LJS._post(data.url+'-save',{'LC':{'id':obj.data.id,'name':obj.field,'value':obj.value}},function(res){if(res.code=='1'){LJS._tips(res.msg)}else{LJS._tips(res.msg,0)}})});openall.on('click',function(){var treedata=layui.treeGrid.getDataTreeList(tableId);layui.treeGrid.treeOpenAll(tableId,!treedata[0][layui.treeGrid.config.cols.isOpen])});$(this).children('button').on('click',function(){var event=$(this).attr('lay-event');var data=$(this).data();switch(event){case'ajax':var checkStatus=layui.treeGrid.checkStatus(tableId);if(data.tips){layer.alert(data.tips,{area:'120px'},function(){Treeajax(data.url,checkStatus.data)})}else{Treeajax(data.url,checkStatus.data)};break;case'iframe':LJS._iframe(data.url,$(this).html());break;case'href':window.location.href=data.url;break}})})});