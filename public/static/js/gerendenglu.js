$(function(){
	$(".message-option .phone").click(function(){
		$(".message-option .phone").css("color","#e5383a");
		$(".message-option .zhanghao").css("color","#666666");
		$(".phone_denglu").css("display","block");
		$(".zhanghao_denglu").css("display","none");
		$(".phone_denglu").css("display","none");
		$(".zhanghao_denglu").css("display","block");
		console.log(111)
	});
	$(".message-option .zhanghao").click(function(){
		$(".message-option .zhanghao").css("color","#e5383a");
		$(".message-option .phone").css("color","#666666");
		$(".phone_denglu").css("display","block");
		$(".zhanghao_denglu").css("display","none");
	});
})