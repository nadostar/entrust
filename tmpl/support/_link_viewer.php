<div class="col-lg-10 col-lg-offset-1 animated fadeInUp">
    <div class="ibox float-e-margins">
        <div class="ibox-title">
            <h5>New Link</h5>
            <div class="ibox-tools">
                <a class="collapse-link">
                    <i class="fa fa-chevron-up"></i>
                </a>
                <a class="close-link">
                    <i class="fa fa-times"></i>
                </a>
            </div>
        </div>
        <div class="ibox-content">
            <p class="alert alert-warning alert-dismissable">Excluded Officers cannot be obtained from Banquet Hall, Recruit Ticket and Reference. Will be shown as shadow in Officer Manager. </p>
            <form class="form-horizontal" enctype="multipart/form-data" method="post">
                <input type="hidden" name="id" value="<?php es($link['id']); ?>">
                <input type="hidden" name="pid" value="<?php es($link['pid']); ?>">
                <div class="form-group">
                    <label class="col-sm-2 control-label">Link No *</label>
                    <div class="col-sm-9">
                        <input type="text" class="form-control" placeholder="auto generate" value="<?php es($link['id']); ?>" disabled>
                    </div>
                </div>
                <div class="hr-line-dashed"></div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">Project *</label>
                    <div class="col-sm-9">
                    	<select id="pids" name="pids" class="form-control select2" <?php if(!empty($link['pid'])) es("disabled");?>>
                            <option value=""></option>
                            <?php if(!empty($project_data)): ?>
                            <?php foreach ($project_data as $idx => $row): ?>
                            <option value="<?php es($row['id']); ?>" <?php if($link['pid'] == $row['id']) es("selected");?>><?php es($row['id']); ?>:<?php es($row['name']); ?></option>
                            <?php endforeach; ?>
                            <?php endif; ?>
                    	</select>
                    </div>
                </div>
                <div class="hr-line-dashed"></div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">Link Name *</label>
                    <div class="col-sm-9">
                        <input type="text" name="name" class="form-control" placeholder="Link name" value="<?php es($link['name']); ?>">
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">Link Type *</label>
                    <div class="col-sm-9">
                    	<div class="radio">
                    		<input type="radio" name="type" class="i-checks" value="0" <?php if($link['type'] == 0) es("checked");?>> Single&nbsp;
                        	<input type="radio" name="type" class="i-checks" value="1" <?php if($link['type'] == 1) es("checked");?>> Multi
                        </div>
                    </div>
                </div>
                <div class="single form-group" style="<?php if($link['type'] == 1) es("display: none;");?>">
                    <label class="col-sm-2 control-label">URL *</label>
                    <div class="col-sm-9">
                		<textarea class="form-control" id="url" name="url" rows="3" placeholder="Please insert client link here."><?php if($link['type'] == 0) es($link['url']); ?></textarea>
                    </div>
                </div>

                <div class="multi form-group" style="<?php if($link['type'] == 0) es("display: none;");?>">
                    <label class="col-sm-2 control-label">File *</label>
                    <div class="col-sm-9">
						<input type="file" name="attachment">
						<p class="help-block">Example block-level help text here.</p>
                    </div>
                </div>
                <div class="ibox-content text-right">
                    <a href="#" class="delete-form btn btn-danger" data-user="{{admin_user.id|default('new')}}" data-toggle="tooltip" data-placement="top" title="Discard"><i class="fa fa-times"></i> Delete</a>
                    
                    <a href="#" class="save-form btn btn-primary" data-user="{{admin_user.id|default('new')}}" data-toggle="tooltip" data-placement="top" title="Save"><i class="fa fa-save"></i> Save Changes</a>
                    <a href="#" class="discard-form btn btn-danger" data-user="{{admin_user.id|default('new')}}" data-toggle="tooltip" data-placement="top" title="Discard"><i class="fa fa-times"></i> Discard</a>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="<?php echo Env::APP_URL ?>static/js/plugins/select2/select2.full.min.js"></script>
<script type="text/javascript">
$(function(){
    $('.i-checks').iCheck({
        checkboxClass: 'icheckbox_square-green',
        radioClass: 'iradio_square-green',
    });

    $('input').on('ifChecked', function(){
        $('div.single').toggle();
        $('div.multi').toggle();
    });

    $("#pid").select2({
        placeholder: "Please choose a project",
        allowClear: true
    });
});
</script>
