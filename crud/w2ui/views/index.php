<?php

use yii\helpers\Inflector;
use yii\helpers\StringHelper;

$gridId = "grid" . Inflector::camel2words(StringHelper::basename($generator->modelClass));

?>

<div id="<?=$gridId?>" style="width: 100%; height: 400px;"></div>
<script type="text/javascript">

$(function () {
    $('#<?=$gridId?>').w2grid({ 
        name: '<?=$gridId?>', 
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
            $tableColumns[] = "\t\t\t{ field: '$name', caption: '$name', resizable: true, sortable: true}";
        }
    } else {
        foreach ($tableSchema->columns as $column) {
            $format = $generator->generateColumnFormat($column);
            $tableColumns[] = "\t\t\t{ field: '$column->name', caption: '$column->name', resizable: true, sortable: true}";
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
        },
        records: [
        ]
    });    
});
</script>