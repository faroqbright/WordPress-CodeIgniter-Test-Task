jQuery(document).ready(function($){
    $("#search_cake_btn").click(function(){
        let c_name = $("#search_cake_name").val()
        // let c_type = $("#search_cake_type").val()


        if(c_name=='' ){
            return false
        }

        //alert("search cake button clicked"+search)
        const formData = new FormData();
        formData.set('action','wp_adv_serach_cakes_ajax');
        formData.set('c_name',c_name);
        // formData.set('c_type',c_type);
        //let nonce_val = "187344422";
        jQuery.ajax( {
            url: myAjax.ajaxurl,
            method: 'POST',
            processData: false,
            contentType: false,
            data: formData
        } ).success( function ( response ) {
             let res = jQuery.parseJSON(response)
            console.log( res )
            if('success' == res.status){
                $(".wp_adv_all_cakes").html("");
                let cakes = res.data.data;
                cakes.forEach(function(item) {
                    if ($(".wp_adv_all_cakes")[0]){
                        
                        let single_cake_html = '<div class="col-12 col-md-4"><div class="card" style="width: 18rem;"><img class="card-img-top" src="https://www.cakes.com.pk/assets/cakes/IMG-20191121-WA0000.jpg" alt="Card image cap"><div class="card-body"><h5 class="card-title">'+item.name+'</h5><h6 class="card-subtitle mb-2 text-muted">Type:'+item.type+'</h6><p class="card-text">Cake description here</p><a href="'+res.data.single_page_url+'?id='+item.id+'" class="btn btn-primary">View Cake ($'+item.price+')</a></div></div></div>';
                        $( ".wp_adv_all_cakes" ).append( single_cake_html );
                    } else {
                        // Do something if class does not exist
                    }
                    
                });
            }if('failure' == res.status){
                let res = jQuery.parseJSON(response)
                console.log( res )
                $('#search_cake_error').text(res.msg)
            }
            //$('#ch_role_success').text(res.message)
            
        } ).error( function( response ) {
            console.log( 'error' );
            let res = jQuery.parseJSON(response)
            console.log( res )
            $('#search_cake_error').text(res.msg)
            
        });
    });
    // $(".wp_adv_cake_buy_now").click(function(){
    //     let cake_id = $(this).attr("data-cake_id ");
        
    // });
    $("#wp_adv_single_page_url_save").click(function(){
        alert("wp_adv_single_page_url_save clicked")
        let single_page_url = $(".wp_adv_single_page_url").val()

        if(single_page_url=='' ){
            return false
        }

        const formData = new FormData();
        formData.set('action','wp_adv_save_single_page_url');
        formData.set('single_page_url',single_page_url);
        jQuery.ajax( {
            url: myAjax.ajaxurl,
            method: 'POST',
            processData: false,
            contentType: false,
            data: formData
        } ).success( function ( response ) {
             let res = jQuery.parseJSON(response)
            console.log( res )
            if('success' == res.status){
                
            }if('failure' == res.status){
                let res = jQuery.parseJSON(response)
                console.log( res )
            }
            
        } ).error( function( response ) {
            console.log( 'error' );
            let res = jQuery.parseJSON(response)
            console.log( res )
            
        });
    });
    

});