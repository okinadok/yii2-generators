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

/**
 * <?= $apiControllerClass ?> implements the CRUD actions for <?= $modelClass ?> model.
 */
class <?= $apiControllerClass ?> extends ActiveController
{
    public $modelClass = '<?= ltrim($generator->modelClass, '\\') ?>';
}
