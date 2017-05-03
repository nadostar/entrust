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
        <h2>Log Management</h2>
        <ol class="breadcrumb">
            <li><a href="/">Home</a></li>
            <li><a>Logs</a></li>
            <li class="active">
                <strong>Admin log</strong>
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
                    <label class="control-label" for="admin">Admin ID</label>
                    <input type="text" name="admin_id" value="" placeholder="ID" class="form-control">
                </div>
            </div>
            <div class="col-sm-4">
                <div class="form-group">
                    <label class="control-label" for="category">Category</label>
                    <select name="category" class="input-large form-control" >
                        <option value="">===========</option>
                        <?php foreach (MasterData::getAdminLogCategoryMap() as $k => $v): ?>
                        <option value="<?php echo $k; ?>"><?php echo $v; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-4">
                <div class="form-group">
                    <label class="control-label" for="start_time">Start Date</label>
                    <div class="input-group date">
                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span><input type="text" name="start_time" class="daterange form-control" value="">
                    </div>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="form-group">
                    <label class="control-label" for="end_time">End Date</label>
                    <div class="input-group date">
                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span><input type="text" name="end_time" class="daterange form-control" value="">
                    </div>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="form-group">
                    <label class="control-label" for="limit">Limit</label>
                    <input type="text" name="limit" value="100" placeholder="" class="form-control">
                </div>
            </div>
        </div>
        <div class="row">
            <button type="button" class="search-form btn btn-primary pull-right"><i class="fa fa-search"></i> Search </button>
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
    'search': "<?php url('support/adminLog/?m=search', true, false); ?>",
    'viewer': "<?php url('support/adminLog/?m=viewer', true, false); ?>",
};

var global = new Entrust(urls);

$(function(){
    $('.daterange').daterangepicker({
        autoUpdateInput: false,
        singleDatePicker: true,
        timePicker: true,
        timePickerIncrement: 10,
        timePicker24Hour: true,
        locale: {
            cancelLabel: 'Clear',
            format: 'YYYY-MM-DD HH:mm'
        },
        ranges: {
           'Today': [moment(), moment()],
           'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
           'Last 7 Days': [moment().subtract(6, 'days'), moment()],
           'Last 30 Days': [moment().subtract(29, 'days'), moment()],
           'This Month': [moment().startOf('month'), moment().endOf('month')],
           'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
        }
    });

    $('.daterange').on('apply.daterangepicker', function(ev, picker) {
        $(this).val(picker.startDate.format('YYYY-MM-DD HH:mm'));
    });

    $('.daterange').on('cancel.daterangepicker', function(ev, picker) {
        $(this).val('');
    });

    $('search-form').on('submit', false).find('button').click(function(){
        var $button = $(this);

        var $form = $button.parents('form');
        var params = $form.serializeJSON();

        console.log(urls['search'], params);

        $.post(urls['search'], params)
            .done(function(response){
                console.log(response);
                $('#result').html(response);
            })
            .error(function(response, textStatus, error){
                console.log(response, textStatus, error);
                toastr.error(response.responseText, error);
            });

        return false;
    });

    $('body')
        .on('click', "button.search-form", function(){
            var $form = $('#search-form');
            var params = $form.serializeJSON();

            console.log(urls['search'], params);

            $.post(urls['search'], params)
                .done(function(response){
                    console.log(response);
                    $('#result').html(response);
                })
                .error(function(response, textStatus, error){
                    console.log(response, textStatus, error);
                    toastr.error(response.responseText, error);
                });

            return false;
        })
        .on('click', "tr.viewer", function(){
            var id = $(this).data('id');
            if(!id) {
                toastr.error("missing id");
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