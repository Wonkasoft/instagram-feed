var $wk;

$wk = jQuery.noConflict();

(function ($wk) {

  $wk(document).ready( function () {

    $wk("#_insta_linked_products").select2();
    $wk("#_hashtag_visiblity").select2();
    $wk(".img-visiblity").select2();

    $wk("#instagram-thickbox-btn").on( "click", function() {
      var hasTag = $wk(this).data("tag");
      $wk.ajax({
        url: insta_script.insta_admin_ajax,
        type: 'POST',
        data: "action=get_insta_images&tag=" + hasTag + "&nonce=" + insta_script.insta_api_nonce,
        success: function(result) {
          if (result != 0) {
            $wk('.thickbox-body-wrap').empty();
            $wk('.thickbox-body-wrap').append(result);
            $wk('.thickbox-body-wrap').removeClass('pre-loader');
          }
        },
      });

    });

    $wk(document).on("click", ".thickbox-body img", function(){

      $wk(this).closest(".img-wrap").toggleClass("selected");

    });

    $wk(".import-images").on( "click", function() {

        var selectedImg = $wk(".thickbox-body .img-wrap.selected").length;
        var tag = '';
        if( selectedImg > 0 ) {

            var id_array = [];
            tag = $wk(".thickbox-body").data('tag');
            $wk(this).attr('disabled','disabled');

            $wk(".img-wrap.selected img").each( function() {

                var id = $wk(this).attr("id");

                if( id != undefined ) {

                  id_array.push(id);

                }

            });

            if( id_array.length > 0 && tag ) {

              $wk.ajax({
                url: insta_script.insta_admin_ajax,
                type: 'POST',
                data: "action=import_selected_insta_images" + "&tag="+tag+"&insta_id="+ JSON.stringify(id_array) +"&nonce=" + insta_script.insta_api_nonce,
                success: function(result) {

                  if (result != 0) {

                    location.reload(true);

                  }

                },
              })
            }

        } else {

          alert("please select images to import.");

        }
    });

  });

})($wk)
