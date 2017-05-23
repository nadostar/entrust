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
                <strong>Partner</strong>
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
                            <button class="new-form btn btn-primary"><i class="fa fa-edit"></i> New Partner</button>
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
</div>

<?php include __DIR__ . '/__footer.php'; ?>
</div>

<div id="survey-link-dialog" class="modal fade" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Entrust Survey Links</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="form-group">
                            <label class="col-sm-2 control-label">For Join In: </label>
                            <div id="joinin_url"></div>
                        </div>
                        <div class="hr-line-dashed"></div>
                        <div class="form-group">
                            <label class="col-sm-12 control-label">Redirect Links</label>
                        </div>
                        
                        <div class="form-group">
                            <label class="col-sm-2 control-label">For Complate: </label>
                            <div id="complate_url"></div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label">For Screenout: </label>
                            <div id="screenout_url"></div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label">For Quotafull: </label>
                            <div id="quotafull_url"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="invoice-dialog" class="modal fade" aria-hidden="true">
<div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Invoice Edit</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="form-group"><label>Quantity</label> <input id="inv_quantity" type="text" placeholder="0" class="form-control" value="0"></div>
                    <div class="form-group"><label>Unit Price</label> <input id="inv_price" type="text" placeholder="0.00" class="form-control" value="0.00"></div>
                    <div class="form-group"><label>Other Price</label> <input id="inv_other" type="text" placeholder="0.00" class="form-control" value="0.00"></div>
                    <div class="form-group"><label>Remarks</label> <textarea id="inv_remark" rows="3" class="form-control"></textarea></div>
                    <div><button class="invoice-confirm btn btn-sm btn-primary pull-right m-t-n-xs" type="button"><strong>Confirm</strong></button></div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="<?php url('script/entrust.js'); ?>"></script>
<script type="text/javascript">
var urls = {
    'search': "<?php url('support/partner/?m=search', true, false); ?>",
    'viewer': "<?php url('support/partner/?m=viewer', true, false); ?>",
    'saveChanges': "<?php url('support/partner/?m=saveChanges', true, false); ?>",
    'toggle': "<?php url('support/partner/?m=toggle', true, false); ?>",
    'accesskey': "<?php url('support/partner/?m=accesskey', true, false); ?>",
    'show': "<?php url('support/partner/?m=show', true, false); ?>",
    'ajaxLink': "<?php url('support/partner/?m=ajaxLink', true, false); ?>",
    'invoice': "<?php url('support/invoice/?m=invoice', true, false); ?>",
    'payment': "<?php url('support/invoice/?m=payment', true, false); ?>",
};

var global = new Entrust(urls);

$(function(){

    $('body')
        .on('click', "button.new-form", function(){
            var pid = $(this).data('pid');
            var params = {
                'pid': pid
            };
            $('#viewer').load(urls['viewer'], params, function(response, status, err){

                if(status == "error") {
                    toastr.error(response, status);
                }
            });

            return false;
        })
        .on('click', "a.viewer", function(){
            var $button = $(this);
            var id = $button.data('id');
            var pid = $button.data('pid');

            if(!id) {
                toastr.error("Missing ID.");
                return;
            }

            var params = {
                'id': id,
                'pid': pid
            };

            $('#viewer').load(urls['viewer'], params, function(response, status, err){
                if(status == 'error') {
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
                }).fail(function(response, status, err){
                    console.log(response, status, err);
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
              text: "Would you like to save change this data.",
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
        .on('click', 'button.accesskey', function(){
            var $button = $(this);
            var id = $button.data('id');

            if(!id) {
                toastr.error("Missing ID.");
                return;
            }

            var params = {
                'id': id
            };
            
            swal({
              title: "Are you sure?",
              text: "Generate accesskey.",
              type: "info",
              showCancelButton: true,
              closeOnConfirm: false,
              showLoaderOnConfirm: true,
            },
            function(){
                $.post(urls['accesskey'], params).done(function(response){

                    var result = JSON.parse(response);

                    if(result.status) {
                        toastr.success(result.message);
                        swal("Success!", result.message, "success");

                        $button.toggle();
                        $('#survey-link_' + id).toggle();
                        
                    } else {
                        toastr.error(result.message);
                        swal("Fail!", result.message, "error");

                    }
                }).fail(function(response, status, err){
                    console.log(response, status, err);
                    toastr.error(response.responseText, err);
                    swal(err, result.message, "error");
                });
            });


            return false;
        })
        .on('click', "button.survey-link", function(){
            var $button = $(this);
            var id = $button.data('id');

            if(!id) {
                toastr.error("Missing ID.");
                return;
            }

            var params = {
                'id': id
            };
            
            $.post(urls['show'], params).done(function(response){
            
                var result = JSON.parse(response);
                
                $('#joinin_url').html(result['joinin_url']);
                $('#complate_url').html(result['complate_url']);
                $('#screenout_url').html(result['screenout_url']);
                $('#quotafull_url').html(result['quotafull_url']);
                $('#survey-link-dialog').modal();

            }).fail(function(response, status, err){
                toastr.error(response.responseText, err);
            });

            return false;
        })
        .on('click', "button.invoice", function(){
            var $button = $(this);
            var id = $button.data('id');

            if(!id) {
                toastr.error("Missing ID.");
                return;
            }

            var params = {
                'id': id
            };

            $('#viewer').load(urls['invoice'], params, function(response, status, err){
                if(status == 'error') {
                    toastr.error(response, status);
                }
            });

            return false;
        })
        .on('click', "button.invoice-edit", function(){
            $('#inv_quantity').val('');
            $('#inv_price').val('');
            $('#inv_other').val('');
            $('#inv_remark').val('');
            $('#invoice-dialog').modal();

            return false;
        })
        .on('click', "button.invoice-save", function(){

            var params = $('#invoice-form').serializeJSON();

            swal({
              title: "Are you sure?",
              text: "Make A Invoice",
              type: "info",
              showCancelButton: true,
              closeOnConfirm: false,
              showLoaderOnConfirm: true,
            },
            function(){
                $.post(urls['payment'], params).done(function(response){

                    var result = JSON.parse(response);

                    if(result.status) {
                        toastr.success(result.message);
                        swal("Success!", result.message, "success");                        
                    } else {
                        toastr.error(result.message);
                        swal("Fail!", result.message, "error");

                    }
                }).fail(function(response, status, err){
                    console.log(response, status, err);
                    toastr.error(response.responseText, err);
                    swal(err, result.message, "error");
                });
            });

            return false;
        })
        .on('click', "button.invoice-confirm", function(){
            var quantity = parseInt($('#inv_quantity').val());
            var price = parseFloat($('#inv_price').val());
            var other = parseFloat($('#inv_other').val());
            var remark = $('#inv_remark').val();
            var _total = quantity * price + other;
            
            total = Math.round(_total*100)/100;

            $('#quantity').html(quantity);
            $('#price').html('$' + price);
            $('#other').html('$' + other);

            if(remark != '') {
                $('#remark').show();
                $('#remark').html(remark);  
            } else {
                $('#remark').hide();
                $('#remark').html("");  
            }
            
            $('#total').html('$' + total);

            // invoice-form
            $('#q').val(quantity);
            $('#p').val(price);
            $('#o').val(other);
            $('#r').val(remark);
            
            $('#invoice-dialog').modal('hide');

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