<div class="col-lg-12 animated fadeInRight">
    <div class="mail-box-header">
        <div class="mail-tools tooltip-demo m-t-md">
            <div class="btn-group pull-right">
                <button class="page-prev btn btn-white btn-sm" data-params="<?php es($pager['prev']); ?>" <?php if(!$pager['prev']) es("disabled");?>><i class="fa fa-arrow-left"></i></button>
                <button class="page-next btn btn-white btn-sm" data-params="<?php es($pager['next']); ?>" <?php if(!$pager['next']) es("disabled");?>><i class="fa fa-arrow-right"></i></button>
            </div>
            <h2><?php es($pager['total']); ?> survey found</h2>
        </div>
    </div>
    <div class="mail-box">
        <table class="table table-hover table-mail ">
            <thead>
            <tr>
                <th>#</th>
                <th>Project</th>
                <th>Client</th>
                <th>Country</th>
                <th>Sales</th>
                <th>Type</th>
                <th>Sample</th>
                <th>C</th>
                <th>S</th>
                <th>Q</th>
                <th>IR_A</th>
                <th>IR_Q</th>
                <th>Status</th>
                <th>Final($)</th>
            </tr>
            </thead>
            <tbody>
            <?php if(!empty($data)): ?>
            <?php foreach ($data as $idx => $row): ?>
            <tr class="viewer read" data-id="<?php es($row['id']); ?>">
                <td><?php es($row['id']); ?></td>
                <td><?php es($row['name']);?></td>
                <td><?php es($row['client']);?></td>
                <td><?php echo MasterData::getCountry($row['country']); ?></td>
                <td><?php es($row['sales']);?></td>
                <td><?php echo MasterData::getSnapshotSurveyType($row['type']); ?></td>
                <td><?php es($row['sample']);?></td>
                <td><?php es($row['c']);?></td>
                <td><?php es($row['s']);?></td>
                <td><?php es($row['q']);?></td>
                <td>
                <?php es($row['IR_A']);?>%
                </td>
                <td><?php es($row['IR_Q']);?>%</td>
                <td><?php echo MasterData::getProjectStatus($row['status']); ?></td>
                <td>$ <?php es($row['final']);?></td>
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
