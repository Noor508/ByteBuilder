(function($, window, undefined) {

    // Reset tooltip text on mouse out
    $(document).on('mouseout', '.copy_bitly', function() {
        $(this).find('.su_tooltiptext').html("Click to Copy");
    });

    // Copy Bitly link to clipboard
    $(document).on('click', '.copy_bitly', function(event) {
        event.preventDefault();
        var $url = $(this).find('.copy_bitly_link').text();
        var $temp = $("<input>");
        $("body").append($temp);
        $temp.val($url).select();
        document.execCommand("copy");
        $temp.remove();
        $(this).find('.su_tooltiptext').html("Copied: " + $url);
    });

    // Generate Bitly URL via AJAX
    $(document).on('click', '.generate_bitly', function(event) {
        event.preventDefault();
        var $su_generate_button = $(this);
        var su_post_id = $su_generate_button.attr('data-post_id');
        
        if (!su_post_id) {
            $su_generate_button.addClass('generate_bitly_disable');
            return; // No post ID, stop the process
        }

        $.ajax({
            url: suJS.ajaxurl, // Ensure this is defined in your WP environment
            data: {
                action: 'generate_su_bitly_url_via_ajax',
                post_id: su_post_id
            },
            method: 'POST',
            beforeSend: function() {
                $su_generate_button.addClass('generate_bitly_disable');
            },
            success: function(response) {
                if (typeof response === 'string') {
                    var data = JSON.parse(response); // Only parse if response is string
                } else {
                    var data = response; // Response is already an object
                }

                if (data.status) {
                    $su_generate_button.closest('td').html(data.bitly_link_html);
                } else {
                    alert(data.message ? data.message : 'An error occurred');
                }
            },
            error: function(error) {
                alert('An error occurred');
            },
            complete: function() {
                $su_generate_button.removeClass('generate_bitly_disable');
            }
        });
    });

    // Increment share count via AJAX
    $(document).on('click', '.su_share_button', function(event) {
        event.preventDefault();
        var su_post_id = $(this).closest('[data-post_id]').data('post_id');

        if (!su_post_id) return; // No post ID, stop the process

        $.ajax({
            url: suJS.ajaxurl, // Ensure suJS.ajaxurl is defined and points to admin-ajax.php
            data: {
                action: 'su_increment_share_count',
                post_id: su_post_id
            },
            method: 'POST',
            success: function(response) {
                if (response.success) {
                    var share_count = response.data.share_count;
                    $('#share-count-' + su_post_id).text(share_count); // Update share count on the page
                } else {
                    alert(response.data ? response.data : 'An error occurred while updating the share count');
                }
            },
            error: function(error) {
                alert('An error occurred while trying to update the share count');
            }
        });
    });

}(jQuery, window));
