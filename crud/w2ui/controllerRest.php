<?php
/**
 * This is the template for generating a CRUD controller class file.
 */

use yii\db\ActiveRecordInterface;
use yii\helpers\StringHelper;


/* @var $this yii\web\View */
/* @var $generator yii\gii\generators\crud\Generator */

$apiControllerClass = StringHelper::basename($generator->apiControllerClass);
$modelClass = StringHelper::basename($generator->modelClass);

echo "<?php\n";
?>

namespace <?= StringHelper::dirname(ltrim($generator->apiControllerClass, '\\')) ?>;

use Yii;
use <?= ltrim($generator->modelClass, '\\') ?>;
use yii\rest\ActiveController;
use yii\data\ActiveDataProvider; 
use yii\base\Model; 

/**
 * <?= $apiControllerClass ?> implements the CRUD actions for <?= $modelClass ?> model.
 */
class <?= $apiControllerClass ?> extends ActiveController
{
    public $modelClass = '<?= ltrim($generator->modelClass, '\\') ?>';
    
    public function actionGetgrid() {
        $params = Yii::$app->getRequest()->getQueryParams();

        return Yii::createObject([
            'class' => ActiveDataProvider::className(),
            'query' => <?= $modelClass ?>::find(),
            'pagination' => [
                'pageSize' => $params['limit'],
                'page' => floor($params['offset']/$params['limit'])
            ]
        ]);
    }

    public function actionPutgrid() {
        $params = Yii::$app->getRequest()->getBodyParams();
        $models = [];
        if(isset($params['changes'])) {
            foreach($params['changes'] as $change) {
                if($change['recid'] == "*") { //CREATE ONE
                    $model = new <?= $modelClass ?>();
                }
                else {
                    $model = <?= $modelClass ?>::findOne($change['recid']);
                }
                $models[] = $model;
                $model->scenario = Model::SCENARIO_DEFAULT;
                $model->load($change, '');
                if ($model->save() === false && !$model->hasErrors()) {
                    throw new ServerErrorHttpException('Failed to update the object for unknown reason.');
                }
            }
        }

        return ["status"=>"success", "models"=> $models];
    }

    public function actionDeletegrid() {
        $params = Yii::$app->getRequest()->getBodyParams();
        $models = [];
        foreach($params['selected'] as $id) {
            $model = <?= $modelClass ?>::findOne($id);
            if ($model->delete() === false) {
                throw new ServerErrorHttpException('Failed to delete the object for unknown reason.');
            }
        }
        return ["status"=>"success"];
    }
}
