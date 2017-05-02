<!DOCTYPE html>
<html>
<head>
<?php include __DIR__ . '/__header.php'; ?>
</head>

<body>
<div id="wrapper">
<?php include __DIR__ . '/__navbar.php'; ?>

<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-10">
        <h2>System Management</h2>
        <ol class="breadcrumb">
            <li><a href="/">Home</a></li>
            <li><a>System</a></li>
            <li class="active">
                <strong>Permission</strong>
            </li>
        </ol>
    </div>
</div>

<div class="wrapper wrapper-content animated fadeInUp">
    <div class="row">
        <div class="col-lg-12">
           <div class="ibox">
                <div class="ibox-content">
                    <div class="row">
                        <div class="col-lg-6">
                            <button class="new-form btn btn-primary" data-toggle="tooltip" data-placement="left" title="Refresh inbox"><i class="fa fa-edit"></i> New Permission</button>
                        </div>
                        <form id="search-form">
                        <div class="col-lg-6">
                            <div class="input-group">
                                <input type="text" name="search" placeholder="search" class="form-control"> 
                                <span class="input-group-btn">
                                    <button type="button" class="submit btn btn-primary" onclick="global.search();"> Search</button> 
                                </span>
                            </div>
                        </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="result" class="row"></div>
    <div id="viewer" class="row"></div>
</div>

<?php include __DIR__ . '/__footer.php'; ?>
    </div>

<script src="<?php url('script/entrust.js'); ?>"></script>
<script type="text/javascript">
var urls = {
    'search': "<?php url('support/permission/?m=search', true, false); ?>",
    'viewer': "<?php url('support/permission/?m=viewer', true, false); ?>",
    'saveChanges': "<?php url('support/permission/?m=saveChanges', true, false); ?>",
}; 

var global = new Entrust(urls);

$(function(){

    $('body')
        .on('click', "button.new-form", function(){
            console.log(urls['viewer']);

            $('#viewer').load(urls['viewer'], function(response, status, err){
                console.log('loaded', status);

                if(status == "error") {
                    toastr.error(response, status);
                }
            });

            return false;
        })
        .on('click', "a.viewer", function(){
            var $button = $(this);
            var id = $button.data('id');

            if(!id) {
                toastr.error("missing id.");
                return;
            }
            
            var params = {
                'id': id
            };

            console.log(urls['viewer'], params);

            $('#viewer').load(urls['viewer'], params, function(response, status, err){
                console.log('loaded', status);

                if(status == "error") {
                    toastr.error(response, status);
                }
            });

            return false;
        })
        .on('click', 'a.save-form', function(){
            var $button = $(this);
            var $form = $button.parents('form');
            var params = $form.serializeJSON();

            console.log(urls['saveChanges'], params);

            swal({
              title: "Are you sure?",
              text: "Would you like to save change this data.",
              type: "info",
              showCancelButton: true,
              closeOnConfirm: false,
              showLoaderOnConfirm: true,
            },
            function(){
                $.post(urls['saveChanges'], params).done(function(response){
                    console.log(response);
                    
                    var result = JSON.parse(response);

                    if(result.status) {
                        toastr.success(result.message);
                        swal("Success!", result.message, "success");

                        setTimeout(function(){
                            $('#viewer').empty();
                            global.search();
                        }, 1000);
                    } else {
                        toastr.error(result.message);
                        swal("Fail!", result.message, "error");
                    }
                }).error(function(response, status, err){
                    console.log(response, status, err);
                    toastr.error(response.responseText, err);
                    swal(err, response.responseText, "error");
                });
            });

            return false;
        })
        .on('click', "a.discard-form", function(){
            $('#viewer').empty();
            return false;
        })
        .on('click', "button.page-prev", function(){
            global.pager($(this));
            return false;
        })
        .on('click', "button.page-next", function(){
            global.pager($(this));
            return false;
        });
});

</script>
</body>
</html>