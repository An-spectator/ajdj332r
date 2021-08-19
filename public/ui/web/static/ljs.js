var LJS={_loadstart:function(){if(typeof mlayer=='object'){var index=mlayer.loading({title:'加载中'})}else{var index=layer.load(2,{shade:[0.3,'#fff']})}return index},_loadend:function(index){if(typeof mlayer=='object'){mlayer.close(index)}else{layer.close(index)}},_tips:function(msg,type){if(msg){if(typeof mlayer=='object'){if(type===0){mlayer.error({title:msg,time:1})}else{mlayer.success({title:msg,time:1})}}else{if(type===0){layer.msg(msg,{icon:2,anim:6,time:1000})}else{layer.msg(msg,{icon:1,time:1000})}}}},_get:function(url,callback,type){url=url.indexOf("?")!=-1?url+'&'+Math.random():url+'?'+Math.random();$.ajax({url:url,dataType:type?type:'html',cache:false,timeout:15000,success:function(res){(callback&&typeof(callback)==="function")&&callback(res)},error:function(){(callback&&typeof(callback)==="function")&&callback({code:0,msg:'数据加载失败'})},complete:function(){}})},_post:function(url,data,callback,type){var loading=LJS._loadstart();$.ajax({url:url,data:data,type:'POST',dataType:type?type:'json',cache:false,timeout:15000,success:function(res){(callback&&typeof(callback)==="function")&&callback(res)},error:function(){(callback&&typeof(callback)==="function")&&callback({code:0,msg:'数据加载失败'})},complete:function(){LJS._loadend(loading)}})},_getjs:function(url,callback,asy){$.ajaxSetup({async:!asy?true:false,cache:true});$.getScript(url,function(){(callback&&typeof(callback)==="function")&&callback()});$.ajaxSetup({async:true,cache:false})},_lazydo:function(callback,t){setTimeout(function(){(callback&&typeof(callback)==="function")&&callback()},t?t:1000)},_isdev:function(type){type=type?type:'micromessenger';var ua=window.navigator.userAgent.toLowerCase();if(ua.indexOf(type.toLowerCase())!=-1){return true}else{return false}},_lazyload:function(imgs){setTimeout(function(){imgs=imgs?imgs:$('img.lazyload');imgs.css('opacity','1');if(imgs.length>0){$('head').append('<style type="text/css">img.lazyload{-o-object-fit:scale-down;object-fit:scale-down;max-width:20px!important;max-height:20px!important;margin:auto;display:block;opacity:0;}</style>');imgs.lazyload({data_attribute:"src",effect:"fadeIn",threshold:200,failure_limit:10,skip_invisible:false})}},10)},_lazylunbo:function(imgs){setTimeout(function(){imgs=imgs?imgs:$('img.lazylunbo');if(imgs.length>0){$('head').append('<style type="text/css">img.lazylunbo{-o-object-fit:scale-down;object-fit:scale-down;max-width:50px!important;max-height:50px!important;margin:auto;display:block;opacity:0;}</style>');imgs.each(function(){var that=$(this);var src=that.attr('data-src');var placeholder="data:image/gif;base64,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";that.attr('src',placeholder).css('opacity','1');var img=new Image();img.src=src;img.onload=function(){that.animate({opacity:'0'},50,'',function(){that.attr('src',src)});LJS._lazydo(function(){that.animate({opacity:'1'},100).removeClass('lazylunbo')},51)}})}},10)},_pjax:function(){(function($){$.fn.hoverDelay=function(options){var defaults={hoverDuring:50,outDuring:0,hoverEvent:function(that){$.noop()},outEvent:function(that){$.noop()}};var sets=$.extend(defaults,options||{});var hoverTimer,outTimer;return $(this).each(function(){var that=$(this);that.hover(function(){clearTimeout(outTimer);var func=function(){sets.hoverEvent(that)};hoverTimer=setTimeout(func,sets.hoverDuring)},function(){clearTimeout(hoverTimer);var func=function(){sets.outEvent(that)};outTimer=setTimeout(func,sets.outDuring)})})}})(jQuery);if($('a').length>0){var xhr;$("a").hoverDelay({hoverEvent:function(that){var url=false,href=that.attr('href');if(href){if(href.search("http://")!=-1||href.search("https://")!=-1){if(href.search(window.location.origin)!=-1){url=href}else{url=false}}else if(href!='javascript:;'){url=href}}else{url=window.location.origin}if(url!==false){xhr=$.ajax({type:'GET',url:url,timeout:5000,cache:true,success:function(){}})}},outEvent:function(that){if(xhr){xhr.abort()}}})}},_audio:function(src,loop){var audio=new Audio();audio.src=src;audio.loop=loop==1?1:0;audio.preload='meta';audio.play();return audio},_imagePreview:function(imgs,callback){imgs=imgs?imgs:$('.editor img');if(imgs.length>0){var srcList=Array();LJS._lazydo(function(){var imgurl=function(url){if(url.indexOf('//')==-1){url=window.location.protocol+'//'+window.location.host+'/'+url.replace('../','')};return url};imgs.each(function(index){srcList.push(imgurl($(this).attr("src")))});imgs.on('click',function(){var src=$(this).attr('src');var index=imgs.index($(this));if(!src||!srcList||srcList.length==0){return};res={index:index,src:imgurl(src),srcList:srcList};(callback&&typeof(callback)==="function")&&callback(res)})},500)}},_loadfun:function(fun){var oldonload=window.onload;if(typeof window.onload!='function'){window.onload=fun}else{window.onload=function(){oldonload();fun()}}},_tpl:function(tpl,arr){for(var key in arr){var reg='/{'+key+'}/g';arr[key]=arr[key]?arr[key]:'';tpl=tpl.replace(eval(reg),arr[key])};return tpl},_getQuery:function(name,url){url=url?url:window.location.search;var reg=new RegExp("(^|&)"+name+"=([^&]*)(&|$)",'i');var result=url.substr(1).match(reg);if(result!=null){return decodeURIComponent(result[2])}else{return null}},_randstr:function(len){len=len||4;var chars='ABCDEFGHJKMNPQRSTWXYZabcdefhijkmnprstwxyz2345678';var str='';for(var i=0;i<len;i++){str+=chars.charAt(Math.floor(Math.random()*chars.length))};return str},_iframe:function(url,title,area,shade){if(!area){if($(window).width()<768){var area=['100%','100%']}else{var area=['700px','600px']}};return layer.open({id:'LCMSLAYERIFRAME'+LJS._randstr(4),type:2,title:title?title:' ',content:url,area:area,maxmin:true,resize:true,scrollbar:true,shade:shade?0.3:0,zIndex:layer.zIndex,success:function(layero,index){layer.setTop(layero)}})},_closeframe:function(reload){var index=parent.layer.getFrameIndex(window.name);parent.layer.close(index);reload?reload:false;if(reload){parent.location.reload()}}};