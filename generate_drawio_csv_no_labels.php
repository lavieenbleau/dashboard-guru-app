<?php
$data = json_decode(file_get_contents('c:\ISCP\dashboardguru-app\models_data.json'), true);

$allowed_tables = [
    'users', 'products', 'serials', 'classrooms', 'students', 'reports', 
    'mapels', 'lessons', 'themes', 'subthemes', 'lesson_items', 'exercises', 
    'exercise_points', 'posts', 'tasks', 'post_comments'
];

$csv = "## Draw.io CSV Import untuk ERD Chen's Notation (Tanpa Label Garis)\n";
$csv .= "# label: %name%\n";
$csv .= "# style: shape=%shape%;whiteSpace=wrap;html=1;\n";
$csv .= "# namespace: csvimport_\n";
$csv .= "# connect: {\"from\": \"parent\", \"to\": \"id\", \"style\": \"endArrow=none;html=1;edgeStyle=orthogonalEdgeStyle;curved=1;\"}\n";
$csv .= "# connect: {\"from\": \"parent2\", \"to\": \"id\", \"style\": \"endArrow=none;html=1;edgeStyle=orthogonalEdgeStyle;curved=1;\"}\n";
$csv .= "# layout: horizontalflow\n";
$csv .= "id,name,shape,parent,parent2\n";

$nodes = [];
foreach ($data as $model => $info) {
    $table = $info['table'];
    if (!in_array($table, $allowed_tables)) continue;
    
    $entityId = "E_" . $table;
    $nodes[] = "$entityId,$table,rectangle,,";
    
    if (isset($info['primaryKey']) && $info['primaryKey']) {
        $nodes[] = "A_{$table}_{$info['primaryKey']},{$info['primaryKey']},ellipse,$entityId,";
    }
    
    $count = 0;
    foreach ($info['fillable'] as $field) {
        if ($field != $info['primaryKey']) {
            if ($count < 4) {
                $nodes[] = "A_{$table}_{$field},$field,ellipse,$entityId,";
                $count++;
            }
        }
    }
}

$relIdCounter = 1;
$processedRels = [];
foreach ($data as $model => $info) {
    $table1 = $info['table'];
    if (!in_array($table1, $allowed_tables)) continue;
    foreach ($info['relations'] as $relName => $rel) {
        if (!isset($data[$rel['related']])) continue;
        $table2 = $data[$rel['related']]['table'];
        if (!in_array($table2, $allowed_tables)) continue;
        
        $relKey1 = $table1 . '_' . $table2;
        $relKey2 = $table2 . '_' . $table1;
        
        if (in_array($relKey1, $processedRels) || in_array($relKey2, $processedRels)) {
            continue;
        }
        $processedRels[] = $relKey1;
        
        $relId = "R_" . $relIdCounter++;
        $nodes[] = "$relId,memiliki,rhombus,E_$table1,E_$table2";
    }
}

$csv .= implode("\n", $nodes);
file_put_contents('C:\Users\faisa\.gemini\antigravity-ide\brain\1d48db67-5db4-458f-87cd-ad554db006ec\drawio_chen_erd_no_labels.csv', $csv);
echo "DONE!";
