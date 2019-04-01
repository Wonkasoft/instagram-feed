
var $wk = jQuery.noConflict();

(function ($wk) {

    $wk(document).ready( () => {

        $wk(".popup-open").on("click", function() {

            var tag_id = $wk(this).closest('.insta-box').attr('id');
            tag_id = parseInt( tag_id );

            if( tag_id ) {
                var slideTemp = '';
                var sliderHolder = $wk("#sliderHolder");
                var slidertemplate = $wk("#screenSliderTemplate").html();
                slideTemp += slidertemplate.replace( "{{id}}", tag_id);

                $wk(sliderHolder).empty().html(slideTemp);

                $wk.ajax({
                    url: insta_script.insta_admin_ajax,
                    type: 'GET',
                    data: {
                        action : "insta_images_by_tag_id",
                        tag : tag_id,
                        nonce : insta_script.insta_api_nonce,
                    },
                    beforeSend : function() {
                        $wk(document).find(".wkgrid-squeezy .wk-loader").addClass('preload').empty().append("<div class='cp-spinner cp-round'></div>");
                    },
                    success: function(result) {

                      var content = '';

                      if( result.error != undefined )  {

                        if( result.error ) {

                        } else {

                            var data = result.data;

                            if( data.insta_pic != undefined && data.insta_pic.length > 0 ) {

                                $wk.each( data.insta_pic, ( i, val ) => {

                                    content += val.preview;
                                });
                            }

                            if( data.insta_products != undefined && data.insta_products ) {

                                $wk(".info-part .insta-tag-products").empty().html(data.insta_products);

                            }

                            $wk(".insta-modal.slider-wrapper").empty().html(content);
                            $wk(".insta-modal.owl-carousel").owlCarousel({
                                navigation:true,
                                singleItem : true,
                            });

                            $wk(document).find(".wkgrid-squeezy .wk-loader").removeClass('preload').empty();

                        }

                      }

                    }
                })
            }
        });

        $wk(".shop.owl-carousel").owlCarousel({
            navigation:true,
            items : 3,
        });

        $wk(".in-load-more").on("click", function() {

            var tag_id = $wk(this).attr('id');
            var product_id = $wk(this).data('product-id');
            var paged = $wk("input[name='paged']").val();

            paged = parseInt(paged);

            tag_id = parseInt(tag_id);

            if( tag_id && product_id && paged ) {

                $wk.ajax({
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

                            $wk(".instagram-wrap").append(result.message);
                            $wk(".instabox-footer input[name='paged']").val(paged+1);

                            if( result.message == '' ) {

                                $wk(".instabox-footer button").attr("disabled", true);

                            }

                        }

                    }
                })
            }
        });

        $wk(document).on("click", ".close-icon", () => {

            $wk("#sliderHolder").empty();

        });

    });


})($wk)
