<div class="col-lg-10 col-lg-offset-1 animated fadeInUp">
    <div class="ibox float-e-margins">
        <div class="ibox-title">
            <h5>Invoice</h5>
            <div class="ibox-tools">
                <a class="collapse-link">
                    <i class="fa fa-chevron-up"></i>
                </a>
                <a class="close-link">
                    <i class="fa fa-times"></i>
                </a>
            </div>
        </div>
        <div class="ibox-content p-xl">
            <div class="row">
                <div class="col-sm-6">
                    <h4><?php es($partner['project_name']);?></h4>
                    <h4 class="text-navy"><?php es($partner['pid']);?></h4>
                </div>
                <div class="col-sm-6 text-right">
                    <h4>Invoice No.</h4>
                    <h4 class="text-navy"><?php es($partner['invoice_no']);?></h4>
                    <p>
                        <span><strong>Invoice Date:</strong> <?php es($partner['invoice_date']);?></span><br>
                    </p>
                </div>
            </div>

            <div class="table-responsive m-t">
                <table class="table invoice-table">
                    <thead>
                    <tr>
                        <th>Info</th>
                        <th>Quantity</th>
                        <th>Unit Price</th>
                        <th>Other Price</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td><div><strong><?php es($partner['name']);?></strong> (<?php es($partner['id']);?>)</div>
                            <small>
                                <div><strong>Complete: </strong><?php es($partner['c']);?></div>
                                <div><strong>Screenout: </strong><?php es($partner['s']);?></div>
                                <div><strong>Quotafull: </strong><?php es($partner['q']);?></div>
                                <div><strong>IR: </strong><?php es($partner['ir_a']);?>%</div>
                            </small>
                        </td>
                        <td id="quantity"><?php es($partner['quantity']);?></td>
                        <td id="price">$<?php es($partner['price']);?></td>
                        <td id="other">$<?php es($partner['other']);?></td>
                        <td><button class="invoice-edit btn btn-default"><i class="fa fa-edit"></i> Edit</button></td>
                    </tr>
                    </tbody>
                </table>
                <div id="remark" class="well m-t" style="<?php if(strlen($partner['remark']) == 0) es("display: none;"); ?>">
                    <?php es($partner['remark']);?>
                </div>

                <form id="invoice-form">
                    <input type="hidden" name="id" value="<?php es($partner['invoice_no']);?>">
                    <input type="hidden" name="pid" value="<?php es($partner['pid']);?>">
                    <input type="hidden" name="tid" value="<?php es($partner['id']);?>">
                    <input type="hidden" name="sample" value="<?php es($partner['c']);?>">
                    <input type="hidden" id="q" name="q" value="<?php es($partner['quantity']);?>">
                    <input type="hidden" id="p" name="p" value="<?php es($partner['price']);?>">
                    <input type="hidden" id="o" name="o" value="<?php es($partner['other']);?>">
                    <input type="hidden" id="r" name="r" value="<?php es($partner['remark']);?>">
                </form>
            </div><!-- /table-responsive -->

            <table class="table invoice-total">
                <tbody>
                <tr>
                    <td><strong>TOTAL :</strong></td>
                    <td id="total">$<?php es($partner['total']);?></td>
                </tr>
                </tbody>
            </table>
            <div class="text-right">
                <button class="invoice-save btn btn-primary"><i class="fa fa-dollar"></i> Make A Invoice</button>
            </div>
        </div>
    </div>
</div>