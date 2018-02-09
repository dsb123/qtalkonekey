/******/ (function(modules) { // webpackBootstrap
/******/ 	// The module cache
/******/ 	var installedModules = {};

/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {

/******/ 		// Check if module is in cache
/******/ 		if(installedModules[moduleId])
/******/ 			return installedModules[moduleId].exports;

/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = installedModules[moduleId] = {
/******/ 			exports: {},
/******/ 			id: moduleId,
/******/ 			loaded: false
/******/ 		};

/******/ 		// Execute the module function
/******/ 		modules[moduleId].call(module.exports, module, module.exports, __webpack_require__);

/******/ 		// Flag the module as loaded
/******/ 		module.loaded = true;

/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}


/******/ 	// expose the modules object (__webpack_modules__)
/******/ 	__webpack_require__.m = modules;

/******/ 	// expose the module cache
/******/ 	__webpack_require__.c = installedModules;

/******/ 	// __webpack_public_path__
/******/ 	__webpack_require__.p = "//q.qunarzz.com/packeage_qtalk/dev/";

/******/ 	// Load entry module and return exports
/******/ 	return __webpack_require__(0);
/******/ })
/************************************************************************/
/******/ ([
/* 0 */
/***/ function(module, exports, __webpack_require__) {

	module.exports = __webpack_require__(1);


/***/ },
/* 1 */
/***/ function(module, exports) {

	'use strict';

	$(function(){
	    var app = {
	        init: function() {
	            this.index = 0;
	            this.maxPage = 6; //7page
	            this.transformDiscount = 0; //垂直滚动的距离
	            this.scrollFlag = false;
	            this.$body = $('body');
	            this.wrapper = $('#app');
	            this.bodyHeight = this.$body.height();
	            //第一屏和第二屏可能在一起
	            if($('#second_page').height() < this.$body.height()){
	                $('#second_page').removeClass('page');
	                this.maxPage--;
	            }
	            this.$pages = $('.page');
	            this.bindEvent();
	        },
	        /**
	         * @function debounce 基本防抖函数如果一直触发事件，将导致回调函数一直不执行，改进版防抖函数超过一定的时间，必须执行一次
	         * @param  {function} fn    {需要进行防抖操作的函数}
	         * @param  {number} wait    {延迟的时间}
	         * @param  {number} interval    {上一次运行的时间和下一次运行的时间间隔}
	         * @return {function} {进行防抖包装后的函数}
	         */
	        debounce: function(fn, wait, interval) {
	            var timer,
	                previousTime;
	            if (!wait) {
	                wait = 250;
	                interval = 250;
	            } else if (!interval) {
	                interval = 250;
	            }
	            var self = this;
	            return function () {
	                var args = arguments,
	                    now = Date.now();
	                if (!previousTime) {
	                    previousTime = now;
	                }
	                if (now - previousTime >= interval) {
	                    clearTimeout(timer);
	                    fn.bind(self, args)();
	                    previousTime = now;
	                } else {
	                    clearTimeout(timer);
	                    timer = setTimeout(function () {
	                        fn.bind(self, args)();
	                    }, wait);
	                }
	            };
	        },
	        transformY: function($dom, isDown, time, lastPage) {
	            var flag = 0,
	                intervalNum = 40,
	                originHeight = this.transformDiscount,
	                pageHeight = lastPage ? 250 : this.bodyHeight, //250是footer的固定高度
	                self = this;
	            //如果是最后一页网上滚动
	            var transformTime = setInterval(function(){
	                flag++;
	                var nowHieght = isDown ? originHeight + pageHeight*flag / intervalNum : originHeight - pageHeight*flag / intervalNum;
	                $dom.css('transform','translateY(-'+ nowHieght +'px)');
	                if(flag ===  intervalNum) {
	                    self.transformDiscount = nowHieght;
	                    setTimeout(function(){
	                        self.scrollFlag = false;
	                    },800);
	                    clearInterval(transformTime);
	                }
	            }, time/100 );
	        },
	        nextPage: function(){
	            this.index++;
	            if(this.index === this.maxPage) {
	                this.transformY(this.$pages, true, 500, true);
	            } else {
	                this.transformY(this.$pages, true, 500, false);
	            }
	            this.$pages.removeClass('on-view');
	            this.$pages.eq(this.index).addClass('on-view');
	        },
	        prePage: function(){
	            this.index--;
	            if(this.index === this.maxPage - 1 ) {
	                this.transformY(this.$pages, false, 500, true);
	            } else {
	                this.transformY(this.$pages, false, 500, false);
	            }
	            this.$pages.removeClass('on-view');
	            this.$pages.eq(this.index).addClass('on-view');
	        },
	        bindEvent: function() {
	            var self = this;
	            this.$body.on('mousewheel', function(event) {
	                if(self.scrollFlag) {
	                    event.preventDefault();
	                    event.stopPropagation();
	                    return ;
	                }
	                if(event.deltaY<0){
	                    self.index < self.maxPage && (self.scrollFlag = true) && self.nextPage();
	                } else if(event.deltaY>0) {
	                    self.index > 0 && (self.scrollFlag = true) && self.prePage();
	                }
	            });
	            
	            this.$body.on('keydown', function(event) {
	                if(self.scrollFlag) {
	                    return ;
	                }
	                if(event.which === 40) {
	                    self.index < self.maxPage && (self.scrollFlag = true) && self.nextPage();                    
	                } else if(event.which === 38) {
	                    self.index > 0 && (self.scrollFlag = true) && self.prePage();             
	                }
	                    
	            });  
	            //窗口大小改变时
	            $(window).resize(function(){
	                if((self.bodyHeight > 900 && self.$body.height() <= 900)||(self.bodyHeight < 900 && self.$body.height() >= 900)){
	                    window.location.reload();
	                }
	                if(self.bodyHeight !== self.$body.height()) {
	                    self.bodyHeight = self.$body.height();
	                    //250是footer的固定高度
	                    self.transformDiscount = self.index === self.maxPage ? self.bodyHeight * (self.index-1) + 250 : self.bodyHeight * self.index;
	                    self.$pages.css('transform','translateY(-'+ self.transformDiscount +'px)');
	                }
	            });

	            //图片滚动
	            var galleryTop = new Swiper('.js-big-case');
	            var galleryThumbs = new Swiper('.js-small-case', {
	                spaceBetween: 10,
	                slidesPerView: 3,
	                direction: 'vertical',
	                autoplay: {
	                    delay: 2000
	                },
	                navigation: {
	                    nextEl: '.js-next-img',
	                    prevEl: '.js-pre-img',
	                  },
	              });
	              galleryTop.controller.control = galleryThumbs;
	              galleryThumbs.controller.control = galleryTop;
	        }
	    };
	    app.init();
	});



/***/ }
/******/ ]);