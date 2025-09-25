<?php

namespace app\controllers;

use Yii;
use yii\base\DynamicModel;
use yii\data\ArrayDataProvider;
use yii\filters\AccessControl;
use yii\helpers\Html;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;
use yii\httpclient\Client;
use Da\QrCode\QrCode;

class SiteController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        $token = Yii::$app->session->get('authToken');
        if (!$token) {
            // kalau belum login, arahkan ke login
            return $this->redirect(['site/login']);
        }

        $dataReservasi = [];
        $client = new Client(['baseUrl' => Yii::$app->params['pointo']]); // URL API Anda
        $response = $client->get('daftar-umum/list-reservasi', [], [
            'Authorization' => 'Bearer ' . $token,
        ])->send();
        /*echo "<pre>";
        print_r($response);
        exit;*/
        if($response->isOk){
            if ($response->data['code'] == '200'){
                $dataReservasi = $response->data['data'];
            }else{
                Yii::$app->session->setFlash('error',$response->data['message']);
            }
        }else{
            Yii::$app->session->setFlash('error',$response->data['message']);
            return $this->goHome();
        }

        return $this->render('index',[
            'dataReservasi' => $dataReservasi
        ]);
    }

    /**
     * Login action.
     *
     * @return Response|string
     */
    public function actionLogin()
    {
        $model = new DynamicModel(['username', 'password']);
        $model->addRule(['username', 'password'], 'required');

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $client = new Client(['baseUrl' => Yii::$app->params['pointo']]); // URL API Anda
            $response = $client->post('auth/login', [
                'NORM' => $model->username,
                'TANGGAL_LAHIR' => $model->password,
            ])->send();

            /*echo "<pre>";
            print_r($response->data);
            exit;*/

            if ($response->isOk && isset($response->data['data']['access_token'])) {
                // simpan token ke session
                Yii::$app->session->set('authToken', $response->data['data']['access_token']);
                return $this->redirect(['index']);
            } else {
                Yii::$app->session->setFlash('error', $response->data['message']);
            }
        }

        return $this->render('login', ['model' => $model]);
    }

    /**
     * Logout action.
     *
     * @return Response
     */
    public function actionLogout()
    {
        Yii::$app->session->remove('authToken');
        return $this->goHome();
    }

    public function actionQr($kodeBooking)
    {
        $qrCode = (new QrCode($kodeBooking))
            ->setSize(800)
            ->setMargin(10);

        // tampilkan langsung di halaman sebagai <img>
        echo '<img src="' . $qrCode->writeDataUri() . '"> <br>';
        echo "<h1>Pastikan Tingkat Kecerahan HP Anda Maksimal, Agar Mudah diBaca</h1>";
        exit;
    }

    public function actionBatal($kodeBooking)
    {
        $token = Yii::$app->session->get('authToken');
        if (!$token) {
            // kalau belum login, arahkan ke login
            return $this->redirect(['site/login']);
        }

        $client = new Client(['baseUrl' => Yii::$app->params['pointo']]); // URL API Anda
        $response = $client->post('daftar-umum/batal', [
            'kodeBooking' => $kodeBooking,
        ], [
            'Authorization' => 'Bearer ' . $token,
        ])->send();
        if($response->isOk && $response->data['code'] == '200'){
            Yii::$app->session->setFlash('success', 'Berhasil batal daftar online');
        }else{
            Yii::$app->session->setFlash('error', 'Gagal. '.$response->data['message']);
        }

        return $this->redirect(['index']);

    }

    public function actionCreate()
    {
        $model = new \yii\base\DynamicModel(['caraBayar', 'noWA', 'tglKunjungan', 'idJadwal']);
        $model->addRule(['caraBayar', 'noWA', 'tglKunjungan', 'idJadwal'], 'required');
        $model->addRule('noWA', 'match', ['pattern' => '/^[0-9]{10,15}$/', 'message' => 'No WA harus angka 10-15 digit']);

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $token = Yii::$app->session->get('authToken');
            if (!$token) {
                return $this->redirect(['site/login']);
            }

            // kirim ke API
            $client = new Client(['baseUrl' => Yii::$app->params['pointo']]);
            $response = $client->post('daftar-umum/add-reservasi', [
                'caraBayar'   => $model->caraBayar,
                'noWA'        => $model->noWA,
                'tglKunjungan'=> $model->tglKunjungan,
                'idJadwal'    => $model->idJadwal,
            ], [
                'Authorization' => 'Bearer ' . $token,
            ])->send();

            if ($response->isOk && $response->data['code'] == '200') {
                Yii::$app->session->setFlash('success', $response->data['message']);
                return $this->redirect(['index']);
            } else {
                Yii::$app->session->setFlash('error', $response->data['message']);
            }
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    public function actionGetJadwal($tgl)
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $token = Yii::$app->session->get('authToken');
        if (!$token) {
            return ['message' => 'Token Kadaluwarsa. Silakan Login Ulang', 'success' => false, 'data' => []];
        }

        $client = new Client(['baseUrl' => Yii::$app->params['pointo']]);
        $response = $client->get('daftar-umum/list-jadwal?tgl='.$tgl, [], [
            'Authorization' => 'Bearer ' . $token,
        ])->send();

        if ($response->isOk) {
            return ['message' => 'OK', 'success' => true, 'data' => $response->data['data']];
        }

        return ['message' => 'Error tidak diketahui', 'success' => false, 'data' => []];
    }


}
