var Entrust = function(urls) {
	this.urls = urls;
}

Entrust.prototype.search = function() {
    var $form = $('#search-form');
    var params = $form.serializeJSON();

    $.post(this.urls['search'], params)
        .done(function(response){
            $('#result').html(response);
        })
        .error(function(response, status, err){
            console.error(response.responseText, status, err);
            toastr.error(response.responseText, err);
        });

    return false;
};

Entrust.prototype.viewer = function(that) {
    var $button = $(that);
    var key = $button.data('key');
    
    if(!key) {
        toastr.error("Missing key");
        return;
    }

    var params = {
        'id': key
    };

    $('#viewer').load(this.urls['viewer'], params, function(response, status, err){
        if(status == "error") {
            toastr.error(response, status);
        }
    });

    return false;
};

Entrust.prototype.pager = function(that) {
    var $button = $(that);
    var params = $button.data('params');
    
    if(params) {
        $.post(urls['search'], params)
            .done(function(response){
                $('#result').html(response);
            })
            .error(function(response, textStatus, error){
                console.log(response, textStatus, error);
                toastr.error(response.responseText, error);
            });
    }
};

Entrust.prototype.pager2 = function(that, url, result) {
    var $button = $(that);
    var params = $button.data('params');
    
    if(params) {
        $.post(url, params)
            .done(function(response){
                $('#' + result).html(response);
            })
            .error(function(response, textStatus, error){
                console.log(response, textStatus, error);
                toastr.error(response.responseText, error);
            });
    }
};