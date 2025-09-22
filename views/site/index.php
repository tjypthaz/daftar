<?php

/** @var yii\web\View $this */

use yii\bootstrap4\Modal;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'Daftar Online';
?>
<div class="row">
    <div class="col">
        <h1>Daftar Online "SELAIN BPJS"</h1>
        <?= Html::a('Buat Daftar Online', ['create'], ['class' => 'btn btn-primary mb-1']) ?>
    </div>
</div>
<?php if (empty($dataReservasi)): ?>
    <div class="alert alert-info">Tidak ada data reservasi.</div>
<?php else: ?>
    <div class="row">
        <?php foreach ($dataReservasi as $r): ?>
            <div class="col-md-6">
                <div class="card mb-3" style="border:3px solid #ddd; border-radius:6px; padding:15px; background:#fff;">
                    <h5 class="card-title">
                        <?= Html::encode($r['kodeBooking']) ?>
                    </h5>
                    <p class="card-text">
                        <strong>Nomor Rekam Medis :</strong> <?= Html::encode($r['NORM'])?><br>
                        <strong>Nama Pasien :</strong> <?= Html::encode($r['NAMA'])?><br>
                        <strong>Rencana Kunjungan :</strong> <?= Html::encode(Yii::$app->formatter->asDate($r['TANGGALKUNJUNGAN'], 'php:d F Y')) ?><br>
                        <strong>Estimasi Pelayanan :</strong> <?= Html::encode($r['ESTIMASI_PELAYANAN'])?><br>
                        <strong>Poli :</strong> <?= Html::encode($r['namaPoli'])?><br>
                        <strong>Dokter :</strong> <?= Html::encode($r['namaDokter'])?><br>
                        <strong>Cara Bayar :</strong> <?= Html::encode($r['caraBayar'])?><br>
                        <strong>Nomor Antrian :</strong> <?= Html::encode($r['NOMOR_ANTRIAN'])?><br>
                        <strong>Tanggal Daftar :</strong> <?= Html::encode($r['TGL_DAFTAR'])?>
                    </p>
                    <div class="row">
                        <!--jika tanggal sudah lewat hilangkan tombol-->
                        <?php
                        if($r['TANGGALKUNJUNGAN'] >= date('Y-m-d')){
                            ?>
                            <div class="col">
                                <?= Html::a('Scan', ['qr', 'kodeBooking' => $r['kodeBooking']], ['class' => 'btn btn-sm btn-success']) ?>
                            </div>
                            <div class="col text-right">
                                <?= Html::a('Batal', ['batal', 'kodeBooking' => $r['kodeBooking']], [
                                    'class' => 'btn btn-danger btn-sm',
                                    'data' => [
                                        'confirm' => 'Kamu yakin mau BATAL ?',
                                        'method' => 'post',
                                    ],
                                ]) ?>
                            </div>
                            <?php
                        }
                        ?>
                    </div>

                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php /*GridView::widget([
    'dataProvider' => $dataReservasi,
    'columns' => [
        ['class' => 'yii\grid\SerialColumn'],
        'TGL_DAFTAR',
        'kodeBooking',
        'TANGGALKUNJUNGAN',
        'ESTIMASI_PELAYANAN',
        'namaPoli',
        'namaDokter',
        'caraBayar',
        'NOMOR_ANTRIAN',

    ],
    'pager' => [
        'class' => 'yii\bootstrap4\LinkPager'
    ]
]);*/ ?>