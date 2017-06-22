<?php

use yii\helpers\Inflector;
use yii\helpers\StringHelper;

$baseModelName = StringHelper::basename($generator->modelClass);
$gridId = "grid" . Inflector::camel2words($baseModelName);

echo '<?php
    use ' . $generator->modelClass . ';
    use paulosales\w2ui\assets\w2uiAsset;

    $model = new ' . $baseModelName . '();
    $labels = $model->attributeLabels();
    $w2uiBundle = w2uiAsset::register($this);
?>';

?>


<div id="<?=$gridId?>" style="width: 100%; height: 400px;"></div>
<script type="text/javascript">

$(function () {
    w2utils.locale('<?= "<?= \$w2uiBundle->baseUrl; ?>" ?>/locale/pt-br.json');
    w2utils.settings.dataType = 'RESTFULLJSON';
    $('#<?=$gridId?>').w2grid({ 
        name: '<?=$gridId?>', 
        recid: 'id',
        url: 'http://localhost/api/usuarios',
        show: { 
            toolbar: true,
            footer: true,
            toolbarSave: true
        },
        columns: [     
<?php
    $tableColumns = [];
    if (($tableSchema = $generator->getTableSchema()) === false) {
        foreach ($generator->getColumnNames() as $name) {
            $tableColumns[] = "\t\t\t{ field: '$name', caption: '<?php echo \$labels[\"$name\"];?>', resizable: true, sortable: true}";
        }
    } else {
        foreach ($tableSchema->columns as $column) {
            $format = $generator->generateColumnFormat($column);
            $tableColumns[] = "\t\t\t{ field: '$column->name', caption: '<?php echo \$labels[\"$column->name\"];?>', resizable: true, sortable: true}";
        }
    }
    echo implode(",\r\n", $tableColumns);
?>
        ],
        toolbar: {
            items: [
                { id: 'add', type: 'button', caption: 'Adicionar', icon: 'w2ui-icon-plus' }
            ],
            onClick: function (event) {
                if (event.target == 'add') {
                    w2ui.<?=$gridId?>.add({ recid: w2ui.<?=$gridId?>.records.length + 1 });
                }
            }
        }
    });    
});
</script>