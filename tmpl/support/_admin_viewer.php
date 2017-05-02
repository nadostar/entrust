<div class="col-lg-10 col-lg-offset-1 animated fadeInUp">
    <div class="ibox float-e-margins">
        <div class="ibox-title">
            <h5>New Admin User</h5>
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
                <input type="hidden" name="id" value="<?php es($admin['id']); ?>">
                <div class="form-group">
                    <label class="col-sm-2 control-label">Email *</label>
                    <div class="col-sm-9">
                        <?php if(empty($admin['id'])): ?>
                        <input type="text" name="email" class="form-control" placeholder="Email" value="<?php es($admin['email']); ?>">
                        <?php else: ?>
                        <input type="hidden" name="email" class="form-control" value="<?php es($admin['email']); ?>">
                        <input type="text" class="form-control" value="<?php es($admin['email']); ?>" <?php if(!empty($admin['id'])) es("disabled");?>>
                        <?php endif; ?>

                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">Name *</label>
                    <div class="col-sm-9">
                        <input type="text" name="name" class="form-control" placeholder="Name" value="<?php es($admin['name']); ?>">
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">Password *</label>
                    <div class="col-sm-9">
                        <input type="text" name="password" class="form-control" placeholder="Password" value="dash@default" disabled>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">Permission</label>
                    <div class="col-sm-9">
                      <select name="permission_id" class="input-large form-control" >
                        <?php foreach ($permission_data as $idx => $row): ?>
                        <option value="<?php es($row['id']); ?>" <?php if($row['id'] === $admin['permission_id']) es('selected');?>><?php es($row['name']); ?></option>  
                        <?php endforeach; ?>
                      </select>
                    </div>
                </div>
                <div class="ibox-content text-right">
                    <a href="#" class="delete-form btn btn-danger" data-user="{{admin_user.id|default('new')}}"><i class="fa fa-times"></i> Delete</a>
                    <a href="#" class="save-form btn btn-primary" data-user="{{admin_user.id|default('new')}}" ><i class="fa fa-save"></i> Save Changes</a>
                    <a href="#" class="discard-form btn btn-danger" data-user="{{admin_user.id|default('new')}}"><i class="fa fa-times"></i> Discard</a>
                </div>
            </form>
        </div>
    </div>
</div>
