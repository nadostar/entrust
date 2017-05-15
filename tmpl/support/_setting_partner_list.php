<div class="col-lg-12 animated fadeInRight">
    <div class="mail-box-header">
        <div class="mail-tools tooltip-demo m-t-md">
            <div class="btn-group pull-right">
                <button class="page-prev btn btn-white btn-sm" data-params="<?php es($pager['prev']); ?>" <?php if(!$pager['prev']) es("disabled");?>><i class="fa fa-arrow-left"></i></button>
                <button class="page-next btn btn-white btn-sm" data-params="<?php es($pager['next']); ?>" <?php if(!$pager['next']) es("disabled");?>><i class="fa fa-arrow-right"></i></button>
            </div>
            <button class="new-form btn btn-primary" data-toggle="tooltip" data-placement="right" title="new partner" data-pid="<?php es($pager['pid']);?>"><i class="fa fa-edit"></i> New Partner</button>
        </div>
    </div>
    <div class="mail-box">
        <table class="table table-hover table-mail ">
            <thead>
            <tr>
                <th>Project</th>
                <th>#</th>
                <th>Name</th>
                <th>Country</th>
                <th>Related Link</th>
                <th>Max Sample</th>
                <th>Max Hits</th>
                <th>Status</th>
                <th>Date</th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            <?php if(!empty($data)): ?>
            <?php foreach ($data as $idx => $row): ?>
            <tr>
                <td><?php es($row['pid']);?> (<?php es($row['project_name']);?>)</td>
                <td><a href="#" class="viewer read" data-id="<?php es($row['id']); ?>" data-pid="<?php es($row['pid']); ?>"><?php es($row['id']); ?></a></td>
                <td><?php es($row['name']);?></td>
                <td><?php echo MasterData::getCountry($row['country']); ?></td>
                <td><?php es($row['link_name']);?></td>
                <td><?php es($row['sample_size']);?></td>
                <td><?php es($row['hits_limit']);?></td>
                <td><?php echo MasterData::getStatus($row['status']); ?></td>
                <td><?php es($row['updated_at']);?></td>
                <td>
                    <button type="button" class="accesskey btn btn-warning btn-xs" data-id="<?php es($row['id']); ?>" style="<?php if($row['found'] > 0) es("display: none;"); ?>""><i class="fa fa-retweet"></i> AccessKey</button>
                    <button type="button" class="survey-link btn btn-warning btn-xs" data-id="<?php es($row['id']); ?>" style="<?php if($row['found'] == 0) es("display: none;"); ?>""><i class="fa fa-send"></i> Survey Links</button>
                    <button type="button" class="setting-toggle btn btn-plain btn-xs" data-id="<?php es($row['id']); ?>" data-status="<?php es($row['status']); ?>"><?php echo MasterData::getPartnerStatusControl($row['status']); ?></button>
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
