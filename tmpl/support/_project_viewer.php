<div class="col-lg-10 col-lg-offset-1 animated fadeInUp">
    <div class="ibox float-e-margins">
        <div class="ibox-title">
            <h5>New Project</h5>
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
            <form class="form-horizontal" method="post">
                <input type="hidden" name="id" value="<?php es($project['id']); ?>">
                <div class="form-group">
                    <label class="col-sm-2 control-label">Project No *</label>
                    <div class="col-sm-9">
                        <input type="text" class="form-control" placeholder="auto generate" value="<?php es($project['id']); ?>" disabled>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">Project Name *</label>
                    <div class="col-sm-9">
                        <input type="text" name="name" class="form-control" placeholder="Project name" value="<?php es($project['name']); ?>">
                    </div>
                </div>
                <div class="hr-line-dashed"></div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">Client *</label>
                    <div class="col-sm-9">
                        <input type="text" name="client" class="form-control" placeholder="Client name" value="<?php es($project['client']); ?>">
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">Country *</label>
                    <div class="col-sm-3">
                        <select id="country" name="country" class="form-control select2" >
                            <option value=""></option>
                            <?php foreach (MasterData::getCountryMap() as $k => $v): ?>
                            <option value="<?php echo $k; ?>" <?php if($project['country'] == $k) es("selected");?>><?php echo $v; ?></option>
                            <?php endforeach; ?>
                            
                        </select>
                    </div>

                    <label class="col-sm-3 control-label">Sales *</label>
                    <div class="col-sm-3">
                        <input type="text" name="sales" class="form-control" placeholder="Sales" value="<?php es($project['sales']); ?>">
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-2 control-label">Survey Type</label>
                    <div class="col-sm-3">
                        <select name="type" class="form-control" >
                            <?php foreach (MasterData::getSurveyTypeMap() as $k => $v): ?>
                            <option value="<?php echo $k; ?>" <?php if($project['type'] == $k) es("selected");?>><?php echo $v; ?></option>
                            <?php endforeach; ?>
                            <!--
                            <option value="0" <?php if($project['type'] == 0) es("selected");?>>Sample Only</option>
                            <option value="1" <?php if($project['type'] == 1) es("selected");?>>Full Service</option>
                            <option value="2" <?php if($project['type'] == 2) es("selected");?>>Healthcare</option>
                            -->
                        </select>
                    </div>

                    <label class="col-sm-3 control-label">IP Access *</label>
                    <div class="col-sm-3">
                        <select name="ip_access" class="form-control" >
                            <option value="0" <?php if($project['ip_access'] == 0) es("selected");?>>N</option>
                            <option value="1" <?php if($project['ip_access'] == 1) es("selected");?>>Y</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-2 control-label">IR</label>
                    <div class="col-sm-3">
                        <div class="input-group">
                            <input type="text" name="ir" class="form-control" placeholder="0" value="<?php es($project['ir']);?>">
                            <span class="input-group-addon">%</span>
                        </div>
                    </div>

                    <label class="col-sm-3 control-label">CPI($)</label>
                    <div class="col-sm-3">
                        <input type="text" name="cpi" class="form-control" placeholder="0" value="<?php es($project['cpi']);?>">
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-2 control-label">Sample N</label>
                    <div class="col-sm-3">
                        <input type="text" class="form-control" name="sample" placeholder="0" value="<?php es($project['sample']);?>">
                    </div>

                    <label class="col-sm-3 control-label">Other free</label>
                    <div class="col-sm-3">
                        <input type="text" class="form-control" name="free" placeholder="0" value="<?php es($project['free']);?>">
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-2 control-label">Start Date</label>
                    <div class="col-sm-3">
                        <div class="input-group">
                            <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                            <input type="text" name="start_at" class="form-control" data-inputmask="'alias': 'yyyy/mm/dd'" data-mask value="<?php es($project['start_at']);?>">
                        </div>
                    </div>

                    <label class="col-sm-3 control-label">End Date</label>
                    <div class="col-sm-3">
                        <div class="input-group">
                            <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                            <input type="text" name="end_at" class="form-control" data-inputmask="'alias': 'yyyy/mm/dd'" data-mask value="<?php es($project['end_at']);?>">
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-2 control-label">Invoice</label>
                    <div class="col-sm-3">
                        <select name="invoice" class="form-control" >
                            <option value="0" <?php if($project['invoice'] == 0) es("selected");?>>N</option>
                            <option value="1" <?php if($project['invoice'] == 1) es("selected");?>>Y</option>
                        </select>
                    </div>

                    <label class="col-sm-3 control-label">Payment</label>
                    <div class="col-sm-3">
                        <select name="payment" class="form-control" >
                            <option value="0" <?php if($project['payment'] == 0) es("selected");?>>N</option>
                            <option value="1" <?php if($project['payment'] == 1) es("selected");?>>Y</option>
                        </select>
                    </div>
                </div>

                <div class="ibox-content text-right">
                    <a href="#" class="delete-form btn btn-danger" data-row="{{admin_user.id|default('new')}}"><i class="fa fa-times"></i> Delete</a>
                    <a href="#" class="save-form btn btn-primary" data-row="{{admin_user.id|default('new')}}"><i class="fa fa-reply"></i> Save Changes</a>
                    <a href="#" class="discard-form btn btn-danger" data-row="{{admin_user.id|default('new')}}"><i class="fa fa-times"></i> Discard</a> 
                </div>
            </form>
        </div>
    </div>
</div>

<script src="<?php echo Env::APP_URL ?>static/js/plugins/select2/select2.full.min.js"></script>
<script src="<?php echo Env::APP_URL ?>static/js/plugins/input-mask/jquery.inputmask.js"></script>
<script src="<?php echo Env::APP_URL ?>static/js/plugins/input-mask/jquery.inputmask.date.extensions.js"></script>
<script src="<?php echo Env::APP_URL ?>static/js/plugins/input-mask/jquery.inputmask.extensions.js"></script>
<script type="text/javascript">

$(function(){
    $("#country").select2({
        placeholder: "Select a country",
        allowClear: true
    });

    $("#datemask").inputmask("yyyy/mm/dd", {"placeholder": "yyyy/mm/dd"});
    $("[data-mask]").inputmask();
});

</script>