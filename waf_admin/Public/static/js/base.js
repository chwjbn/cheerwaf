// JavaScript Document
function alertMsg(content, callback)
{
   return layer.msg(content, {icon: 0},callback);
}

function successMsg(content,callback)
{
	return layer.msg(content, {icon: 1},callback);
}

$(function(){



	$("#txtKeywords").keydown(function(event)
	{
		if(event && event.keyCode==13)
		{
			$(this).next().click();
		}
	});

	$('.btn-submit').click(function(){
	
	   var pThis=$(this);
	   var pForm=pThis.parents('form');
	   if(!pForm)
	   {
	      return;
	   }
	   
	   pForm=pForm[0];
	      
	   var noclick=pThis.data('noclick');

	   if(noclick)
	   {
	      return;
	   }
	   pThis.data('noclick',1);
	   
	   function showRequest(formData, jqForm, options){}
	   
	   function showResponse(responseText, statusText)
	   {

		   
		    var error=responseText.error_code;
			var msg=responseText.msg;
			var res_data=responseText.data;
			
			if(!msg){msg='网络异常,数据无法提交,请稍候再试!';}
			
			var callBack=eval(pThis.data('callback'));
			if(!callBack)
			{
			   callBack=function(){window.location.href=window.location.href;};
			}
			
			
			if(error=='0')
			{
				successMsg(msg,function(){callBack.call(callBack,res_data)});
				return;
			}
			
			if(error=='-1')
			{
				callBack.call(callBack,res_data);
				return;
			}

		    //防止重复点击
		    alertMsg(msg,function(){pThis.data('noclick',0);});
	   }
	   
	   function showError()
	   {
		   pThis.data('noclick',0);
		   var msg='网络异常,数据无法提交,请稍候再试!';
		   alertMsg(msg);
	   }
	   
	   var options = { 
				   beforeSubmit: showRequest,  //提交前的回调函数
				   success: showResponse,     //提交后的回调函数
				   error:showError,          //提交失败后回调函数
				   timeout: 10000           //限制请求的时间，当请求大于10秒后，跳出请求
	   }
	   
	   $(pForm).ajaxSubmit(options);
	});
	
	
	$('.see-image').click(function(){
	    var src=$(this).attr('src')||$(this).data('src');
		
		layer.open({
			type: 1,
			title:'查看图片',
			skin: 'layui-layer-rim', //加上边框
			area: ['100%', '100%'], //宽高
			content: '<img src="'+src+'" />'
		});
		
	});
	
 	$('.btn-pop').click(function(){
		
		var url=$(this).data('url');
	    var pop_title=$(this).data('title');
		
		layer.open({
            type: 2,
            title: pop_title,
            shadeClose: true,
            shade: true,
            maxmin: true, //开启最大化最小化按钮
            area: ['90%', '90%'],
            content: url
        });     
		
		}
	);
	
	
	$('.btn-action').click(function(){
  
		var pThis=$(this);
		
		var process=pThis.data('process');
		if(process)
		{
			return;
		}
		
		pThis.data('process',1);
		
		var url=pThis.data('url');
		
		$.ajax({
		   url:url,
		   type:'GET',
		   cache:false,
		   dataType:'json',
		   complete:function(XHR, TS){
			   pThis.data('process',0);
			   
		   },
		   error:function (XMLHttpRequest, textStatus, errorThrown){
		       alertMsg('网络异常,请稍后再试!');
		   },
		   success:function(data, textStatus, jqXHR){
			   
			   var msg=data.msg;
			   if(!msg)
			   {
			      msg='网络异常,请稍后再试!';
			   }
			   
			   if(data.error_code!='0')
			   {
				   alertMsg(msg);
				   return;
			   }
			   
			    var callBack=eval(pThis.data('callback'));
				if(!callBack)
				{
				   callBack=function(){window.location.href=window.location.href;};
				}
			   
			    successMsg(msg,function(){callBack.call(callBack,data.data)});
		   }
		   });
		
     
    });


	
});