<div class="col-lg-12 animated fadeInRight">
    <div class="mail-box-header">
        <div class="mail-tools tooltip-demo m-t-md">
            <div class="btn-group pull-right">
                <button class="page-prev btn btn-white btn-sm" data-params="<?php es($pager['prev']); ?>" <?php if(!$pager['prev']) es("disabled");?>><i class="fa fa-arrow-left"></i></button>
                <button class="page-next btn btn-white btn-sm" data-params="<?php es($pager['next']); ?>" <?php if(!$pager['next']) es("disabled");?>><i class="fa fa-arrow-right"></i></button>
            </div>
            <h2> <?php es($pager['total']);?> projects found</h2>
        </div>
    </div>

    <div class="mail-box">
        <table class="table table-hover table-mail ">
            <thead>
            <tr>
                <th>#</th>
                <th>Name</th>
                <th>Country</th>
                <th>Sales</th>
                <th>Sample</th>
                <th>CPI($)</th>
                <th>IR</th>
                <th>Status</th>
                <th>Date</th>
                <th>Settings</th>
            </tr>
            </thead>
            <tbody>
            <?php if(!empty($data)): ?>
            <?php foreach ($data as $idx => $row): ?>
            <tr>
                <td><a href="#" class="viewer read" data-id="<?php es($row['id']); ?>"><?php es($row['id']);?></a></td>
                <td><?php es($row['name']); ?></td>
                <td><?php echo MasterData::getCountry($row['country']); ?></td>
                <td><?php es($row['sales']);?></td>
                <td><?php es($row['sample']);?></td>
                <td><?php es($row['cpi']);?></td>
                <td><?php es($row['ir']);?>%</td>
                <td><?php echo MasterData::getProjectStatus($row['disable']); ?></td>
                <td><?php es($row['updated_at']);?></td>
                <td>
                    <button type="button" class="setting-link btn btn-success btn-xs" data-id="<?php es($row['id']); ?>"><i class="fa fa-link"></i> Link</button>
                    <button type="button" class="setting-partner btn btn-info btn-xs" data-id="<?php es($row['id']); ?>"><i class="fa fa-share-alt"></i> Partner</button>
                </td>
            </tr>
            <?php endforeach; ?>
            <?php else: ?>
            <tr>
                <td colspan="10" class="text-center">No found data.</td>
            </tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

