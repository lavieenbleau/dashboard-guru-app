<?php
$data = json_decode(file_get_contents('c:\ISCP\dashboardguru-app\models_data.json'), true);

$mermaid = "erDiagram\n";
$plantuml = "@startuml\nhide circle\nskinparam linetype ortho\n\n";

$relationsSet = [];

foreach ($data as $model => $info) {
    $table = $info['table'];
    if (!$table) continue;
    
    // add to Mermaid
    $mermaid .= "    {$table} {\n";
    if (isset($info['primaryKey']) && $info['primaryKey']) {
        $mermaid .= "        id {$info['primaryKey']} PK\n";
    }
    foreach ($info['fillable'] as $field) {
        if ($field != $info['primaryKey']) {
            $isFk = strpos($field, '_id') !== false ? ' FK' : '';
            $mermaid .= "        string {$field}{$isFk}\n";
        }
    }
    $mermaid .= "    }\n\n";
    
    // add to PlantUML
    $plantuml .= "entity \"{$table}\" as {$table} {\n";
    if (isset($info['primaryKey']) && $info['primaryKey']) {
        $plantuml .= "  *{$info['primaryKey']} : number <<generated>>\n";
    }
    $plantuml .= "  --\n";
    foreach ($info['fillable'] as $field) {
        if ($field != $info['primaryKey']) {
            $isFk = strpos($field, '_id') !== false ? ' <<FK>>' : '';
            $plantuml .= "  {$field} : string{$isFk}\n";
        }
    }
    $plantuml .= "}\n\n";
}

foreach ($data as $model => $info) {
    $table1 = $info['table'];
    if (!$table1) continue;
    foreach ($info['relations'] as $relName => $rel) {
        if (!isset($data[$rel['related']])) continue;
        $table2 = $data[$rel['related']]['table'];
        if (!$table2) continue;
        
        $type = $rel['type']; // BelongsTo, HasMany, HasOne, BelongsToMany
        
        $relKey = $table1 . '_' . $table2 . '_' . $type;
        $revKey = $table2 . '_' . $table1 . '_';
        
        if ($type == 'BelongsTo') {
            $mermaid .= "    {$table1} }o--|| {$table2} : \"belongs_to\"\n";
            $plantuml .= "{$table2} ||--o{ {$table1} : \"\"\n";
        } elseif ($type == 'HasMany') {
            $mermaid .= "    {$table1} ||--o{ {$table2} : \"has_many\"\n";
            $plantuml .= "{$table1} ||--o{ {$table2} : \"\"\n";
        } elseif ($type == 'HasOne') {
            $mermaid .= "    {$table1} ||--|| {$table2} : \"has_one\"\n";
            $plantuml .= "{$table1} ||--|| {$table2} : \"\"\n";
        } elseif ($type == 'BelongsToMany') {
            // Wait, BelongsToMany has pivot table
            $mermaid .= "    {$table1} }o--o{ {$table2} : \"belongs_to_many\"\n";
            $plantuml .= "{$table1} }o--o{ {$table2} : \"\"\n";
        }
    }
}
$plantuml .= "@enduml\n";

echo "=== MERMAID ===\n" . $mermaid . "\n\n=== PLANTUML ===\n" . $plantuml;
