<div class="col-lg-12 animated fadeInRight">
    <div class="mail-box-header">
        <div class="mail-tools tooltip-demo m-t-md">
            <div class="btn-group pull-right">
                <button class="page-prev btn btn-white btn-sm" data-params="<?php es($pager['prev']); ?>" <?php if(!$pager['prev']) es("disabled");?>><i class="fa fa-arrow-left"></i></button>
                <button class="page-next btn btn-white btn-sm" data-params="<?php es($pager['next']); ?>" <?php if(!$pager['next']) es("disabled");?>><i class="fa fa-arrow-right"></i></button>
            </div>
            <h2> <?php es($pager['total']); ?> admin found</h2>
        </div>
    </div>

    <div class="mail-box">
        <table class="table table-hover table-mail ">
            <thead>
            <tr>
                <th>#</th>
                <th>Email</th>
                <th>Name</th>
                <th>Permission</th>
                <th>Password</th>
                <th>Date</th>
            </tr>
            </thead>
            <tbody>
            <?php if(!empty($data)): ?>
            <?php foreach ($data as $idx => $row): ?>
            <tr>
                <td><a href="#" class="viewer read" data-id="<?php es($row['admin_id']); ?>"><?php es($row['admin_id']); ?></a></td>
                <td><?php es($row['email']); ?></td>
                <td><?php es($row['name']); ?></td>
                <td><?php es($row['permission_name']); ?></td>
                <td><button type="button" class="init-password btn btn-warning btn-xs" data-id="<?php es($row['admin_id']); ?>">init</button></td>
                <td><?php es($row['updated_at']); ?></td>
            </tr>
            <?php endforeach; ?>
            <?php else: ?>
            <tr>
                <td colspan="6" class="text-center">No found data.</td>
            </tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
