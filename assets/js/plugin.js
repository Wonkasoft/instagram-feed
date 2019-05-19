
var $ws = jQuery.noConflict();

(function ($ws) {

    $ws(document).ready( () => {

        $ws(".box-head").on("click", function() {

            var tag_id = $ws(this).closest('.insta-box').attr('data-tag-id');
            tag_id = parseInt( tag_id );

            if( tag_id ) {
                var slideTemp = '';
                var sliderHolder = $ws("#sliderHolder");
                var slidertemplate = $ws("#screenSliderTemplate").html();
                slideTemp += slidertemplate.replace( "{{data-tag-id}}", tag_id);

                $ws(sliderHolder).empty().html(slideTemp);

                $ws.ajax({
                    url: insta_script.insta_admin_ajax,
                    type: 'GET',
                    data: {
                        action : "insta_images_by_tag_id",
                        tag : tag_id,
                        nonce : insta_script.insta_api_nonce,
                    },
                    beforeSend : function() {
                        $ws(document).find(".wsgrid-squeezy .ws-loader").addClass('preload').empty().append("<div class='wonka-spinner wonka-round'></div>");
                    },
                    success: function(result) {

                      var content = '';

                      if( result.error != undefined )  {

                        if( result.error ) {

                        } else {

                            var data = result.data;

                            if( data.insta_pic != undefined && data.insta_pic.length > 0 ) {

                                $ws.each( data.insta_pic, ( i, val ) => {

                                    content += val.preview;
                                });
                            }

                            if( data.insta_products != undefined && data.insta_products ) {

                                $ws(".info-part .insta-tag-products").empty().html(data.insta_products);
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
                                var message_el = $ws( '.wonka-row.wonka-insta-message p' );
                                var message = $ws( '.wonka-row.wonka-insta-message p' ).text();
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
                            $ws(".insta-modal.slider-wrapper").empty().html(content);
                            $ws( '.insta-modal.slider-wrapper' ).slick({
                                slidesToShow: 1,
                                slidesToScroll: 1,
                                adaptiveHeight: false,
                                mobileFirst: true,
                                dots: false,
                                prevArrow: '<button class="slick-prev" type="button"><i class="far fa-arrow-alt-circle-left"></i></button>',
                                nextArrow: '<button class="slick-next" type="button"><i class="far fa-arrow-alt-circle-right"></i></button>',
                            });

                            $ws(document).find(".wsgrid-squeezy .ws-loader").removeClass('preload').empty();

                        }

                      }

                    }
                })
            }
        });

        $ws(".in-load-more").on("click", function() {

            var tag_id = $ws(this).attr('id');
            var product_id = $ws(this).data('product-id');
            var paged = $ws("input[name='paged']").val();

            paged = parseInt(paged);

            tag_id = parseInt(tag_id);

            if( tag_id && product_id && paged ) {

                $ws.ajax({
                    url: insta_script.insta_admin_ajax,
                    type: 'GET',
                    data: {
                        action : "insta_load_more_images",
                        tag_id : tag_id,
                        product_id : product_id,
                        paged : paged,
                        nonce : insta_script.insta_api_nonce,
                    },
                    success: function(result) {

                        if( result && result.error != undefined && result.error == false ) {

                            $ws(".instagram-wrap").append(result.message);
                            $ws(".instabox-footer input[name='paged']").val(paged+1);

                            if( result.message == '' ) {

                                $ws(".instabox-footer button").attr("disabled", true);

                            }

                        }

                    }
                })
            }
        });

        $ws(document).on("click", ".close-icon", () => {

            setTimeout( function() {
                $ws("#sliderHolder").empty();
            }, 500 );

        });

    });


    if ( document.querySelector('div.wonka-insta-row.wonka-insta-message') )
    {

    }

})($ws)
