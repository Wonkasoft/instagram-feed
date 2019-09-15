(function( $ ) {
	'use strict';

	$(document).ready( () => {

	    $(".box-head").on("click", function() {

	        var tag_id = $(this).closest('.insta-box').attr('data-tag-id');
	        tag_id = parseInt( tag_id );

	        if( tag_id ) {
	            var slideTemp = '';
	            var sliderHolder = $("#sliderHolder");
	            var slidertemplate = $("#screenSliderTemplate").html();
	            slideTemp += slidertemplate.replace( "{{data-tag-id}}", tag_id);

	            $(sliderHolder).empty().html(slideTemp);

	            $.ajax({
	                url: WONKA_INSTAGRAM_AJAX.insta_admin_ajax,
	                type: 'GET',
	                data: {
	                    action : "insta_images_by_tag_id",
	                    tag : tag_id,
	                    security : WONKA_INSTAGRAM_AJAX.insta_api_nonce,
	                },
	                beforeSend : function() 
	                {
	                    $(document).find(".wsgrid-squeezy .ws-loader").addClass('preload').empty().append("<div class='wonka-spinner wonka-round'></div>");
	                    $( 'body' ).css( { 'overflow': 'hidden' } );
	                },
	                success: function( result ) 
	                {
	                  var content = '';

	                  if( result.error != undefined )  {

	                    if( result.error ) {
	                    	console.log( result.error );
	                    } else {

	                        var data = result.data;
	                        if( data.insta_pic != undefined && data.insta_pic.length > 0 ) {

	                            $.each( data.insta_pic, ( i, val ) => {

	                                content += val.preview;
	                            });
	                        }

	                        if( data.insta_products != undefined && data.insta_products ) {

	                            $(".info-part .insta-tag-products").empty().html(data.insta_products);
	                            if ( document.querySelector( ".info-part" ) ) 
	                            {
	                                document.querySelector( ".info-part" ).classList.add( 'loaded' );
	                            }

	                            if ( document.querySelector( ".slider-part" ) ) 
	                            {
	                                document.querySelector( ".slider-part" ).classList.add( 'loaded' );
	                            }
	                            /*==========================================================
	                            =            adding span tags for the hash tags            =
	                            ==========================================================*/
	                            var message_el = $( '.wonka-row.wonka-insta-message p' );
	                            var message = $( '.wonka-row.wonka-insta-message p' ).text();
	                            var new_message = '';
	                            message = message.split( ' ' );
	                            message.forEach( function( item, i ) 
	                                {
	                                    if ( ~item.indexOf('#') ) 
	                                    {
	                                        new_message += '<span class="wonka-insta-hash-tag">' + item + '</span> ';
	                                    }
	                                    else
	                                    {
	                                        new_message += item + ' ';
	                                    }
	                                });
	                            
	                            message_el.html( new_message );
	                            /*=====  End of adding span tags for the hash tags  ======*/
	                        }
	                        /*===== Slick Slider added to the instagram feed =====*/

	                        $(".insta-modal.slider-wrapper").empty().html(content);
	                        
	                        var item_height;
	                        var insta_item_imgs = $( '.insta-modal.slider-wrapper .item img' );
	                        var insta_screen_temp_wrap = $( '.screen-template-wrap' );

	                        $( '.insta-modal.slider-wrapper' ).slick({
	                            slidesToShow: 1,
	                            slidesToScroll: 1,
	                            adaptiveHeight: false,
	                            mobileFirst: true,
	                            asNavFor: '.wonka-insta-row.wonka-insta-message',
	                            dots: false,
	                            prevArrow: '<button class="slick-prev" type="button"><i class="far fa-arrow-alt-circle-left"></i></button>',
	                            nextArrow: '<button class="slick-next" type="button"><i class="far fa-arrow-alt-circle-right"></i></button>',
	                        });

	                        $( '.wonka-insta-row.wonka-insta-message' ).slick({
	                            slidesToShow: 1,
	                            slidesToScroll: 1,
	                            adaptiveHeight: true,
	                            mobileFirst: true,
	                            asNavFor: '.insta-modal.slider-wrapper',
	                            dots: false,
	                            arrows: false,
	                        });


	                        $( document ).find( ".wsgrid-squeezy .ws-loader" ).removeClass( 'preload' ).empty();

	                    }

	                  }

	                }
	            });
	        }
	    });
	    
	    if ( document.querySelector( '.fetch-more-posts' ) ) 
	    {
	        $(".fetch-more-posts").on("click", function() 
	        {

	            var current_displayed_images = document.querySelectorAll( '.wonka-insta-box' );
	            if ( document.querySelector( '#wonkasoft-instafeed-feed' ) ) 
	            {
	            	var view = document.querySelector( '#wonkasoft-instafeed-feed' ).getAttribute( 'data-view' );
	            }

	            $.ajax(
	            {
	                url: WONKA_INSTAGRAM_AJAX.insta_admin_ajax,
	                type: 'GET',
	                data: 
	                {
	                    action : "insta_load_more_images",
	                    posts_per_page : ( current_displayed_images.length + 10 ),
	                    view : view,
	                    security : WONKA_INSTAGRAM_AJAX.insta_api_nonce
	                },
	                success: function( result ) 
	                {
	                  if ( result.data ) 
	                  {
	                  	console.log( result.data );
	                  	var content = document.querySelector( 'article div.entry-content' );
	                  	content.innerHTML = result.data;
	                  }
	                }
	            });
	        });
	    }

	    $(document).on("click", ".close-icon", () => {

	        setTimeout( function() {
	            $( "#sliderHolder" ).empty();
	            $( 'body' ).css( { 'overflow': 'unset' } );
	        }, 500 );

	    });
	});

})( jQuery );
