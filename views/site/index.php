<?php

/** @var yii\web\View $this */

use yii\bootstrap4\Modal;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use kartik\icons\Icon;

$this->title = 'Dashboard';
?>
<div class="row mb-1">
    <div class="col text-center">
        <?= Html::a(Icon::show('calendar-check').'<br>Daftar Online UMUM',[
            Url::to('reservasi')
        ],['class' => 'btn'])?>
    </div>
    <div class="col text-center">
        <?= Html::a(Icon::show('x-ray').'<br>Hasil Radiologi',[
            Url::to('hasil-radiologi')
        ],['class' => 'btn'])?>
    </div>
</div>