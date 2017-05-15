<div class="col-lg-10 col-lg-offset-1 animated fadeInUp">
    <div class="ibox float-e-margins">
        <div class="ibox-title">
            <h5>New Partner</h5>
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
                <input type="hidden" name="id" value="<?php es($partner['id']);?>">
                <input type="hidden" name="pid" value="<?php es($partner['pid']);?>">
                <div class="form-group">
                    <label class="col-sm-2 control-label">Projects *</label>
                    <div class="col-sm-9">
                        <select id="pids" name="pids" class="form-control select2" <?php if(!empty($partner['pid'])) es("disabled");?>>
                            <option value=""></option>
                            <?php if(!empty($project_data)): ?>
                            <?php foreach ($project_data as $idx => $row): ?>
                            <option value="<?php es($row['id']); ?>" <?php if($partner['pid'] == $row['id']) es("selected");?>><?php es($row['id']); ?>:<?php es($row['name']); ?></option>
                            <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                </div>
               <div class="form-group">
                    <label class="col-sm-2 control-label">Links *</label>
                    <div class="col-sm-9">
                        <select id="link_id" name="link_id" class="form-control" placeholder="Please choose link.">
                            <?php if(!empty($link_data)): ?>
                            <?php foreach ($link_data as $idx => $row): ?>
                            <option value="<?php es($row['id']); ?>" <?php if($partner['link_id'] == $row['id']) es("selected");?>><?php es($row['id']); ?>:<?php es($row['name']); ?></option>
                            <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                </div>
                <div class="hr-line-dashed"></div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">Partner No *</label>
                    <div class="col-sm-9">
                        <input type="text" class="form-control" placeholder="auto generate" value="<?php es($partner['id']);?>" disabled>
                    </div>
                </div>
                <div class="hr-line-dashed"></div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">Partner Name *</label>
                    <div class="col-sm-9">
                        <input type="text" name="name" class="form-control" placeholder="Partner name" value="<?php es($partner['name']);?>">
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">Partner Country *</label>
                    <div class="col-sm-9">
                        <select id="country" name="country" class="form-control select2" >
                            <option value=""></option>
                            <?php foreach (MasterData::getCountryMap() as $k => $v): ?>
                            <option value="<?php echo $k; ?>" <?php if($partner['country'] == $k) es("selected");?>><?php echo $v; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="hr-line-dashed"></div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">For Complate *</label>
                    <div class="col-sm-9">
                		<input type="text" name="complate" class="form-control" value="<?php es($partner['complate_url']);?>" placeholder="Please insert complate link here.">
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">For Screen out *</label>
                    <div class="col-sm-9">
                        <input type="text" name="screenout" class="form-control" value="<?php es($partner['screenout_url']);?>" placeholder="Please insert screen out link here.">
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">For Quota full *</label>
                    <div class="col-sm-9">
                        <input type="text" name="quotafull" class="form-control" value="<?php es($partner['quotafull_url']);?>" placeholder="Please insert quota full link here.">
                    </div>
                </div>
                <div class="hr-line-dashed"></div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">Sample size *</label>
                    <div class="col-sm-2">
                        <input type="text" name="sample_size" class="form-control" value="<?php es($partner['sample_size']);?>" placeholder="0"> 
                    </div>
                    <label class="control-label"><?php es($partner['hits_comment']);?></label>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">Hits limit *</label>
                    <div class="col-sm-2">
                        <input type="text" name="hits_limit" class="form-control" value="<?php es($partner['hits_limit']);?>" placeholder="0">
                    </div>
                </div>
                <div class="ibox-content text-right">
                    <a href="#" class="delete-form btn btn-danger" data-user="{{admin_user.id|default('new')}}"><i class="fa fa-times"></i> Delete</a>
                    <a href="#" class="save-form btn btn-primary" data-user="{{admin_user.id|default('new')}}"><i class="fa fa-save"></i> Save Changes</a>
                    <a href="#" class="discard-form btn btn-danger" data-user="{{admin_user.id|default('new')}}"><i class="fa fa-times"></i> Discard</a>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="<?php echo Env::APP_URL ?>static/js/plugins/select2/select2.full.min.js"></script>
<script type="text/javascript">
$(function(){
    $("#pids").select2({
        placeholder: "Select a project",
        allowClear: true,
    });

    $("#country").select2({
        placeholder: "Select a country",
        allowClear: true
    });
});
</script>