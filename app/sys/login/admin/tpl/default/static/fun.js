$.getScript(LCMS.url.public+'ui/web/static/ljs.js');$(document).ready(function(){if($('.login').length>0){particlesJS("lizitexiao",{"particles":{"number":{"value":20,"density":{"enable":true,"value_area":800}},"color":{"value":"#ffffff"},"shape":{"type":"circle","stroke":{"width":0,"color":"#000000"},"polygon":{"nb_sides":5},"image":{"src":"","width":100,"height":100}},"opacity":{"value":0.5,"random":false,"anim":{"enable":false,"speed":1,"opacity_min":0.1,"sync":false}},"size":{"value":10,"random":true,"anim":{"enable":false,"speed":50,"size_min":0.1,"sync":false}},"line_linked":{"enable":true,"distance":300,"color":"#ffffff","opacity":0.4,"width":2},"move":{"enable":true,"speed":8,"direction":"none","random":false,"straight":false,"out_mode":"out","bounce":false,"attract":{"enable":false,"rotateX":600,"rotateY":1200}}},"interactivity":{"detect_on":"canvas","events":{"onhover":{"enable":false,"mode":"repulse"},"onclick":{"enable":false,"mode":"push"},"resize":true},"modes":{"grab":{"distance":800,"line_linked":{"opacity":1}},"bubble":{"distance":800,"size":80,"duration":2,"opacity":0.8,"speed":3},"repulse":{"distance":400,"duration":0.4},"push":{"particles_nb":4},"remove":{"particles_nb":2}}},"retina_detect":true})};if($('.login-readme').length>0){$('.login-readme a').on('click',function(){var title=$(this).text();if($(window).width()<768){var area=['100%','100%']}else{var area=['700px','600px']}LJS._post(LCMS.url.own_form+'readme&c=index',{action:$(this).attr('type')},function(res){if(res.code==1){LJS._iframe('<div class="lcms-editor" style="padding:20px">'+res.data+'</div>',title)}},'json',true)})};var qrcheck,showqr=function(name){var qrbox=$('.login-qr-'+name),qrimg=$('.login-qr-'+name+' img'),rfbtn=$('.login-refresh-'+name),pingtest=function(){LJS._get(LCMS.url.now+'&a=ping',function(res){if(res.code==1){LJS._tips(res.msg);LJS._lazydo(function(){window.location.href=res.go},1000)}else{qrcheck&&setTimeout(function(){pingtest()},1000)}},'json')};if(qrbox.length<=0){return false};var refresh=function(){rfbtn.hide();LJS._get(qrbox.attr('data-url')+'&name='+name,function(res){if(res.code==1){qrimg.attr('src',LCMS.url.qrcode+encodeURIComponent(res.go)).show();qrcheck=true;qrcheck&&pingtest();setTimeout(function(){qrcheck=false;$('.login-refresh').show()},180*1000)}else{LJS._tips('二维码获取失败',0)}},'json')};if(qrimg.attr('src')=='')refresh();rfbtn.on('click',function(){refresh()})};if($('.login-tab').length>0){$('.login-tab a').on('click',function(){var index=$(this).index(),name=$(this).attr('name');$('.login-tab .active').removeClass('active');$(this).addClass('active');$('.login-body').hide().eq(index).show();showqr(name)})};$('.login-form form').submit(function(){var url=$(this).attr('action'),query=$(this).serialize();LJS._post(url+query,{},function(res){if(res.code==1){LJS._tips(res.msg);LJS._lazydo(function(){if(res.go=="goback"){window.history.back()}else{window.location.href=res.go}},1000)}else{if(typeof(CAPTCHARESET)=='function'){CAPTCHARESET()}else{$('.login-code input').val('');$('.login-code img').click()};LJS._tips(res.msg,0)}},'json');return false});$('.login-input div').on('click',function(){var name=$(this).attr('name'),ipts=['name','pass',name],value={};for(var i=0;i<ipts.length;i++){var ipt=$('form input[name="'+ipts[i]+'"]');if(ipt.length>0){if(ipt.val()==''){LJS._tips('请输入'+ipt.attr('placeholder'),0);ipt.focus();return false};if(ipt.val().length<6){LJS._tips(ipt.attr('placeholder')+'长度不能小于6位',0);ipt.focus();return false};if(ipts[i]=='name'&&!new RegExp("^[a-zA-Z0-9_\u4e00-\u9fa5\\s·]+$").test(ipt.val())){LJS._tips(ipt.attr('placeholder')+'不能有特殊字符',0);ipt.focus();return false}value[ipts[i]]=ipt.val()}};var layindex=layer.open({type:1,title:"请输入验证码",area:["300px","200px"],content:$(".tplcode").html()});$(".login-send").on("click",function(){var code=$(".login-send-code").val();if(!code){LJS._tips('请输入验证码',0);return false};LJS._post($(this).attr('data-url'),{action:name,value:value,code:code},function(res){if(res.code==1){LJS._tips(res.msg);layer.close(layindex)}else{LJS._tips(res.msg,0)}},'json')})})});