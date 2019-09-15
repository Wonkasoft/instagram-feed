(function( $ ) {
	'use strict';
	
	var xhr = new XMLHttpRequest();

	window.onload = function() 
	{
		if ( document.querySelector( '#_insta_linked_products' ) ) 
		{
			$( '#_insta_linked_products' ).select2();
			$( '#_hashtag_visibility' ).select2();
			$( '.img-visibility' ).select2();
		}
		if ( document.querySelector( '#instagram-thickbox-btn' ) ) 
		{
			document.querySelector( '#instagram-thickbox-btn' ).addEventListener( 'click', function( e ) 
				{
					e.preventDefault();
					var has_tag = e.target.getAttribute( 'data-tag' );
					var has_tag_id = e.target.getAttribute( 'data-tag-id' );
					var already_selected = document.querySelectorAll( '.insta-preview-image' );
					var already_ids_array = []; 
					if ( already_selected.length ) 
					{
						already_selected.forEach( function( el, i ) 
							{
								already_ids_array.push( el.getAttribute( 'data-id' ) );
							});
					}
					var data = {};
					data.action = 'get_instagram_images';
					data.tag = encodeURIComponent( has_tag );
					data.tag_id = has_tag_id;
					data.security = WONKA_INSTAGRAM_AJAX.insta_api_nonce;
					var query_string = Object.keys(data).map(key => key + '=' + data[key]).join('&');
					xhr.onreadystatechange = function() {
					    if (this.readyState == 4 && this.status == 200) {
					     var response = JSON.parse( this.responseText );
					     console.log( response );
					     var image_obj = response.data.image_obj;
					     $( '.profile-info-container' ).empty();
					     var profile_data = '<img src="' + response.data.profile_picture_link + '" class="profile-img"><span class="profile-name">' + response.data.full_name + '</span>';
					     $( '.profile-info-container' ).append( profile_data );
					     var fetched_images = '<div class="fetched-images-container">';
					    	for ( var image in image_obj ) 
					    		{
					    			if ( already_ids_array.length && already_ids_array.includes( image_obj[image].id, 0 ) ) 
					    			{
					    				fetched_images += '<div data-id="' + image_obj[image].id + '" class="img-wrap selected"><img id="' + image_obj[image].id + '" src="' + image_obj[image].images.thumbnail.url + '" /></div>';
					    			}
					    			else
					    			{
					    				fetched_images += '<div data-id="' + image_obj[image].id + '" class="img-wrap"><img id="' + image_obj[image].id + '" src="' + image_obj[image].images.thumbnail.url + '" /></div>';
					    			}
					    		}
					    	fetched_images += '</div>';
					     $('.thickbox-body').empty();
					     $('.thickbox-body').append( fetched_images );
					     $('.thickbox-body-wrap').removeClass('pre-loader');
					    }
					  };
					  xhr.open("GET", ajaxurl + '?' + query_string, true);
					  xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded;");
					  xhr.send();
				});
		}

	  $(document).on("click", ".thickbox-body .img-wrap", function() {
	    $(this).closest(".img-wrap").toggleClass("selected");
	  });

	  $(".import-images").on( "click", function() {

	      var tag = '';
	      var tag_id = '';

        var id_array = [];
        tag = $(".thickbox-body").data('tag');
        tag_id = $(".thickbox-body").data('tag_id');
        var priority = $(".thickbox-body").data( 'priority' );
        var visibility = $(".thickbox-body").data( 'visibility' );
        var status = $(".thickbox-body").data( 'status' );
        $(this).attr('disabled','disabled');

        $(".img-wrap.selected").each( function() {

            var id = $(this).data("id");

            if( id !== undefined ) {

              id_array.push(id);

            }

        });
      	var data = {};
      	data.action = 'import_selected_insta_images';
      	data.tag = encodeURIComponent( tag );
      	data.tag_id = tag_id;
      	data.priority = priority;
      	data.visibility = visibility;
      	data.status = status;
      	data.insta_id = JSON.stringify( id_array );
      	data.security = WONKA_INSTAGRAM_AJAX.insta_api_nonce;
      	var query_string = Object.keys(data).map(key => key + '=' + data[key]).join('&');
        xhr.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
             var response = JSON.parse( this.responseText );
             console.log( response );
             $(".import-images").attr( 'disabled', false );
             document.querySelector( 'div.submitter .button-primary' ).click();
            }
          };
          xhr.open("POST", ajaxurl, true);
          xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded;");
          xhr.send( query_string );
	  });

		if ( document.querySelector( '#get_instagram_access' ) ) 
		{
			var get_btn = document.querySelector( '#get_instagram_access' );
			var access_input = document.querySelector( '#_instafeed_access_token' );
			var msg_span = document.querySelector( '#msg-onclick' );
			var get_new_token = false;
			var msg;
			access_input.parentElement.appendChild( msg_span );
			get_btn.addEventListener( 'click', function( e ) 
				{
					e.preventDefault();
					var redirect = get_btn.getAttribute( 'data-redirect' );

					if ( '' !== access_input.value && false === get_new_token ) 
					{
						msg = 'Your access token seems to be set already. Get new <a id="get_new_token" href="#">access token</a>.';
						msg_span.innerHTML = msg;
						get_new_token = true;
						var get_new_btn = document.querySelector( '#get_new_token' );
						get_new_btn.addEventListener( 'click', function( e ) 
							{
								e.preventDefault();
								get_btn.click();
							});

						return;

					}
					if ( get_btn.getAttribute( 'data-client' ) ) 
					{
						var client = get_btn.getAttribute( 'data-client' );
					}
					else
					{
						msg = 'Your client ID is not set.';
						msg_span.innerText = msg;

						return;
					}
					var url = 'https://www.instagram.com/oauth/authorize/?client_id=' + client + '&redirect_uri=' + redirect + '&response_type=token';
					var w = 600;
					var h = 600;
					var width = window.innerWidth ? window.innerWidth : document.documentElement.clientWidth ? document.documentElement.clientWidth : screen.width; 
					var height = window.innerHeight ? window.innerHeight : document.documentElement.clientHeight ? document.documentElement.clientHeight : screen.height;
					var systemZoom = width / window.screen.availWidth;
					var top = ( height - h ) / 2  / systemZoom;
					var left = ( width - w ) / 2 / systemZoom;
					var get_response_params;
					var access_window = window.open( url, access_window, "width=" + w + ",height=" + h + ",top=" + top + ",left=" + left );
					var window_params = access_window.location.href;
					var checking = setInterval( function() 
						{
							window_params = access_window.location.href;
							if ( window_params.includes( 'access_token' ) ) 
							{
								get_response_params = get_url_params( window_params );
								access_window.close();
								clearInterval( checking );
								access_input.value = get_response_params.access_token;
							}

							if ( window_params.includes( 'error_description' ) ) 
							{
								get_response_params = get_url_params( window_params );
								msg = get_response_params.error_description.replace( /[+]/gi, ' ' ) + '. Get new <a id="need_new_token" href="#">access token</a>.';
								access_window.close();
								clearInterval( checking );
								msg_span.innerText = msg;
								var need_new_btn = document.querySelector( '#need_new_token' );
								need_new_btn.addEventListener( 'click', function( e ) 
									{
										e.preventDefault();
										get_btn.click();
									});
							}
						}, 1000 );
				});
		}
	};

	function get_url_params( url )
	{
		var vars = {};
	  var parts = url.replace(/[?&#]+([^=&]+)=([^&]*)/gi, function(m,key,value) {
	      vars[key] = value;
	  });
	  return vars;
	}

})( jQuery );

