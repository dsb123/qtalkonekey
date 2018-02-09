// 'use strict';

$(function(){
    var app = {
        init: function() {
            this.index = 0;
            this.maxPage = $('.page').length -1; //6page
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
        transformY: function($dom, isDown, time) {
            var flag = 0,
                intervalNum = 10,
                originHeight = this.transformDiscount,
                pageHeight = this.bodyHeight, //250是footer的固定高度
                self = this;
            //如果是最后一页网上滚动
            var transformTime = setInterval(function(){
                flag++;
                var nowHieght = isDown ? originHeight + pageHeight*flag / intervalNum : originHeight - pageHeight*flag / intervalNum;
                $dom.css({
                    '-webkit-transform':'translateY(-'+ nowHieght +'px)',
                    '-os-transform':'translateY(-'+ nowHieght +'px)',
                    '-moz-transform':'translateY(-'+ nowHieght +'px)',
                    'transform':'translateY(-'+ nowHieght +'px)'
                });
                if(flag ===  intervalNum) {
                    self.transformDiscount = nowHieght;
                    setTimeout(function(){
                        self.scrollFlag = false;
                    },800);
                    clearInterval(transformTime);
                }
            }, time/ intervalNum );
        },
        nextPage: function(){
            this.index++;
            this.transformY(this.$pages, true, 300);
            this.$pages.removeClass('on-view');
            this.$pages.eq(this.index).addClass('on-view');
        },
        prePage: function(){
            this.index--;
            this.transformY(this.$pages, false, 300);
            this.$pages.removeClass('on-view');
            this.$pages.eq(this.index).addClass('on-view');
        },
        bindEvent: function() {
            var self = this;
            this.$body.on('mousewheel', function(event) {
                event.preventDefault();
                event.stopPropagation();
                if(self.scrollFlag) {
                    return ;
                }
                if(event.deltaY<0){
                    self.index < self.maxPage && (self.scrollFlag = true) && self.nextPage();
                } else if(event.deltaY>0) {
                    self.index > 0 && (self.scrollFlag = true) && self.prePage();
                }
                
            });
            
            this.$body.on('keydown', function(event) {
                if (event.target.tagName.toLocaleLowerCase()=='input'){
                    return;
                }
                if(self.scrollFlag) {
                    return ;
                }
                if(event.which === 40) {
                    event.preventDefault();
                    event.stopPropagation();
                    self.index < self.maxPage && (self.scrollFlag = true) && self.nextPage();
                } else if(event.which === 38) {
                    event.preventDefault();
                    event.stopPropagation();
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
            // var galleryTop = new Swiper('.js-big-case');
            // var galleryThumbs = new Swiper('.js-small-case', {
            //     spaceBetween: 10,
            //     slidesPerView: 3,
            //     direction: 'vertical',
            //     autoplay: {
            //         delay: 2000
            //     },
            //     navigation: {
            //         nextEl: '.js-next-img',
            //         prevEl: '.js-pre-img',
            //       }
            //   });
            //   galleryTop.controller.control = galleryThumbs;
            //   galleryThumbs.controller.control = galleryTop;
        }
    };
    app.init();
});

