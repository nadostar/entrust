<div class="col-lg-12 animated fadeInRight">
    <div class="mail-box-header">
        <div class="mail-tools tooltip-demo m-t-md">
            <div class="btn-group pull-right">
                <button class="page-prev btn btn-white btn-sm" data-params="<?php es($pager['prev']); ?>" <?php if(!$pager['prev']) es("disabled");?>><i class="fa fa-arrow-left"></i></button>
                <button class="page-next btn btn-white btn-sm" data-params="<?php es($pager['next']); ?>" <?php if(!$pager['next']) es("disabled");?>><i class="fa fa-arrow-right"></i></button>
            </div>
            <button class="new-form btn btn-primary" data-pid="<?php es($pager['pid']);?>"><i class="fa fa-edit"></i> New Link</button>
        </div>
    </div>
    <div class="mail-box">
        <table class="table table-hover table-mail ">
            <thead>
            <tr>
                <th>#</th>
                <th>Name</th>
                <th>Type</th>
                <th>Date</th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            <?php if(!empty($data)): ?>
            <?php foreach ($data as $idx => $row): ?>
            <tr>
                <td><a href="#" class="viewer read" data-id="<?php es($row['id']); ?>"><?php es($row['id']); ?></a></td>
                <td><?php es($row['name']);?></td>
                <td><?php echo MasterData::getSnapshotSurveyType($row['type']); ?></td>
                <td><?php es($row['updated_at']);?></td>
                <td>
                    <button type="button" class="accesskey btn btn-gray btn-xs" data-id="<?php es($row['id']); ?>" <?php if($row['accesskey'] > 0) es("disabled"); ?>><i class="fa fa-retweet"></i> AccessKey</button>
                    <button type="button" class="download btn btn-gray btn-xs" data-id="<?php es($row['id']); ?>" <?php if($row['accesskey'] == 0) es("disabled"); ?>><i class="fa fa-download"></i> Download</button>
                </td>
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
