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
                <th>CPI($)</th>
                <th>Sample</th>
                <th>C</th>
                <th>S</th>
                <th>Q</th>
                <th>IR_A</th>
                <th>IR_Q</th>
                <th>Status</th>
                <th>Date</th>
                <th></th>
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
                <td><?php es($row['cpi']);?></td>
                <td><?php es($row['sample']);?></td>
                <td><?php es($row['c']);?></td>
                <td><?php es($row['s']);?></td>
                <td><?php es($row['q']);?></td>
                <td><?php es($row['ir']);?>%</td>
                <td><?php es($row['ir_q']);?>%</td>
                <td><?php echo MasterData::getProjectStatus($row['status']); ?></td>
                <td><?php es($row['updated_at']);?></td>
                <td>
                    <button type="button" class="setting-link btn btn-success btn-circle btn-outline" data-id="<?php es($row['id']); ?>" data-placement="top" title="Links"><i class="fa fa-link"></i></button>
                    <button type="button" class="setting-partner btn btn-warning btn-circle btn-outline" data-id="<?php es($row['id']); ?>" data-placement="top" title="Partners"><i class="fa fa-users"></i></button>
                    <?php if($row['status'] > 0): ?>
                    <button type="button" class="setting-toggle btn btn-primary btn-circle btn-outline" data-id="<?php es($row['id']); ?>" data-status="<?php es($row['status']); ?>"><?php echo MasterData::getProjectStatusControl($row['status']); ?></button>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
            <?php else: ?>
            <tr>
                <td colspan="14" class="text-center">No found data.</td>
            </tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

