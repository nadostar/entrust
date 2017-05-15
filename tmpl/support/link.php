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
            <li class="active">
                <strong>Link</strong>
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
                        <div class="col-lg-3">
                            <button class="new-form btn btn-primary"><i class="fa fa-edit"></i> New Link</button>
                        </div>
                        <form id="search-form">
                        <div class="col-lg-9">
                            <div class="input-group">
                                <select id="search" name="search" class="input-large form-control" >
                                    <option value=""> -- Please choose -- </option>
                                    <?php if(!empty($project_data)): ?>
                                    <?php foreach ($project_data as $idx => $row): ?>
                                    <option value="<?php es($row['id']); ?>"><?php es($row['id']); ?>:<?php es($row['name']); ?></option>
                                    <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
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
    <div id="usefullinks-viewer" class="row"></div>
</div>

<?php include __DIR__ . '/__footer.php'; ?>
</div>

<script src="<?php url('script/entrust.js'); ?>"></script>
<script type="text/javascript">
var urls = {
    'search': "<?php url('support/link/?m=search', true, false); ?>",
    'viewer': "<?php url('support/link/?m=viewer', true, false); ?>",
    'saveChanges': "<?php url('support/link/?m=saveChanges', true, false); ?>",
    'partner': "<?php url('support/partner/?pid=', true, false); ?>",
    'usefulLinks': "<?php url('support/link/?m=usefulLinks', true, false); ?>",
};

var global = new Entrust(urls);

$(function(){

    $('body')
        .on('click', "button.new-form", function() {
            $('#viewer').load(urls['viewer'], function(response, status, err){
                
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
            var form = $('form')[2];
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