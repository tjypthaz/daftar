<?php

/** @var yii\web\View $this */

use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'Dashboard';
?>
<div class="row">
    <div class="col-md">
        <?= Html::a(
            '<div class="card text-white bg-primary mb-3">
                <div class="card-body text-center">
                    <h2>Daftar Online UMUM</h2>
                </div>
            </div>',
            ['reservasi'],
            ['style' => 'text-decoration:none']
        ) ?>
    </div>

    <div class="col-md">
        <?= Html::a(
            '<div class="card text-white bg-success mb-3">
                <div class="card-body text-center">
                    <h2>Hasil Radiologi</h2>
                </div>
            </div>',
            ['hasil-radiologi'],
            ['style' => 'text-decoration:none']
        ) ?>
    </div>
</div>