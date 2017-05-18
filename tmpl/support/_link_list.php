<div class="col-lg-12 animated fadeInRight">
    <div class="mail-box-header">
        <div class="mail-tools tooltip-demo m-t-md">
            <div class="btn-group pull-right">
                <button class="page-prev btn btn-white btn-sm" data-params="<?php es($pager['prev']); ?>" <?php if(!$pager['prev']) es("disabled");?>><i class="fa fa-arrow-left"></i></button>
                <button class="page-next btn btn-white btn-sm" data-params="<?php es($pager['next']); ?>" <?php if(!$pager['next']) es("disabled");?>><i class="fa fa-arrow-right"></i></button>
            </div>
            <h2><?php es($pager['total']); ?> links found</h2>
        </div>
    </div>
    <div class="mail-box">
        <table class="table table-hover table-mail ">
            <thead>
            <tr>
                <th>Project</th>
                <th>Link No</th>
                <th>Name</th>
                <th>Type</th>
                <th>URLs</th>
                <th>Date</th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            <?php if(!empty($data)): ?>
            <?php foreach ($data as $idx => $row): ?>
            <tr>
                <td><?php es($row['pid']);?> (<?php es($row['project_name']);?>)</td>
                <td><a href="#" class="viewer read" data-id="<?php es($row['id']); ?>"><?php es($row['id']); ?></a></td>
                <td><?php es($row['name']);?></td>
                <td><?php echo MasterData::getLinkType($row['type']); ?></td>
                <td>
                <?php if($row['type'] == 1): ?>
                    <?php es($row['used_urls']);?>/<?php es($row['urls']);?>
                <?php else: ?>
                    <?php es($row['urls']);?>
                <?php endif; ?>
                </td>
                <td><?php es($row['updated_at']);?></td>
                <td>
                    <!--
                    <button type="button" class="partner btn btn-info btn-circle btn-outline" data-id="<?php es($row['pid']); ?>" data-placement="top" title="Partners"><i class="fa fa-users"></i></button>
                    
                    <?php if($row['type'] == 1): ?>
                        <button type="button" class="usefullinks btn btn-primary btn-xs" data-id="<?php es($row['id']); ?>"><i class="fa fa-unlink"></i> Useful Links</button>
                        
                        <button type="button" class="download btn btn-plain btn-xs" data-id="<?php es($row['pid']); ?>"><i class="fa fa-download"></i> Download</button>
                    <?php endif; ?>
                    -->
                </td>
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
