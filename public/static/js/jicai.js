$(document).ready(function() {
//	$("#jicai-header").load("./html/header.html")
	$("#jicai-footer").load("./html/footer.html")
	
	$("#jicai-main .shangpin .rightUl li").each(function(){
		$(this).hover(function(){
//			setTimeout(function(){
				$(this).find(".slideUP").slideDown(200);
//			},10)
		},function(){
			$(this).find(".slideUP").slideUp(200);
		})
	})
})