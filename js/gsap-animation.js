
(function ($) {
	"use strict";

	gsap.registerPlugin(ScrollTrigger, SplitText);
	gsap.config({
		nullTargetWarn: false,
		trialWarn: false
	});

	/*----  Functions  ----*/

	function getpercentage(x, y, elm) { 
		elm.find('.pbmit-fid-inner').html(y + '/' + x);
		var cal = Math.round((y * 100) / x);
		return cal;
	}

	function pbmit_img_animation() {
		const boxes = gsap.utils.toArray('.pbmit-animation-style1,.pbmit-animation-style2,.pbmit-animation-style3,.pbmit-animation-style4,.pbmit-animation-style5,.pbmit-animation-style6,.pbmit-animation-style7');
		boxes.forEach(img => {
			gsap.to(img, {
				scrollTrigger: {
					trigger: img,
					start: "top 70%",
					end: "bottom bottom",
					toggleClass: "active",
					once: true,
				}
			});
		});
	}

	function pbmit_staticbox_hover() {
		var pbmit_var = jQuery('.pbmit-element-service-style-2, .pbmit-element-static-box-style-1');
		if (!pbmit_var.length) {
			return;
		}
		pbmit_var.each(function() {
			var pbmit_Class = '.swiper-hover-slide-nav .pbmit-hover-inner li, .swiper-static-slide-nav .pbmit-hover-inner li';
			jQuery(this)
				.find(pbmit_Class).first()
				.addClass('pbmit-active');
			jQuery(this)
				.find(pbmit_Class)
				.on('mouseover', function() {
					jQuery(this).addClass('pbmit-active').siblings().removeClass('pbmit-active');
				});
		});
	}

	var pbmit_hover_slide = function() {
		if (typeof Swiper !== 'undefined') {
			var pbmit_hover1 = new Swiper(".pbmit-hover-image", {
				grabCursor: true,
				effect: "slide",
				allowTouchMove: false
			});
			var pbmit_hover2 = new Swiper(".pbmit-short-description", {
				grabCursor: true,
				effect: "creative",
				creativeEffect: {
					prev: {
						translate: [0, "-170%", 1],
					},
					next: {
						translate: [0, "100%", 0],
					},
				},
				allowTouchMove: false
			});
			jQuery('.pbmit-main-hover-slider li').hover(function(e) {
				e.preventDefault();
				var myindex = jQuery(this).index();
				pbmit_hover1.slideTo(myindex, 500, false);
				pbmit_hover2.slideTo(myindex, 500, false);
			});
		}
	}

	function pbmit_title_animation() {
		ScrollTrigger.matchMedia({
			"(min-width: 1025px)": function() {
				var pbmit_var = jQuery('.pbmit-custom-heading, .pbmit-heading-subheading');
				if (!pbmit_var.length) {
					return;
				}
				const quotes = document.querySelectorAll(".pbmit-custom-heading .pbmit-title , .pbmit-heading-subheading .pbmit-title");
				quotes.forEach(quote => {

					//Reset if needed
					if (quote.animation) {
						quote.animation.progress(1).kill();
						quote.split.revert();
					}

					var getclass = quote.closest('.pbmit-custom-heading ,.pbmit-heading-subheading').className;
					var animation = getclass.split('animation-');
					if (animation[1] == "style4") return
				
					quote.split = new SplitText(quote, {
						type: "lines,words,chars",
						linesClass: "split-line"
					});
					gsap.set(quote, { perspective: 400 });
					if (animation[1] == "style1") {
						gsap.set(quote.split.chars, {
							opacity: 0,
							y: "90%",
							rotateX: "-40deg"
						});
					}
					if (animation[1] == "style2") {
						gsap.set(quote.split.chars, {
							opacity: 0,
							x: "50"
						});
					}
					if (animation[1] == "style3") {
						gsap.set(quote.split.chars, {
							opacity: 0,
						});
					}
					quote.animation = gsap.to(quote.split.chars, {
					scrollTrigger: {
						trigger: quote,
						start: "top 90%",
					},
					x: "0",
					y: "0",
					rotateX: "0",
					opacity: 1,
					duration: 1,
					ease: Back.easeOut,
					stagger: .02
					});
				});
			},
		});
	}

	function pbmit_iconbox_hover() {
		var pbmit_var = jQuery('.pbmit-element-miconheading-style-1, .pbmit-element-miconheading-style-6, .pbmit-element-blog-style-3');
		if (!pbmit_var.length) {
			return;
		}
		pbmit_var.each(function() {
			var pbmit_Class = '.pbmit-miconheading-style-1, .pbmit-miconheading-style-6, .pbmit-blog-style-3';
			jQuery(this)
				.find(pbmit_Class).first()
				.addClass('pbmit-active');
			jQuery(this)
				.find(pbmit_Class)
				.on('mouseover', function() {
					jQuery(this).addClass('pbmit-active').siblings().removeClass('pbmit-active');
				});
		});
	}

	var pbmit_staticbox_hover_slide = function() {
		if (typeof Swiper !== 'undefined') {
			var pbmit_hover1 = new Swiper(".pbmit-static-image", {
				grabCursor: true,
				effect: "slide",
				allowTouchMove: false
			});

			jQuery('.pbmit-main-static-slider li').hover(function(e) {
				e.preventDefault();
				var myindex = jQuery(this).index();
				pbmit_hover1.slideTo(myindex, 500, false);
			});
		}
	}

	// on resize
	jQuery(window).on('resize', function(){
		pbmit_title_animation();
		pbmit_img_animation();
	});

	// on load
	jQuery(window).on('load', function(){
		pbmit_img_animation();
		pbmit_title_animation();
		pbmit_staticbox_hover();
		pbmit_hover_slide();
		pbmit_title_animation();
		pbmit_iconbox_hover();
		pbmit_staticbox_hover_slide();
		gsap.delayedCall(1, () =>
			ScrollTrigger.getAll().forEach((t) => {
				t.refresh();
			})
		);	
	});
})($);