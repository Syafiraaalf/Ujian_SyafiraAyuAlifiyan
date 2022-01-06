
<html>

<head>
    <style type="tetx/css">
        h2{ 
            padding:0px;
            margin:0px;
        }
        text{
            padding:0px;
        }
        table{
            font-size:12pt;
        }
    </style>
    <title>Cetak Bukti Pembayaran</title>
    <link href="<?=base_url('backend/assets/global/plugins/bootstrap/css/bootstrap.min.css');?>" rel="stylesheet" type="text/css"/>
</head>

<body>
    <div style="page-break-after:always;">
        <hr>
        <h2>BUKTI PEMBAYARAN</h2>
        <p style="text-align:right;width:95%;margin-top:-45px">NO. ORDER #<?=$detail->order_id;?><br>Tanggal : <?= date('d/m/Y');?></p>
        <hr style="border-color:black;">
        <div class="row">
            <div class="col-md-12">
                <table>
                    <tr>
                        <td width="200">Nama Pembeli</td>
                        <td width="10">:</td>
                        <td width="250"><?= $detail->order_nama; ?></td>
                        <td width="100">Kasir</td>
                        <td width="10">:</td>
                        <td><?=$detail->user_username;?></td>
                    </tr>
                    <tr>
                        <td>Nomor Meja</td>
                        <td>:</td>
                        <td><?= $detail->meja_nama; ?></td>
                    </tr>
                    <tr>
                        <td>Tanggal Bayar</td>
                        <td>:</td>
                        <td><?= date('d-m-Y', strtotime($detail->order_tgl_bayar)); ?></td>
                    </tr>
                </table>
                <br>
                <div class="portlet box blue-madison">
                    <div class="portlet-title text-center">
                        <h4>DAFTAR PESANAN MENU</h4>
                    </div>
                    <div class="portlet-body">
                        <div class="row">
                            <div class="col-md-12">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th width="5%">No.</th>
                                            <th>Nama Menu</th>
                                            <th width="10%">Qty</th>
                                            <th width="15%">Harga</th>
                                            <th width="15%">Sub Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $n=1;foreach(order_detail($detail->order_id) as $row): ?>
                                        <tr>
                                            <td><?= $n++; ?></td>
                                            <td><?= $row->menu_nama; ?></td>
                                            <td><?= $row->order_detail_qty; ?></td>
                                            <td align="right"><?= number_format($row->order_detail_harga,0,'','.'); ?></td>
                                            <td align="right"><?= number_format($row->order_detail_subtotal,0,'','.'); ?></td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                    <tfoot>
                                        <tr style="font-weight:bold;">
                                            <td colspan="2" align="center">TOTAL</td>
                                            <td><?= $detail->order_qty; ?></td>
                                            <td></td>
                                            <td align="right"><?= number_format($detail->order_total,0,'','.'); ?></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <hr style="border-color:gray;">
    </div>
</body>
<script src="<?=base_url('backend/assets/global/plugins/jquery-ui/jquery-ui.min.js');?>" type="text/javascript"></script>
<script src="<?=base_url('backend/assets/global/plugins/bootstrap/js/bootstrap.min.js');?>" type="text/javascript"></script>
</html>

<script>
window.print();
</script>