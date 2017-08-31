jQuery(document).ready(function($) {


	/* =========== start of loader =================*/

	var calcPercent;
	var stamp = "?rel=2c5c21";

	var assets = [
		'public/static/img/chenningyi.jpg', 
		'public/static/img/jinchenglei.jpg', 
		'public/static/img/wangyifei.jpg', 
		'public/static/img/wangyingmiao.jpg', 
		'public/static/img/yefangyu.jpg', 
		'public/static/img/youxiaofan.jpg', 
		'public/static/img/welcome.png', 
		'public/static/img/welcome-1.png', 
		'public/static/img/welcome-2.png', 
		'public/static/img/welcome-3.png', 
		'public/static/img/welcome-4.png', 
		'public/static/img/map.png', 
		'public/static/img/t02.jpg', 
		'public/static/img/t01.jpg', 
		'public/static/img/logo.png', 
		'public/static/img/pattern-1.png', 
		'public/static/img/pattern-2.png', 
		'public/static/img/pattern-3.png', 
		'public/static/img/pattern-4.png', 
		'public/static/img/logo.png'
		];
	
	function preload(imgArray) {
		$(".bottle-wrap").addClass("active");
		var increment = Math.floor(100 / imgArray.length);
		var i = 0;
		var total = imgArray.length;
		$(imgArray).each(function() {

			$('<img>').attr("src", this + stamp).bind("load", function() {

				$(".bottle-mask").animate({
					height: "-=" + increment + "%"
				}, 200);

				i++;
				
				if (i >= total) {
					$(".bottle-mask").animate({
						height: "0%"
					}, 300, function() {

						setTimeout(function(){
							$(".page-loading").fadeOut(300);
							$(".page-welcome ul").addClass("show");
							$(".page-welcome .button-start").addClass("show");
						}, 300);
						
					});
				}

			});
			
		});
	}

	preload(assets);


	$(".designer-item").bind("click", function(e){
		var name = $(this).attr("data-name");

		$(".designer-list").addClass("freezed");

		$(".designer[data-name=" + name +"]").addClass("active");

		e.stopPropagation();
	});

	$(".designer .btn-close").bind("click", function(e){

		$(".designer-list").removeClass("freezed");

		$(this).parent(".designer").removeClass("active");

		e.stopPropagation();
	});


	$(".btn-open-qrcode").bind("click", function(e){
		$(".qrcode").addClass("active");
		$("body").addClass("freeze");

		e.stopPropagation();
	});

	$(".qrcode-backdrop").bind("click", function(e){
		if ($(".qrcode").hasClass("active")) {
			$(".qrcode").removeClass("active");
			$("body").removeClass("freeze");
			return false;
		}
	});

	$(document).keyup(function(e) {
		if (e.keyCode == 27 && $(".qrcode").hasClass("active")) {

			$(".qrcode").removeClass("active");
			$("body").removeClass("freeze");

			return false;

		}
	});

	// $('.nc-mag-shop .slides').slick({
	// 	dots: false,
	// 	slidesToShow: 2,
	// 	slidesToScroll: 2,
	// 	infinite: true,
	// 	autoplay: true,
	// 	autoplaySpeed: 3000,
	// 	fade: false,
	// 	cssEase: 'ease',
	// 	arrows: true,
	// 	rows: 1,
	// 	responsive: [
	// 	{
	// 		breakpoint: 680,
	// 		settings: {
	// 			slidesToShow: 1,
	// 			slidesToScroll: 1
	// 		}
	// 	},
	// 	{
	// 		breakpoint: 480,
	// 		settings: {
	// 			slidesToShow: 1,
	// 			slidesToScroll: 1
	// 		}
	// 	}]
	// });

	$(".tab").bind("click", function(){
		var page = $(this).attr("data-page");
		$(".page-" + page).addClass("active").siblings(".page").removeClass("active");

		$(".tab").removeClass("active");
		$(this).addClass("active");
	});


	$(".page-welcome, .page-welcome .btn-close").bind("click", function(){
		$(".page-welcome").fadeOut(300);
	});

	$(".popup-submit .btn-close, .popup-submit .btn-step-close").bind("click", function(){
		$(".popup-submit").removeClass("active");
	});

	$("#upload-from-tab").bind("change", function(){
		$(".popup-submit").addClass("active");

		var files = !!this.files ? this.files : [];
        if (!files.length || !window.FileReader) return;

        if (/^image/.test( files[0].type)){
            var reader = new FileReader();
            reader.readAsDataURL(files[0]);

            reader.onloadend = function(){
            	// $("#preview-image").css("background-image", "url("+this.result+")");
            	$(".preview-image").attr("src", this.result);
            }
        }

	});

	$(".btn-filter-red").bind("click", function(){
		$(".btn-filter").removeClass("active");
		$(this).addClass("active");
		$(".preview-image-wrap .image-filter").removeClass("filter-red filter-white filter-blue").addClass("filter-red");
	});

	$(".btn-filter-blue").bind("click", function(){
		$(".btn-filter").removeClass("active");
		$(this).addClass("active");
		$(".preview-image-wrap .image-filter").removeClass("filter-red filter-white filter-blue").addClass("filter-blue");
	});

	$(".btn-filter-white").bind("click", function(){
		$(".btn-filter").removeClass("active");
		$(this).addClass("active");
		$(".preview-image-wrap .image-filter").removeClass("filter-red filter-white filter-blue").addClass("filter-white");
	});


	$(".submit-step.step-1 .btn-step-next").bind("click", function(){
		$(".submit-step.step-1").removeClass("active");
		$(".submit-step.step-2").addClass("active");
	});

	$(".submit-step.step-2 .btn-step-prev").bind("click", function(){
		$(".submit-step.step-1").addClass("active");
		$(".submit-step.step-2").removeClass("active");
	});

	// 提交按钮，提交表单和上传图片，成功后进入
	$(".submit-step.step-2 .btn-step-submit").bind("click", function(){

		// 提交成功后进入step-3
		$(".submit-step.step-2").removeClass("active");
		$(".submit-step.step-3").addClass("active");
	});



});