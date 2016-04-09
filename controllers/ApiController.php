<?php

namespace yariksav\actives\controllers;

use yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\HttpException;
use yii\helpers\ArrayHelper;

use yariksav\actives;
use yariksav\actives\components\SyActiveObject;
use yariksav\actives\components\SyException;
use yariksav\actives\components\SyConfirmException;

class ApiController extends Controller
{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'index' => ['post', 'get'],
                ],
            ],
        ];
    }

    public $enableCsrfValidation = false;

    public function actionIndex(){
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $dialog = null;
        $response = [];
        try{
            $data = \Yii::$app->request->post('data');

            //$data = strtr($data, array('#u002F'=>'/', '#u002B'=>'+', '#u0026'=>'&', '#u0025'=>'%'));
            $data = json_decode($data, true);

            $dialog = SyActiveObject::createInstance($data);
            $response = $dialog->build();
        }
        // Для логики опросов
        catch (SyConfirmException $e){
            $confirm['confirm'] = ArrayHelper::getValue($data, 'confirm', array());
            $confirm['confirm'][$e->id] = array('message'=>$e->getMessage(), 'buttons'=>$e->buttons);
            $response = json_encode($confirm);
        }
        catch (HttpException $e){
            throw $e;
        }
        // Ошибка
        /*catch (SyException $e){
            var_export($e);
            $msg = $e->getMessage();
            if (strpos($msg, 'Integrity constraint violation: 1451') !== false)
                $msg = Yii::t('app.error', 'You can not delete a record, as it referred to the data from other tables');

            Yii::error('Dialog Exception: ' . $e->getMessage());
            $response['error'] = str_replace('<br/>', ' ', trim($msg));
            $response['code'] = $e->getCode();
            if ($dialog && isset($dialog->validation)) {
                Yii::info(var_export($dialog->validation, true));
                $response['validation'] = $dialog->validation;
            }
        }*/
        Yii::$app->response->format = 'html';
        return $response;
    }

    public function actionGrid(){
        $grid = SyActiveObject::createInstance($_REQUEST);
        Yii::$app->response->format = 'json';
        return $grid->response;

        //if ($grid->response != null)
        //	echo json_encode($grid->response);
    }
}
