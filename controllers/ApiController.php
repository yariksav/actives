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
                    'index' => ['post', 'get'],
                    'export' => ['get']
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
            $data = json_decode(urldecode(file_get_contents("php://input")), true);
            unset($data['permissions'], $data['actions'], $data['controls'], $data['fields']);

            $dialog = ActiveObject::createObject($data);
            $response = json_encode($dialog->run());
        }
        // Для логики опросов
        catch (ConfirmException $e){
            $confirm['confirm'] = ArrayHelper::getValue($data, 'confirm', []);
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

        Yii::$app->response->format = 'html';
        return $response;
    }

    public function actionExport() {

    }
}
