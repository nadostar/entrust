<div class="col-lg-10 col-lg-offset-1 animated fadeInUp">
    <div class="ibox float-e-margins">
        <div class="ibox-title">
            <h5>Access Log #<?php es($log['id']);?></h5>
            <div class="ibox-tools">
                <a class="close-link">
                    <i class="fa fa-times"></i>
                </a>
            </div>
        </div>
        <div class="ibox-content">
            <form class="form-horizontal">
                <div class="form-group">
                    <label class="col-lg-2 control-label">#</label>
                    <div class="col-lg-9">
                        <p class="form-control-static"><?php es($log['id']);?></p>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-lg-2 control-label">Accesskey</label>
                    <div class="col-lg-9">
                        <p class="form-control-static"><?php es($log['accesskey']);?></p>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-lg-2 control-label">Category</label>
                    <div class="col-lg-9">
                        <p class="form-control-static"><?php echo MasterData::getProgress($log['kind']); ?></p>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-lg-2 control-label">Data</label>
                    <div class="col-lg-9">
                        <p class="form-control-static">
                            <pre><?php json_format($log['data']);?></pre>
                        </p>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-lg-2 control-label">IP Address</label>
                    <div class="col-lg-9">
                        <p class="form-control-static"><?php es($log['ip_address']);?></p>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-lg-2 control-label">Date</label>
                    <div class="col-lg-9">
                        <p class="form-control-static"><?php es($log['created_at']);?></p>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
