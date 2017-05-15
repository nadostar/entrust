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
        <h2>Survey Management</h2>
        <ol class="breadcrumb">
            <li><a href="/">Home</a></li>
            <li><a>Survey</a></li>
            <li><a>Settings</a></li>
            <li class="active">
                <strong>Link</strong>
            </li>
        </ol>
    </div>
</div>

<div class="wrapper wrapper-content">
    <div id="result" class="row"></div>
    <div id="viewer" class="row"></div>
    <div id="usefullinks-viewer" class="row"></div>
</div>

<form id="search-form">
    <input type="hidden" name="pid" value="<?php es($pid);?>">
</form>

<?php start_form_tag('support/setting_link/?m=download', 'post', 'download_form'); ?>
    <input type="hidden" id="download_id" name="download_id">
<?php end_form_tag(); ?>

<?php include __DIR__ . '/__footer.php'; ?>
</div>

<script src="<?php url('script/entrust.js'); ?>"></script>
<script type="text/javascript">
var urls = {
    'search': "<?php url('support/setting_link/?m=search', true, false); ?>",
    'viewer': "<?php url('support/setting_link/?m=viewer', true, false); ?>",
    'saveChanges': "<?php url('support/setting_link/?m=saveChanges', true, false); ?>",
    'partner': "<?php url('support/setting_partner/?pid=', true, false); ?>",
    'usefulLinks': "<?php url('support/setting_link/?m=usefulLinks', true, false); ?>",
};

var global = new Entrust(urls);

$(function(){
    global.search();

    $('body')
        .on('click', "button.new-form", function(){
            var pid = $(this).data('pid');
            var params = {
                'pid': pid
            }
            $('#viewer').load(urls['viewer'], params, function(response, status, err){

                if(status == "error") {
                    toastr.error(response, status);
                }
            });

            return false;
        })
        .on('click', 'a.viewer', function(){
            var $button = $(this);
            var id = $button.data('id');

            if(!id) {
                toastr.error("Missing ID.");
                return;
            }

            var params = {
                'id': id
            };

            $('#viewer').load(urls['viewer'], params, function(response, status, err){

                if(status == 'error') {
                    toastr.error(response, status);
                }
            });

            return false;
        })
        .on('click', "a.save-form", function(){
            var form = $('form')[1];
            var formData = new FormData(form);

            swal({
              title: "Are you sure?",
              text: "Would you like to save change this data.",
              type: "info",
              showCancelButton: true,
              closeOnConfirm: false,
              showLoaderOnConfirm: true,
            },
            function(){
                $.ajax({
                    async: false,
                    type: "POST",
                    url: urls['saveChanges'],
                    data: formData,
                    cacha: false,
                    contentType: false,
                    processData: false,
                    dataType: "text",
                    success: function(response) {
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
                    },
                    error: function(response, status, err) {
                        console.log(response, status, err);
                        toastr.error(response.responseText, err);
                        swal(err, response.responseText, "error");
                    }
                });
            });

            return false;
        })
        .on('click', "button.partner", function(){
            var $button = $(this);
            var id = $button.data('id');

            if(!id) {
                toastr.error("Missing ID");
                return;
            }
            var url = urls['partner'] + id;

            location.href = url;

            return false;
        })
        .on('click', "button.usefullinks", function(){
            var $button = $(this);
            var id = $button.data('id');

            params = {
                'id': id
            };

            $('#usefullinks-viewer').load(urls['usefulLinks'], params, function(response, status, err){

                if(status == 'error') {
                    toastr.error(response, status);
                }
            });

            return false;
        })
        .on('click', "button.useful-page-prev", function(){
            global.pager2($(this), urls['usefulLinks'], "usefullinks-viewer");
            return false;
        })
        .on('click', "button.useful-page-next", function(){
            global.pager2($(this), urls['usefulLinks'], "usefullinks-viewer");
            return false;
        })
        .on('click', 'a.discard-form', function(){
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