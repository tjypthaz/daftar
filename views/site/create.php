<?php

use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\helpers\Html;

/** @var $model \yii\base\DynamicModel */

$this->title = 'Buat Daftar Online';
?>

<h1><?= Html::encode($this->title) ?></h1>

<div class="reservasi-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'caraBayar')->dropDownList([
        '1' => 'Umum',
        '4' => 'Jasa Raharja',
        '8' => 'JAMKESDA',
        '15' => 'Jaminan Ketenagakerjaan',
    ], ['prompt' => 'Pilih Cara Bayar'])->label('Cara Bayar / Jaminan') ?>

    <?= $form->field($model, 'noWA')->textInput(['maxlength' => true,'placeholder' => 'nomor whatsapp aktif'])
    ->label('Nomor Whatsapp')?>

    <?= $form->field($model, 'tglKunjungan')->input('date', ['id' => 'tgl-kunjungan'])
        ->label('Tanggal Kunjungan') ?>

    <?= $form->field($model, 'idJadwal')->dropDownList([], [
        'prompt' => 'Pilih Jadwal',
        'id' => 'id-jadwal'
    ])->label('Jadwal Dokter') ?>

    <div class="form-group">
        <?= Html::submitButton('Simpan', ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Batal', ['index'], ['class' => 'btn btn-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<?php
$jadwalUrl = Url::to(['get-jadwal']);
$indexUrl  = Url::to(['index']);
$script = <<<JS
$('#tgl-kunjungan').on('change', function() {
    var tgl = $(this).val();
    if(!tgl) {
        $('#id-jadwal').html('<option value="">Pilih Jadwal</option>');
        return;
    }
    
    var today = new Date();
    today.setHours(0,0,0,0); // reset jam ke 00:00:00
    var selectedDate = new Date(tgl + "T00:00:00"); // pastikan tidak ambigu timezone

    // Batas maksimal = hari ini + 30 hari
    var maxDate = new Date();
    maxDate.setDate(today.getDate() + 31);

    if (selectedDate < today) {
        alert('Tanggal tidak boleh kurang dari hari ini.');
        $('#tgl-kunjungan').val('');
        return;
    }

    if (selectedDate > maxDate) {
        alert('Tanggal tidak boleh lebih dari 30 hari dari hari ini.');
        $('#tgl-kunjungan').val('');
        return;
    }
    
    // validasi: jika pilih hari ini, batasi jam sampai 09:00
    var now = new Date();
    if (selectedDate.getTime() === today.getTime()) {
        var batas = new Date();
        batas.setHours(9,0,0,0); // jam 09:00 hari ini
        if (now > batas) {
            alert('Pendaftaran Online untuk hari ini sudah ditutup (maksimal jam 09:00). Silakan pilih tanggal lain');
            $('#tgl-kunjungan').val('');
            return;
        }
    }
    
    $.getJSON('$jadwalUrl', {tgl: tgl}, function(res) {
        var dropdown = $('#id-jadwal');
        dropdown.empty().append('<option value="">Pilih Jadwal</option>');
        if(res.success) {
            $.each(res.data, function(i, item) {
                dropdown.append('<option value="'+item.idJadwal+'" data-kuota="'+item.sisaKuota+'">'+item.namaDokter+' ('+item.jamPelayanan+')</option>');
            });
        } else {
            alert(res.message);
            window.location.href = '$indexUrl';
        }
    }).fail(function() {
        alert('Terjadi kesalahan koneksi ke server. Silakan Login Ulang.');
        window.location.href = '$indexUrl';
    });
});

// Event saat user pilih jadwal
$('#id-jadwal').on('change', function() {
    var selectedOption = $(this).find(':selected');
    var kuota = selectedOption.data('kuota');
    if (kuota === 0 || kuota === "0") {
        alert('Kuota untuk tanggal ini sudah habis. Silakan pilih tanggal lain.');
        $(this).val(''); // reset pilihan
    }
});
JS;
$this->registerJs($script);
?>