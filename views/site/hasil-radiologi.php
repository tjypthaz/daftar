<?php

/** @var yii\web\View $this */

use yii\bootstrap4\Modal;
use yii\grid\GridView;
use yii\bootstrap4\Html;
use yii\helpers\Url;

$this->title = 'Daftar Online';
?>
<div class="row">
    <div class="col">
        <h1>Hasil Radiologi</h1>
    </div>
</div>
<?php if (empty($dataHasilRadiologi)): ?>
    <div class="alert alert-info">Tidak ada data hasil radiologi.</div>
<?php else: ?>
    <div class="row">
        <?php foreach ($dataHasilRadiologi as $r): ?>
            <div class="col-md-6">
                <div class="card mb-3" style="border:3px solid #ddd; border-radius:6px; padding:15px; background:#fff;">
                    <h5 class="card-title">
                        <?= Html::encode($r['pemeriksaan']) ?>
                    </h5>
                    <p class="card-text">
                        <strong>Tgl Pemeriksaan :</strong> <?= Html::encode($r['tglPemeriksaan'])?>
                    </p>
                    <?php
                    echo Html::button('Hasil Bacaan Dokter', [
                        'class'  => 'btn btn-outline-success mb-2',
                        'onclick' => "window.open('" . Url::to(['expertise', 'acsn' => $r['id']]) . "', '_blank', 'width=1024,height=768,menubar=no,toolbar=no,location=no');",
                    ]);
                    echo Html::button('Gambar Original', [
                        'class'  => 'btn btn-outline-primary mb-2',
                        'onclick' => "window.open('" . Url::to(['radiologi-ori', 'acsn' => $r['id']]) . "', '_blank', 'width=1024,height=768,menubar=no,toolbar=no,location=no');",
                    ]);
                    echo Html::button('Gambar JPG', [
                        'class'  => 'btn btn-outline-warning mb-2',
                        'onclick' => "window.open('" . Url::to(['radiologi-jpg', 'acsn' => $r['id']]) . "', '_blank', 'width=1024,height=768,menubar=no,toolbar=no,location=no');",
                    ]);
                    ?>
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