$(document).ready(function() {
//	注册表的头部四栏点击变换
	$("#zhuce-main .main-shezhi li").eq(0).css({
				"border-bottom":"2px solid #e6393d",
				"color": "#e6393d"
			}).find("i").css("background","#E6393D")
	$("#zhuce-main .main-shezhi li").each(function(){
		$(this).click(function(){
			$(this).css({
				"border-bottom":"2px solid #e6393d",
				"color": "#e6393d"
			}).find("i").css("background","#E6393D")
			$(this).siblings().css({
				"border-bottom":"2px solid transparent",
				"color": ""
			}).find("i").css("background","#ccc")
			var idx = $(this).index();
			$("#zhuce-main .li-article").eq(idx).show().siblings(".li-article").hide()
		})
	})
	
//	设置登录名
//	/判断输入内容是否为空    
	function IsNull(){    
	    var str = document.getElementById('str').value.trim();    
	    if(str.length==0){    
	        alert('对不起，文本框不能为空或者为空格!');//请将“文本框”改成你需要验证的属性名称!    
	    }    
	} 
//判断输入的字符是否为中文    
	function IsChinese(val){        
        if(val.length!=0){    
	        reg=/^[\u0391-\uFFE5]+$/;    
	        if(!reg.test(str)){    
	            alert("对不起，您输入的字符串类型格式不正确!");//请将“字符串类型”要换成你要验证的那个属性名称！    
	        }    
        }    
	} 
	$(".main-denglu .input-text").eq(0).focus(function(){
		
	})
	$(".main-denglu .input-text").eq(0).blur(function(){
       var value = $(this).val().trim();
       if(value.length == 0){
       		$(this).css("border","1px solid #e6393d").siblings(".val-length").show();
       		value = "";
       		console.log(value.length)
       }
    });
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
})