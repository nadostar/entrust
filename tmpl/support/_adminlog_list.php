<div class="col-lg-12 animated fadeInRight">
    <div class="mail-box-header">
        <div class="mail-tools tooltip-demo m-t-md">
            <div class="btn-group pull-right">
                <button class="page-prev btn btn-white btn-sm" data-params="<?php es($pager['prev']); ?>" <?php if(!$pager['prev']) es("disabled");?>><i class="fa fa-arrow-left"></i></button>
                <button class="page-next btn btn-white btn-sm" data-params="<?php es($pager['next']); ?>" <?php if(!$pager['next']) es("disabled");?>><i class="fa fa-arrow-right"></i></button>
            </div>
            <h2><?php es($pager['total']); ?> logs found</h2>
        </div>
    </div>

    <div class="mail-box">
        <table class="table table-hover table-mail ">
            <thead>
            <tr>
                <th>Admin ID</th>
                <th>Category</th>
                <th>IP</th>
                <th>Date</th>
            </tr>
            </thead>
            <tbody>
            <?php if(!empty($data)): ?>
            <?php foreach ($data as $idx => $row): ?>
            <tr class="viewer read" data-id="<?php es($row['id']); ?>">
                <td><?php es($row['admin_id']); ?></td>
                <td>
                    <span class="badge badge-plain"><?php echo MasterData::getAdminLogCategory($row['category']); ?></span>
                </td>
                <td><?php es($row['ip_address']); ?></td>
                <td><?php es($row['created_at']); ?></td>
            </tr>
            <?php endforeach; ?>
            <?php else: ?>
            <tr>
                <td colspan="4" class="text-center">No found data.</td>
            </tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
