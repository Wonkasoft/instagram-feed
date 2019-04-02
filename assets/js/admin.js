var $ws;

$ws = jQuery.noConflict();

(function ($ws) {

  $ws(document).ready( function () {

    $ws("#_insta_linked_products").select2();
    $ws("#_hashtag_visiblity").select2();
    $ws(".img-visiblity").select2();

    $ws("#instagram-thickbox-btn").on( "click", function() {
      var hasTag = $ws(this).data("tag");
      $ws.ajax({
        url: insta_script.insta_admin_ajax,
        type: 'POST',
        data: "action=get_insta_images&tag=" + hasTag + "&nonce=" + insta_script.insta_api_nonce,
        success: function(result) {
          if (result != 0) {
            $ws('.thickbox-body-wrap').empty();
            $ws('.thickbox-body-wrap').append(result);
            $ws('.thickbox-body-wrap').removeClass('pre-loader');
          }
        },
      });

    });

    $ws(document).on("click", ".thickbox-body img", function(){

      $ws(this).closest(".img-wrap").toggleClass("selected");

    });

    $ws(".import-images").on( "click", function() {

        var selectedImg = $ws(".thickbox-body .img-wrap.selected").length;
        var tag = '';
        if( selectedImg > 0 ) {

            var id_array = [];
            tag = $ws(".thickbox-body").data('tag');
            $ws(this).attr('disabled','disabled');

            $ws(".img-wrap.selected img").each( function() {

                var id = $ws(this).attr("id");

                if( id != undefined ) {

                  id_array.push(id);

                }

            });

            if( id_array.length > 0 && tag ) {

              $ws.ajax({
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

})($ws)
