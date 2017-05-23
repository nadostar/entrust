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
                <strong>Analytics</strong>
            </li>
        </ol>
    </div>
</div>

<div class="wrapper wrapper-content">
    <div id="result" class="row"></div>
    <div id="viewer" class="row"></div>
</div>

<?php start_form_tag('support/analytics/?m=export', 'post', 'export_form'); ?>
    <input type="hidden" id="export_id" name="export_id">
<?php end_form_tag(); ?>

<?php include __DIR__ . '/__footer.php'; ?>
</div>

<script src="<?php url('script/entrust.js'); ?>"></script>
<script type="text/javascript">
var urls = {
    'search': "<?php url('support/analytics/?m=search', true, false); ?>",
    'viewer': "<?php url('support/analytics/?m=viewer', true, false); ?>",
    'blocklog': "<?php url('support/analytics/?m=blocklog', true, false); ?>",
    'historylog': "<?php url('support/analytics/?m=historylog', true, false); ?>",
};

var global = new Entrust(urls);

$(function(){
    global.search();

    $('body')
        .on('click', "tr.viewer", function(){
            var $button = $(this);
            var id =  $button.data('id');

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
        .on('click', "button.page-prev", function(){
            global.pager($(this));
            return false;
        })
        .on('click', "button.page-next", function(){
            global.pager($(this));
            return false;
        })
        .on('click', "button.viewer-log", function(){
            var $button = $(this);
            var pid = $button.data('pid');

            params = {
                'pid': pid
            };

            $('#ip-viewer').load(urls['blocklog'], params, function(response, status, err){
                console.log('loaded', status);

                if(status == 'error') {
                    toastr.error(response, status);
                }
            });

            return false;
        })
        .on('click', "button.ip-page-prev", function(){
            global.pager2($(this), urls['blocklog'], "ip-viewer");
            return false;
        })
        .on('click', "button.ip-page-next", function(){
            global.pager2($(this), urls['blocklog'], "ip-viewer");
            return false;
        })
        .on('click', "button.viewer-log", function(){
            var $button = $(this);
            var id = $button.data('partnerid');

            params = {
                'id': id
            };

            $('#history-viewer').load(urls['historylog'], params, function(response, status, err){
                console.log('loaded', status);

                if(status == 'error') {
                    toastr.error(response, status);
                }
            });

            return false;
        })
        .on('click', "button.export", function(){
            var $button = $(this);
            var id = $button.data('id');

            if(!id) {
                toastr.error("Missing ID.");
                return;
            }

            document.getElementById('export_id').value = id;
            document.export_form.submit();

            return false;
        })
        .on('click', "button.history-page-prev", function(){
            global.pager2($(this), urls['historylog'], "history-viewer");
            return false;
        })
        .on('click', "button.history-page-next", function(){
            global.pager2($(this), urls['historylog'], "history-viewer");
            return false;
        });
});

/*
function loadlink(){
    $('#links').load('test.php',function () {
         $(this).unwrap();
    });
}

loadlink(); // This will run on page load
setInterval(function(){
    loadlink() // this will run after every 5 seconds
}, 5000);
*/
</script>
</body>
</html>
