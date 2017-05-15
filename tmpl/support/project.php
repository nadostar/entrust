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
                <strong>Project</strong>
            </li>
        </ol>
    </div>
</div>

<div class="wrapper wrapper-content animated fadeInUp">
    <form id="search-form">
    <div class="ibox-content m-b-sm border-bottom">
        <div class="row">
            <div class="col-sm-4">
                <div class="form-group">
                    <label class="control-label" for="pid">Project No</label>
                    <input type="text" name="pid" value="" placeholder="ex) P100000X" class="form-control">
                </div>
            </div>
            <div class="col-sm-4">
                <div class="form-group">
                    <label class="control-label" for="status">Status</label>
                    <select name="status" class="input-large form-control" >
                        <option value="">===========</option>
                        <?php foreach (MasterData::getProjectSearchStatusMap() as $k => $v): ?>
                        <option value="<?php echo $k; ?>"><?php echo $v; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="form-group">
                    <label class="control-label" for="sales">Sales</label>
                    <input type="text" name="sales" value="" placeholder="ex) Eva" class="form-control">
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-3">
                <button class="new-form btn btn-primary"><i class="fa fa-edit"></i> New Porject</button>
            </div>
            <div class="col-lg-9">
                <button type="button" class="search-form btn btn-primary pull-right" onclick="global.search();"><i class="fa fa-search"></i> Search </button>
            </div>
        </div>
    </div>
    </form>

    <div id="result" class="row"></div>
    <div id="viewer" class="row"></div>
</div>

<?php include __DIR__ . '/__footer.php'; ?>
</div>

<script src="<?php url('script/entrust.js'); ?>"></script>
<script type="text/javascript">
var urls = {
    'search': "<?php url('support/project/?m=search', true, false); ?>",
    'viewer': "<?php url('support/project/?m=viewer', true, false); ?>",
    'saveChanges': "<?php url('support/project/?m=saveChanges', true, false); ?>",
    'toggle': "<?php url('support/project/?m=toggle', true, false); ?>",
    'link': "<?php url('support/setting_link/?pid=', true, false); ?>",
    'partner': "<?php url('support/setting_partner/?pid=', true, false); ?>",
};

var global = new Entrust(urls);

$(function(){

    $('body')
        .on('click', "button.new-form", function(){
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
                toastr.error("Missing ID");
                return;
            }
            
            var params = {
                'id': id
            };

            $('#viewer').load(urls['viewer'], params, function(response, status, err){
                if(status == "error") {
                    toastr.error(response, status);
                }
            });

            return false;
        })
        .on('click', "a.save-form", function(){
            var $button = $(this);
            var $form = $button.parents('form');
            var params = $form.serializeJSON();

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
                    var result = JSON.parse(response);

                    if(result.status){
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
                }).fail(function(response, status, err){
                    toastr.error(response.responseText, err);
                    swal(err, response.responseText, "error");
                });
            });

            return false;
        })
        .on('click', "button.setting-toggle", function(){
            var $button = $(this);
            var id = $button.data('id');
            var status = $button.data('status');

            if(!id) {
                toastr.error("Missing ID");
                return;
            }

            var params = {
                'id': id,
                'status': status
            };
            
            swal({
              title: "Are you sure?",
              text: "Would you like to save change the status.",
              type: "info",
              showCancelButton: true,
              closeOnConfirm: false,
              showLoaderOnConfirm: true,
            },
            function(){
                $.post(urls['toggle'], params).done(function(response){
                    var result = JSON.parse(response);

                    if(result.status){
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
                }).fail(function(response, status, err){
                    toastr.error(response.responseText, err);
                    swal(err, response.responseText, "error");
                });
            });

            return false;
        })
        .on('click', "button.setting-link", function(){
            var $button = $(this);
            var id = $button.data('id');

            if(!id) {
                toastr.error("Missing ID");
                return;
            }

            var url = urls['link'] + id;

            location.href = url;

            return false;
        })
        .on('click', "button.setting-partner", function(){
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