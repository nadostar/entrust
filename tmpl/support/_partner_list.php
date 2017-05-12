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
                <th>#</th>
                <th>Name</th>
                <th>Country</th>
                <th>Related Link</th>
                <th>Max Sample</th>
                <th>Max Requset</th>
                <th>Status</th>
                <th>Date</th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            <?php if(!empty($data)): ?>
            <?php foreach ($data as $idx => $row): ?>
            <tr>
                <td><a href="#" class="viewer read" data-id="<?php es($row['id']); ?>" data-pid="<?php es($row['pid']); ?>"><?php es($row['id']); ?></a></td>
                <td><?php es($row['name']);?></td>
                <td><?php echo MasterData::getCountry($row['country']); ?></td>
                <td><?php es($row['link_name']);?></td>
                <td><?php es($row['sample_size']);?></td>
                <td><?php es($row['request_limit']);?></td>
                <td><?php echo MasterData::getStatus($row['status']); ?></td>
                <td><?php es($row['updated_at']);?></td>
                <td>
                
                </td>
            </tr>
            <?php endforeach; ?>
            <?php else: ?>
            <tr>
                <td colspan="9" class="text-center">No found data.</td>
            </tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
