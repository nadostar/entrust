<div class="col-lg-12 animated fadeInRight">
	<div class="ibox-title">
		<h2><i class="fa fa-info"></i> <?php es($data['project']['name']); ?>(<?php es($data['project']['id']); ?>) Analytics Views</h2>
	</div>

	<div class="row">
		<div class="col-lg-12">
			<div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5><i class="fa fa-info-circle"></i> Partner Information</h5>
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
			                <th>Status</th>
			                <th>Sample</th>
			                <th>Join in</th>
			                <th>C</th>
			                <th>S</th>
			                <th>Q</th>
			                <th>IR</th>
			                <th></th>
                			</tr>
                		</thead>
			            <tbody>
			            <?php if(!empty($data['partner'])): ?>
			            <?php foreach ($data['partner'] as $idx => $row): ?>
			            <tr>
			                <td><?php es($row['id']); ?></td>
			                <td><?php es($row['name']);?></td>
			                <td><?php es($row['country']);?></td>
			                <td><?php es($row['link']);?></td>
			                <td><?php es($row['status']);?></td>
			                <td><?php es($row['sample_size']);?></td>
			                <td><?php es($row['request_size']);?></td>
			                <td><?php es($row['c']);?></td>
			                <td><?php es($row['s']);?></td>
			                <td><?php es($row['q']);?></td>
			                <td><?php es($row['IR']);?>%</td>
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

	<div class="row">
		<div class="col-lg-6">
			<div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5><i class="fa fa-info-circle"></i> IP Block</h5>
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
                				<td>IP</td>
                				<td>Data</td>
                				<td>Date</td>
                			</tr>
				            <?php if(!empty($data['block'])): ?>
				            <?php foreach ($data['block'] as $idx => $row): ?>
				            <tr>
				                <td><?php es($row['ip_address']); ?></td>
				                <td><?php es($row['data']);?></td>
				                <td><?php es($row['created_at']);?></td>
				            </tr>
				            <?php endforeach; ?>
				        	<?php endif; ?>
                			<tr>
                				<td colspan="3">
						            <div class="btn-group pull-right">
						                <button class="page-prev btn btn-white btn-sm"><i class="fa fa-arrow-left"></i></button>
						                <button class="page-next btn btn-white btn-sm"><i class="fa fa-arrow-right"></i></button>
						            </div>
                				</td>
                			</tr>
                		</thead>
                		</table>
                	</div>
                </div>
			</div>
		</div>

		<div class="col-lg-6">
			<div class="ibox float-e-margins">
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
				            <?php if(!empty($data['history'])): ?>
				            <?php foreach ($data['history'] as $idx => $row): ?>
				            <tr>
				            	<td><?php es($row['accesskey']); ?></td>
				                <td><?php es($row['uid']); ?></td>
				                <td>
				                	<?php if($row['progress'] == 0): ?>
				                		<span class="label label-plain">Survey</span>
				                	<?php elseif ($row['progress'] == 1): ?>
				                		<span class="label label-success">Complate</span>
				                	<?php elseif ($row['progress'] == 2): ?>
				                		<span class="label label-danger">Screenout</span>
				                	<?php else: ?>
				                		<span class="label label-warning">Quotafull</span>
				                	<?php endif; ?>	
				                </td>
				                <td><?php es($row['created_at']);?></td>
				            </tr>
				            <?php endforeach; ?>
				        	<?php endif; ?>
                			<tr>
                				<td colspan="4">
						            <div class="btn-group pull-right">
						                <button class="page-prev btn btn-white btn-sm"><i class="fa fa-arrow-left"></i></button>
						                <button class="page-next btn btn-white btn-sm"><i class="fa fa-arrow-right"></i></button>
						            </div>
                				</td>
                			</tr>
                		</thead>
                		</table>
                	</div>
                </div>
			</div>
		</div>
	</div>
</div>
