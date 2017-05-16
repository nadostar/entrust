<div class="col-lg-12 animated fadeInRight">

	<div class="row">
		<div class="col-lg-12">
			<div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5><i class="fa fa-info-circle"></i> <?php es($data['project']['name']); ?> (<?php es($data['project']['id']); ?>) </h5>
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
                		<table class="table table-hover">
                		<thead>
                		<tr>
			                <th>#</th>
			                <th>Name</th>
			                <th>Country</th>
			                <th>Links</th>
			                <th>Sample</th>
			                <th>Hits</th>
			                <th>C</th>
			                <th>S</th>
			                <th>Q</th>
			                <th>IR</th>
			                <th>Status</th>
			                <th></th>
                		</tr>
                		</thead>
			            <tbody>
			            <?php if(!empty($data['partner'])): ?>
			            <?php foreach ($data['partner'] as $idx => $row): ?>
			            <tr class="viewer-log read" data-partnerid="<?php es($row['id']); ?>" data-pid="<?php es($row['pid']); ?>" data-linkid="<?php es($row['link_id']); ?>">
			                <td><?php es($row['id']); ?></td>
			                <td><?php es($row['name']);?></td>
			                <td><?php echo MasterData::getCountry($row['country']); ?></td>
			                <td><?php es($row['link']);?></td>
			                <td><?php es($row['sample_size']);?></td>
			                <td><?php es($row['hits']);?></td>
			                <td><?php es($row['c']);?></td>
			                <td><?php es($row['s']);?></td>
			                <td><?php es($row['q']);?></td>
			                <td>
			                	<?php if(empty($row['IR'])) : ?>
			                		0%
			                	<?php else: ?>
			                		<?php es($row['IR']); ?>%
			                	<?php endif; ?>
			                </td>
			                <td><?php echo MasterData::getStatus($row['status']); ?></td>
			                <td>
			                	<button type="button" class="export btn btn-primary btn-xs" data-id="<?php es($row['id']); ?>"><i class="fa fa-share"></i> Export</button>
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
			</div>
		</div>
	</div>
	<!--
	<div class="row">
		<div id="ip-viewer" class="col-lg-6"></div>
		<div id="history-viewer" class="col-lg-6"></div>
	</div>
	-->
</div>

<div id="history-viewer" class="col-lg-12"></div>
<div id="ip-viewer" class="col-lg-12"></div>

