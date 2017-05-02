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
                <th>Project No</th>
                <th>Category</th>
                <th>Code</th>
                <th>Message</th>
                <th>Date</th>
            </tr>
            </thead>
            <tbody>
            <?php if(!empty($data)): ?>
            <?php foreach ($data as $idx => $row): ?>
            <tr class="viewer read" data-id="<?php es($row['id']); ?>">
                <td><?php es($row['pid']); ?></td>
                <td>
                    <span class="badge badge-plain"><?php es($row['kind']); ?></span>
                </td>
                <td><?php es($row['code']); ?></td>
                <td><?php es($row['message']); ?></td>
                <td><?php es($row['created_at']); ?></td>
            </tr>
            <?php endforeach; ?>
            <?php else: ?>
            <tr>
                <td colspan="5" class="text-center">No found data.</td>
            </tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
