<div class="ibox float-e-margins animated fadeInUp">
    <div class="ibox-title">
        <h5><i class="fa fa-info-circle"></i> Link History</h5>
        <div class="ibox-tools">
            <a class="collapse-link">
                <i class="fa fa-chevron-up"></i>
            </a>
            <a class="close-link">
                <i class="fa fa-times"></i>
            </a>
        </div>
    </div>

    <div class="ibox-content">
    	<div class="table-responsive">
    		<table class="table table-striped">
    		<thead>
    			<tr>
    				<td>AccessKey</td>
    				<td>UID</td>
    				<td>Progress</td>
    				<td>Date</td>
    			</tr>
	            <?php if(!empty($data)): ?>
	            <?php foreach ($data as $idx => $row): ?>
	            <tr>
	            	<td><?php es($row['accesskey']); ?></td>
	                <td><?php es($row['uid']); ?></td>
                    <td><?php echo MasterData::getProgress($row['progress']); ?></td>
	                <td><?php es($row['created_at']);?></td>
	            </tr>
	            <?php endforeach; ?>
	        	<?php endif; ?>
    			<tr>
    				<td colspan="4">
                        <div class="btn-group pull-right">
                            <button class="history-page-prev btn btn-white btn-sm" data-params="<?php es($pager['prev']); ?>" <?php if(!$pager['prev']) es("disabled");?>><i class="fa fa-arrow-left"></i></button>
                            <button class="history-page-next btn btn-white btn-sm" data-params="<?php es($pager['next']); ?>" <?php if(!$pager['next']) es("disabled");?>><i class="fa fa-arrow-right"></i></button>
                        </div>
    				</td>
    			</tr>
    		</thead>
    		</table>
    	</div>
    </div>
</div>