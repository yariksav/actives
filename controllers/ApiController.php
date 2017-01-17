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
use yariksav\actives\exceptions\ConfirmException;
use yariksav\actives\exceptions\ValidationException;

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

    public function createInstance($alias, $component, $data){
        unset($data['permissions'], $data['actions'], $data['controls']);

        $class = ArrayHelper::getValue(Yii::$app->params['alias'], $alias). '\\' . ucfirst($component);

        if (!class_exists($class)) {
            throw new \Exception('Unknown request');
        }
        $instance = Yii::createObject($class, $data);
        if (!$instance instanceof actives\base\RunnableInterface) {
            throw new \Exception('Incorrect method');
        }
        return $instance;
    }

    public function actionComponent($alias, $component, $action = null){

        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        Yii::$app->response->headers->add('Access-Control-Allow-Origin','*');
        Yii::$app->response->headers->add('Content-type','application/json');

        $response = [];
        try{
            $request = json_decode(urldecode(Yii::$app->request->rawBody), true);
            if (!$request) {
                $request = [];
            }
            $instance = $this->createInstance($alias, $component, $request);
            return $instance->run($action);
        }

        // Для логики опросов
        catch (ConfirmException $e){
            $confirm['confirm'] = ArrayHelper::getValue($request, 'confirm', []);
            $confirm['confirm'][$e->id] = [
                'message'=>$e->getMessage(),
                'buttons'=>$e->buttons
            ];
            return $confirm;
        }

        catch (ValidationException $e){
            Yii::$app->response->setStatusCode(400);
            return $e->validation;
        }
    }

    public function actionExport($alias, $component) {
        Yii::$app->response->format = 'html';
        $instance = $this->createInstance($alias, $component, $_GET);
        $instance->run('export');
        Yii::$app->end();
    }
}
