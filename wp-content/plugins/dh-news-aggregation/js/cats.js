/* 
    Loads and manages the display of the post data on the categories page.
    Each category has a list of posts. New posts are added to this list on request.
    Posts are added in 4 post chunks
    The list position is changed to display the newest posts added.
*/

function Posts() {

    this.getCount = function () {
        return 4;
    }

    /* Builds and adds an unordered list of posts. Also defines both the width and position of list. */
    this.buildHolder = function() {

        var ul = jQuery('<ul />');

        var c = 1;
        var cnt = this.getCount();

        while (c <= cnt) {
            var li = jQuery('<li class="post" data-type="post_'+c+'"></li>');
            li.appendTo(ul);
            c=c+1;
        }

        return ul;
    }

    this.buildPosts = function(data,list) {
        var lis = list.children('li.post');

        jQuery.each(lis,function(i,l){

            ps = new Posts();

            if (data.posts[i]) {

                l.innerHTML = ps.buildPost(data.posts[i]);

            }
            else
            {
                l.innerHTML = "";
            }

        }); 
    }

    this.buildPost = function(postData) {
        
        var post = '<div><a href="'+postData.guid+'#comments" title="'+postData.comment_count+' Comment(s)" class="hidden-print">'+postData.comment_count+'</a>'
            +'<img src="'+postData.image[0]+'" alt="Image for: '+postData.post_title+'" /></div>'
            +'<a href="'+postData.guid+'" title="'+postData.post_title+'">'+postData.post_title+'</a>'
            +'<small>'+postData.post_date+'</small>';

        return post;
    }

    /* Add the posts for the first time */ 
	this.create = function(result,domObj) {
		
        if (result.count>0) {

            postList = this.buildHolder();

            /* If there are more posts that can be retrieved add a view more button */
            var cnt = this.getCount();

            if (parseInt(result.post_count) > result.offset) {

                var lessOffset = result.offset-cnt;

                var buttons = jQuery('<li class="buttons" />');
                var more = jQuery('<a href="" title="View Older Posts" class="more" data-offset="'+result.offset+'" data-cat="'+result.category+'">&nbsp;</a>'); 
                more.appendTo(buttons);

                var less = jQuery('<a href="" title="View Newer Posts" class="less off" data-offset="'+lessOffset+'" data-cat="'+result.category+'">&nbsp;</a>'); 
                less.appendTo(buttons);

                buttons.appendTo(postList);

            }

            postList.insertAfter(domObj.parent());

            this.buildPosts(result,postList);

        }
        else {
            domObj.parent().parent().hide();
        }
	}

    /* Update the posts */
    this.update = function(result,domObj) {

        var cnt = this.getCount();

        var parent = domObj.parent();

        /* Retrieve current width and position of the list */
        //if (result.count>0) {

            this.buildPosts(result,domObj.parent().parent());

            var lessOffset = result.offset - (cnt*2);

            parent.children('.less').attr('data-offset',lessOffset);
            parent.children('.more').attr('data-offset',result.offset);

            if (result.offset>cnt) {
                parent.children('.less').removeClass('off');
                parent.children('.less').addClass('on');
            }

            if (result.offset>=parseInt(result.post_count)) {
                parent.children('.more').removeClass('on');
                parent.children('.more').addClass('off');
            }

            if (result.offset<parseInt(result.post_count)) {
                parent.children('.more').removeClass('off');
                parent.children('.more').addClass('on');
            }

            if (result.offset==cnt) {
                parent.children('.less').removeClass('on');
                parent.children('.less').addClass('off');
            }


        //}
    }

}

function Server() {
     
    this.call = function(type,response,url,data,route,vars)
    {


        jQuery.ajax({
            type: type,
            url: url,
            dataType: response,
            data: data,
            cache: false,
            statusCode: {
                404: function () {
                    alert('Page not found!');
                }
            },
            success: function (result) {
 
                /* Routes the successful request */
                var cm = route.split('_');
                var c = cm[0];
                var m = cm[1];
                 
                var myclass = new window[c]();
                 
                myclass[m](result,vars);  
            },
            complete: function (xhr, textStatus) {
                //
                
            },
            error: function(e){
                //
            }
             
        });
    }
 
}


/* Initiate jQuery */
jQuery(document).ready(function(){

    /* Load the routing class for Ajax */
	var srv = new Server();

    /* Foreach heading that appears on the categories page load in the relevant posts via ajax */
	jQuery.each(jQuery('div.dhCats div h2 a'),function(i,c){

        var ps = new Posts();
        var cnt = ps.getCount();

        /*
            The data variable passed to WordPress takes a three variables
            action: which WordPress action to call, see plugin
            catID: the category id for the posts to be retrieved
            offset: the data offset. The limit variable in MySQL
        */
        var data = {'catID':jQuery(this).attr('data-cat'),'offset':0,'count':cnt};
        
        /* 
            This ajax method takes several variables the first four are standard ajax request options
            The last two tell the method where to route the request to after it is successful. 
            This takes the form of Class_Method
            The final option allows you to pass any extra variables you want to the the route
        */
        srv.call('POST','json','/wp-admin/admin-ajax.php',data,'Posts_create',jQuery(this));     

    });
	
    /* Bind click event to the more posts buttons */
    jQuery(document).on('click','.more',function(e){

        e.preventDefault();

        var ps = new Posts();
        var cnt = ps.getCount();

        if (!jQuery(this).hasClass('off')) {

            var data = {'catID':jQuery(this).attr('data-cat'),'offset':jQuery(this).attr('data-offset'),'count':cnt};

            srv.call('POST','json','/wp-admin/admin-ajax.php',data,'Posts_update',jQuery(this));

        }

    });

    jQuery(document).on('click','.less',function(e){

        e.preventDefault();

        var ps = new Posts();
        var cnt = ps.getCount();

        if (!jQuery(this).hasClass('off')) {

            var data = {'catID':jQuery(this).attr('data-cat'),'offset':jQuery(this).attr('data-offset'),'count':cnt};

            srv.call('POST','json','/wp-admin/admin-ajax.php',data,'Posts_update',jQuery(this));

        }

    });

});