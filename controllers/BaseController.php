<?php 

namespace app\controllers;

use Yii;
use yii\rest\ActiveController;
use yii\data\ActiveDataFilter;
use yii\data\ActiveDataProvider;

use app\libraries\Crypt;
use app\libraries\Session;

class BaseController extends ActiveController
{
    public $modelClass = '';

    /**
     * Changes how results are formatted when fetching collection
     */
    public $serializer = [
        'class' => 'yii\rest\Serializer',
        'collectionEnvelope' => 'items',
    ];

    /**
     * Initial processes before start of any action
     */
    public function init()
    {
        // $this->logRequests();
    }

    /**
     * Enable no-pagination requests when fetching collection
     * @return self
     */
    public function actions()
    {
        $actions = parent::actions();
        $actions['index']['prepareDataProvider'] = [$this, 'prepareDataProvider'];
        
        return $actions;
    }

    /**
     * Enable GET requests to use native filtering
     * @return object ActiveDataProvider
     */
    public function prepareDataProvider() 
    {
        $requestParams = Yii::$app->request->get();

        $filter = new ActiveDataFilter([
            'searchModel' => $this->modelClass,
        ]);

        $filterCondition = null;

        if ($filter->load($requestParams))
        {
            $filterCondition = $filter->build();
            if ($filterCondition === false)
            {
                return $filter;
            }
        }

        $query = $this->modelClass::find();

        if ($filterCondition !== null)
        {
            $query->andWhere($filterCondition);
        }

        $pagination = [
            'params' => $requestParams,
        ];

        if (isset($requestParams['no-pagination']) and $requestParams['no-pagination'] == 1)
        {
            $pagination = false;
        }

        return new ActiveDataProvider([
            'query' => $query,
            'pagination' => $pagination,
            'sort' => [
                'params' => $requestParams,
            ],
        ]);
    }

    /**
     * Log every requests in tabbed format
     */
    private function logRequests()
    {
        $logRequestParameters = [
            Yii::$app->request->userIP,
            Yii::$app->request->userAgent,
            Yii::$app->request->method,
            Yii::$app->request->url,
            json_encode(Session::get(null,false)),
            json_encode(Yii::$app->request->get())
        ];

        $logMessage = "";

        foreach ($logRequestParameters as $parameter)
        {
            $logMessage .= "\t" . $parameter;
        }

        Yii::$app->monolog->getLogger('request')->log('info', $logMessage);
    }
    
    /**
     * Converts firstErrors into REST-style format
     * @param  array  $model->firstErrors
     * @return array  an array of errors
     */
    public function jsonError(array $errors)
    {
        $errors_array = array();

        foreach ($errors as $key => $value)
        {
            $errors_array[] = [
                'field' => $key,
                'message' => $value
            ];
        }

        Yii::$app->response->statusCode = 422;
        return $errors_array;
    }
}