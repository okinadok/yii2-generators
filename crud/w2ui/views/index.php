<?php

use yii\helpers\Inflector;
use yii\helpers\StringHelper;

$baseModelName = StringHelper::basename($generator->modelClass);
$modelFriendlyName = $generator->generateString(Inflector::pluralize(Inflector::camel2words($baseModelName)));
$gridId = "grid" . Inflector::camel2words($baseModelName);

echo '<?php
    use ' . $generator->modelClass . ';
    use paulosales\w2ui\assets\w2uiAsset;

    $model = new ' . $baseModelName . '();
    $labels = $model->attributeLabels();
    $w2uiBundle = w2uiAsset::register($this);

    $modelFriendlyName = ' . $modelFriendlyName . ';
    $serviceName = ' . strtolower($modelFriendlyName) . ';

    $apiUrl = rtrim(Yii::$app->params["apiUrl"], "/") . "/";

    $this->title = $modelFriendlyName;
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
        url: '<?= "<?= \$apiUrl . \$serviceName; ?>" ?>',
        show: { 
            toolbar: true,
            footer: true
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
                { type: 'break' },
                { id: 'add', type: 'button', caption: 'Adicionar', icon: 'fa fa-plus', tooltip: 'Adiciona um novo registro na grid.' },
                { type: 'break' },
                { id: 'cancel', type: 'button', caption: 'Cancelar', icon: 'fa fa-times', disabled: true, tooltip: 'Cancela as alterações nos registros não salvos.' },
                { type: 'break' },
                { id: 'save', type: 'button', caption: 'Salvar', icon: 'fa fa-floppy-o', disabled: true, tooltip: 'Salva as alterações dos registros.' },
                { type: 'break' },
                { id: 'delete', type: 'button', caption: 'Remover', icon: 'fa fa-trash-o', disabled: false, tooltip: 'Remove os registros selecionados.' }
            ],
            onClick: function (event) {
                if (event.target == 'add') {
                    var id = 'new_' + w2ui.gridUsuarios.records.length + 1;
                    w2ui.gridUsuarios.add({ recid: id });
                    w2ui.gridUsuarios.select(id);
                    w2ui.gridUsuarios.editField(id, 1);
                    w2ui.gridUsuarios.toolbar.get('save').disabled = false;
                    w2ui.gridUsuarios.toolbar.get('cancel').disabled = false;
                    w2ui.gridUsuarios.toolbar.get('add').disabled = true;
                    w2ui.gridUsuarios.toolbar.get('delete').disabled = true;
                    w2ui.gridUsuarios.toolbar.refresh();
                }
                else if (event.target == 'cancel') {
                    for(var i = 0; i < w2ui.gridUsuarios.records.length; i++) {
                        var strId = w2ui.gridUsuarios.records[i].recid + '';
                        if(strId.startsWith("new_")) {
                            w2ui.gridUsuarios.records.splice(i,1);
                        }
                        else if(w2ui.gridUsuarios.records[i].w2ui) {
                            w2ui.gridUsuarios.records[i].w2ui.changes = {};
                        }
                    }
                    w2ui.gridUsuarios.toolbar.get('save').disabled = true;
                    w2ui.gridUsuarios.toolbar.get('cancel').disabled = true;
                    w2ui.gridUsuarios.toolbar.get('add').disabled = false;
                    w2ui.gridUsuarios.toolbar.get('delete').disabled = false;
                    w2ui.gridUsuarios.refresh();
                }
                else if (event.target == 'save') {
                    w2ui.gridUsuarios.save();
                    w2ui.gridUsuarios.toolbar.get('save').disabled = true;
                    w2ui.gridUsuarios.toolbar.get('cancel').disabled = true;
                    w2ui.gridUsuarios.toolbar.get('add').disabled = false;
                    w2ui.gridUsuarios.toolbar.get('delete').disabled = false;
                    w2ui.gridUsuarios.toolbar.refresh();
                }
                else if (event.target == 'delete') {
                    w2ui.gridUsuarios.delete();
                    w2ui.gridUsuarios.toolbar.get('save').disabled = true;
                    w2ui.gridUsuarios.toolbar.get('cancel').disabled = true;
                    w2ui.gridUsuarios.toolbar.get('add').disabled = false;
                    w2ui.gridUsuarios.toolbar.get('delete').disabled = false;
                    w2ui.gridUsuarios.toolbar.refresh();
                }
            }
        },
        onEditField: function(event) {
            w2ui.gridUsuarios.toolbar.get('save').disabled = false;
            w2ui.gridUsuarios.toolbar.get('cancel').disabled = false;
            w2ui.gridUsuarios.toolbar.get('add').disabled = true;
            w2ui.gridUsuarios.toolbar.get('delete').disabled = true;
            w2ui.gridUsuarios.toolbar.refresh();
        }
    });    
});
</script>