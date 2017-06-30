<?php

use yii\helpers\Inflector;
use yii\helpers\StringHelper;

$baseModelName = StringHelper::basename($generator->modelClass);
$modelFriendlyName = Inflector::pluralize(Inflector::camel2words($baseModelName));
$gridId = "grid" . Inflector::camel2words($baseModelName);
$tableSchema = $generator->getTableSchema();
$pkName = $tableSchema->primaryKey[0];

echo '<?php
    use ' . $generator->modelClass . ';
    use paulosales\w2ui\assets\w2uiAsset;

    $model = new ' . $baseModelName . '();
    $labels = $model->attributeLabels();
    $w2uiBundle = w2uiAsset::register($this);

    $modelFriendlyName = ' . $generator->generateString($modelFriendlyName) . ';
    $serviceName = ' . strtolower($generator->generateString($modelFriendlyName . '/grid')) . ';

    $apiUrl = rtrim(Yii::$app->params["apiUrl"], "/") . "/";

    $this->title = $modelFriendlyName;
?>';

?>


<div id="<?=$gridId?>" style="width: 100%; height: 400px;"></div>
<script type="text/javascript">

$(function () {
    w2utils.locale('<?= "<?= \$w2uiBundle->baseUrl; ?>" ?>/locale/pt-br.json');
    w2utils.settings.dataType = 'RESTFULL';
    $('#<?=$gridId?>').w2grid({ 
        name: '<?=$gridId?>', 
        recid: '<?= $pkName; ?>',
        url: '<?= "<?= \$apiUrl . \$serviceName; ?>" ?>',
        show: { 
            toolbar: true,
            footer: true
        },
        columns: [
<?php
    $tableColumns = [];
    foreach ($tableSchema->columns as $column) {
        if($column->name == $pkName) {
            $tableColumns[] = "\t\t\t{ field: '$column->name', caption: '<?php echo \$labels[\"$column->name\"];?>', resizable: true, sortable: true}";
        }
        else {
            $tableColumns[] = "\t\t\t{ field: '$column->name', caption: '<?php echo \$labels[\"$column->name\"];?>', resizable: true, sortable: true, editable: {type: '" . $generator->getW2uiType($column->type) . "'}}";
        }
    }

    echo implode(",\r", $tableColumns)."\r";
?>
        ],
        multiSearch: true,
        searches: [
<?php
    $tableSearches = [];
    foreach ($tableSchema->columns as $column) {
        $tableSearches[] = "\t\t\t{ field: '$column->name', caption: '<?php echo \$labels[\"$column->name\"];?>', type: '" . $generator->getW2uiType($column->type) . "'}";
    }

    echo implode(",\r", $tableSearches)."\r";
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
                    var id = '*'+(w2ui.<?=$gridId?>.records.length+1);
                    w2ui.<?=$gridId?>.add({ <?= $pkName; ?>: id });
                    w2ui.<?=$gridId?>.select(id);
                    w2ui.<?=$gridId?>.editField(id, 1);
                    w2ui.<?=$gridId?>.toolbar.get('save').disabled = false;
                    w2ui.<?=$gridId?>.toolbar.get('cancel').disabled = false;
                    w2ui.<?=$gridId?>.toolbar.get('delete').disabled = false;
                    w2ui.<?=$gridId?>.toolbar.refresh();
                }
                else if (event.target == 'cancel') {
                    for(var i = 0; i < w2ui.<?=$gridId?>.records.length; i++) {
                        var strId = w2ui.<?=$gridId?>.records[i].<?= $pkName; ?> + '';
                        if(strId.startsWith("*")) {
                            w2ui.<?=$gridId?>.records.splice(i,1);
                        }
                        else if(w2ui.<?=$gridId?>.records[i].w2ui) {
                            w2ui.<?=$gridId?>.records[i].w2ui.changes = {};
                        }
                    }
                    w2ui.<?=$gridId?>.toolbar.get('save').disabled = true;
                    w2ui.<?=$gridId?>.toolbar.get('cancel').disabled = true;
                    w2ui.<?=$gridId?>.toolbar.get('delete').disabled = false;
                    w2ui.<?=$gridId?>.refresh();
                }
                else if (event.target == 'save') {
                    w2ui.<?=$gridId?>.save();
                    w2ui.<?=$gridId?>.toolbar.get('save').disabled = true;
                    w2ui.<?=$gridId?>.toolbar.get('cancel').disabled = true;
                    w2ui.<?=$gridId?>.toolbar.get('delete').disabled = false;
                    w2ui.<?=$gridId?>.toolbar.refresh();
                }
                else if (event.target == 'delete') {
                    for(var i = 0; i < w2ui.<?=$gridId?>.records.length; i++) {
                        var strId = w2ui.<?=$gridId?>.records[i].<?= $pkName; ?> + '';
                        if(strId.startsWith("*")) {
                            w2ui.<?=$gridId?>.records.splice(i,1);
                        }
                        else if(w2ui.<?=$gridId?>.records[i].w2ui) {
                            w2ui.<?=$gridId?>.records[i].w2ui.changes = {};
                        }
                    }
                    w2ui.<?=$gridId?>.delete();
                    w2ui.<?=$gridId?>.toolbar.get('save').disabled = true;
                    w2ui.<?=$gridId?>.toolbar.get('cancel').disabled = true;
                    w2ui.<?=$gridId?>.toolbar.get('delete').disabled = false;
                    w2ui.<?=$gridId?>.toolbar.refresh();
                }
            }
        },
        onEditField: function(event) {
            w2ui.<?=$gridId?>.toolbar.get('save').disabled = false;
            w2ui.<?=$gridId?>.toolbar.get('cancel').disabled = false;
            w2ui.<?=$gridId?>.toolbar.get('delete').disabled = false;
            w2ui.<?=$gridId?>.toolbar.refresh();
        },
        onSave: function(event) {
            if(event.xhr) {
                var response = eval("(" + event.xhr.responseText + ")");
                if(response.inserted) {
                    for(var i = 0; i < response.inserted.length; i++) {
                        var idsMap = response.inserted[i];
                        w2ui.<?=$gridId?>.get(idsMap.oldId).<?= $pkName; ?> = idsMap.newId;
                        w2ui.<?=$gridId?>.get(idsMap.oldId).recid = idsMap.newId;
                    }
                }
            }
        }
    });    
});
</script>