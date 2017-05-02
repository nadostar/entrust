<div class="col-lg-10 col-lg-offset-1 animated fadeInUp">
    <div class="ibox float-e-margins">
        <div class="ibox-title">
            <h5>New Permission</h5>
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
                <input type="hidden" name="id" value="<?php es($permission['id']); ?>">
                <div class="form-group">
                    <label class="col-sm-2 control-label">Name *</label>
                    <div class="col-sm-9">
                        <input type="text" name="name" class="form-control" placeholder="Name" value="<?php es($permission['name']); ?>">
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">Description</label>
                    <div class="col-sm-9">
                        <input type="text" name="description" class="form-control" placeholder="Description" value="<?php es($permission['description']); ?>">
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">Roles *</label>
                    <div class="col-sm-9">
                        <table class="table">
                        <thead>
                            <tr>
                                <th>Permission</th>
                                <th>View</th>
                                <th>Manage</th>
                            </tr>
                            <tr><td colspan="3"><b>System Menu<b></td></tr>
                            <?php $menu_array = MenuData::getSystemMenu(); 
                                foreach ($menu_array as $menu):
                            ?>            
                            <tr>
                                <td>&nbsp;&nbsp;&nbsp;<?php es($menu['name']); ?></td>
                                <td><input type="checkbox" name="allow_no_v" value="<?php es($menu['id']); ?>" class="i-checks" <?php if(array_key_exists($menu['id'], $permission['view'])) es($permission['view'][$menu['id']]); ?>></td>
                                <td><input type="checkbox" name="allow_no_m" value="<?php es($menu['id']); ?>" class="i-checks" <?php if(array_key_exists($menu['id'], $permission['manage'])) es($permission['manage'][$menu['id']]); ?>></td>
                            </tr>
                            <?php endforeach; ?>
                            
                            <tr><td colspan="3"><div class="hr-line-dashed"></div></td></tr>

                            <tr><td colspan="3"><b>Survey Menu</b></td></tr>
                            <?php $menu_array = MenuData::getSurveyMenu();
                                foreach ($menu_array as $menu):
                            ?>            
                            <tr>
                                <td>&nbsp;&nbsp;&nbsp;<?php es($menu['name']); ?></td>
                                <td><input type="checkbox" name="allow_no_v" value="<?php es($menu['id']); ?>" class="i-checks" <?php if(array_key_exists($menu['id'], $permission['view'])) es($permission['view'][$menu['id']]); ?>></td>
                                <td><input type="checkbox" name="allow_no_m" value="<?php es($menu['id']); ?>" class="i-checks" <?php if(array_key_exists($menu['id'], $permission['manage'])) es($permission['manage'][$menu['id']]); ?>></td>
                            </tr>
                            <?php endforeach; ?>

                            <tr><td colspan="3"><div class="hr-line-dashed"></div></td></tr>

                            <tr><td colspan="3"><b>Entrust Menu</b></td></tr>
                            <?php $menu_array = MenuData::getEntrustMenu();
                                foreach ($menu_array as $menu):
                            ?>            
                            <tr>
                                <td>&nbsp;&nbsp;&nbsp;<?php es($menu['name']); ?></td>
                                <td><input type="checkbox" name="allow_no_v" value="<?php es($menu['id']); ?>" class="i-checks" <?php if(array_key_exists($menu['id'], $permission['view'])) es($permission['view'][$menu['id']]); ?>></td>
                                <td><input type="checkbox" name="allow_no_m" value="<?php es($menu['id']); ?>" class="i-checks" <?php if(array_key_exists($menu['id'], $permission['manage'])) es($permission['manage'][$menu['id']]); ?>></td>
                            </tr>
                            <?php endforeach; ?>

                            <tr><td colspan="3"><div class="hr-line-dashed"></div></td></tr>

                            <tr><td colspan="3"><b>Logs Menu</b></td></tr>
                            <?php $menu_array = MenuData::getLogMenu();
                                foreach ($menu_array as $menu):
                            ?>            
                            <tr>
                                <td>&nbsp;&nbsp;&nbsp;<?php es($menu['name']); ?></td>
                                <td><input type="checkbox" name="allow_no_v" value="<?php es($menu['id']); ?>" class="i-checks" <?php if(array_key_exists($menu['id'], $permission['view'])) es($permission['view'][$menu['id']]); ?>></td>
                                <td><input type="checkbox" name="allow_no_m" value="<?php es($menu['id']); ?>" class="i-checks" <?php if(array_key_exists($menu['id'], $permission['manage'])) es($permission['manage'][$menu['id']]); ?>></td>
                            </tr>
                            <?php endforeach; ?>
                        </thead>
                        </table>
                    </div>
                </div>
                <div class="ibox-content text-right tooltip-demo">
                    <a href="#" class="delete-form btn btn-danger" data-user="{{admin_user.id|default('new')}}"><i class="fa fa-times"></i> Delete</a>
                    <a href="#" class="save-form btn btn-primary" data-user="{{admin_user.id|default('new')}}"><i class="fa fa-reply"></i> Save Changes</a>
                    <a href="#" class="discard-form btn btn-danger" data-user="{{admin_user.id|default('new')}}"><i class="fa fa-times"></i> Discard</a>
                </div>
            </form>
        </div>
    </div>
</div>

<script type="text/javascript">
$(function() {
    $('.i-checks').iCheck({
        checkboxClass: 'icheckbox_square-green',
        radioClass: 'iradio_square-green',
    });
});
</script>
