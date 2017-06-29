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
        $inserted = [];
        $errors = [];
        $isInserting = false;
        if(isset($params['changes'])) {
            foreach($params['changes'] as $change) {
                $isInserting = substr($change['recid'], 0,1) == "*";
                if($isInserting) {
                    $model = new <?= $modelClass ?>();
                }
                else {
                    $model = <?= $modelClass ?>::findOne($change['recid']);
                }
                $model->scenario = Model::SCENARIO_DEFAULT;
                $model->load($change, '');
                if (($model->save() === false && !$model->hasErrors()) || $model->getPrimaryKey() == null) {
                    $errors[] = ["id"=>$change['recid'], "message"=>"Failed to update the object for unknown reason."];
                }
                else if($isInserting) {
                    $inserted[] = ["oldId"=>$change['recid'], "newId"=>$model->getPrimaryKey()];
                }
            }
        }

        if(count($errors) > 0) {
            return ["status"=>"error", "message"=>"There are some erros when the system try to save records.", "errors"=>$errors];
        }
        else {
            return ["status"=>"success", "inserted"=>$inserted];
        }
    }

    public function actionDeletegrid() {
        $params = Yii::$app->getRequest()->getBodyParams();
        $models = [];
        $errors = [];
        foreach($params['selected'] as $id) {
            $model = <?= $modelClass ?>::findOne($id);
            if ($model->delete() === false) {
                $errors[] = "Failed to delete the object for unknown reason.";
            }
        }
        if(count($errors) > 0) {
            return ["status"=>"error", "message"=>"There are some erros when the system try to delete records.", "errors"=> $errors];
        }
        else {
            return ["status"=>"success"];
        }
    }
}
