<?php

namespace yariksav\actives\controllers;

use yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\HttpException;
use yii\helpers\ArrayHelper;

use yariksav\actives;
use yariksav\actives\base\ActiveObject;
use yariksav\actives\base\Exception;
use yariksav\actives\dialog\ConfirmException;
use yariksav\actives\dialog\ValidationException;

class ApiController extends Controller
{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'index' => ['post'],
                ],
            ],
        ];
    }

    public $enableCsrfValidation = false;

    public function actionIndex(){

        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        Yii::$app->response->headers->add('Access-Control-Allow-Origin','*');

        $dialog = null;
        $response = [];
        try{
            $data = Yii::$app->request->post('data');

            //$data = strtr($data, array('#u002F'=>'/', '#u002B'=>'+', '#u0026'=>'&', '#u0025'=>'%'));
            $data = json_decode($data, true);
            unset($data['permissions'], $data['actions'], $data['controls']);

            $dialog = ActiveObject::createObject($data);
            $response = json_encode($dialog->run());
        }
        // Для логики опросов
        catch (ConfirmException $e){
            $confirm['confirm'] = ArrayHelper::getValue($data, 'confirm', array());
            $confirm['confirm'][$e->id] = [
                'message'=>$e->getMessage(),
                'buttons'=>$e->buttons
            ];
            $response = json_encode($confirm);
        }
        catch (ValidationException $e){
            $response = json_encode([
                'error'=>$e->getMessage(),
                'validation'=>$e->validation
            ]);
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
        $data = Yii::$app->request->post('data');
        $data = json_decode($data, true);
        $grid = ActiveObject::createObject($data);
        $grid->run();
//        ActiveObject::createObject($data);

        Yii::$app->response->format = 'json';
        return $grid->response;

        //if ($grid->response != null)
        //	echo json_encode($grid->response);
    }
}
