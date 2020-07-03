(function($){

    if(related_content_object.loading_mode != 'ajax'){
        return;
    }

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
                let list = $('.sg_related_posts_list');
                response.entries.forEach((v, i)=>{
                    let anchor = $(`<a target="_blank"></a>`);
                    anchor.attr('href', v.url);

                    let image = $('<img>');
                    image.attr('alt', v.title);
                    image.attr('src', v.thumbnail);
                    image.attr('srcset', v.srcset);
                    image.appendTo(anchor);
                    anchor.append(`<span>${v.title}</span>`);
                    $('<li>').append(anchor).appendTo(list);

                });
            },
            error: function(xhr){
                console.log(xhr.jsonResponse);
            }
        });
    }

    
    loadPosts();


})(jQuery)