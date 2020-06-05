(function($){


    function loadPosts()
    {
        $.ajax({
            url: related_content_object.ajax_url,
            data: {
                action: 'sg_related_posts',
                post_id: related_content_object.post_id
            },
            dataType: 'json',
            method: 'GET',
            success: function (response) {
                console.log(response)
            },
            error: function(xhr){
                console.log(xhr.jsonResponse);
            }
        });
    }

    
    loadPosts();


})(jQuery)