jQuery(document).ready(function($){
    
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