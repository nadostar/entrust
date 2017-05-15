<div class="col-lg-12 animated fadeInRight">
    <div class="mail-box-header">
        <div class="mail-tools tooltip-demo m-t-md">
            <div class="btn-group pull-right">
                <button class="useful-page-prev btn btn-white btn-sm" data-params="<?php es($pager['prev']); ?>" <?php if(!$pager['prev']) es("disabled");?>><i class="fa fa-arrow-left"></i></button>
                <button class="useful-page-next btn btn-white btn-sm" data-params="<?php es($pager['next']); ?>" <?php if(!$pager['next']) es("disabled");?>><i class="fa fa-arrow-right"></i></button>
            </div>
            <h2> Some Useful Multi Links <?php es($pager['total']);?> found</h2>
        </div>
    </div>
    <div class="mail-box">
        <table class="table table-hover table-mail ">
            <thead>
            <tr>
                <th>URL</th>
            </tr>
            </thead>
            <tbody>
            <?php if(!empty($data)): ?>
            <?php foreach ($data as $idx => $row): ?>
            <tr>
                <td><?php es($row['url']);?></td>
            </tr>
            <?php endforeach; ?>
            <?php else: ?>
            <tr>
                <td colspan="2" class="text-center">No found data.</td>
            </tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
