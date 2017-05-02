var Admin = function(urls) {
	this.urls = urls;
};

Admin.prototype.search = function() {
    var $form = $('#search-form');
    var params = $form.serializeJSON();

    console.log(this.urls['search'], params);

    $.post(this.urls['search'], params)
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
