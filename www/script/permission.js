var Permission = function(urls) {
	this.urls = urls;
};

Permission.prototype.search = function() {
    var $form = $('#search-form');
    var params = $form.serializeJSON();

    console.log(urls['search'], params);

    $.post(urls['search'], params)
        .done(function(response){
            console.log(response);
            $('#result').html(response);
        })
        .error(function(response, status, err){
            console.error(response.responseText, status, err);
            toastr.error(response.responseText, err);
        });

    return false;
};

Permission.prototype.viewer = function(that) {
    var $button = $(that);
    var permission_id = $button.data('permission');
    
    console.log("permission_id="+permission_id);

    if(!permission_id) {
        toastr.error("Missing permission id");
        return;
    }

    var params = {
        'id': permission_id
    };

    console.log(this.urls['viewer'], params);

    $('#viewer').load(this.urls['viewer'], params, function(response, status, err){
        console.log('loaded', status);

        if(status == "error") {
            toastr.error(response, status);
        }
    });

    return false;
};