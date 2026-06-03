<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$modelsPath = __DIR__.'/app/Models';
$files = scandir($modelsPath);

$output = [];
foreach ($files as $file) {
    if (pathinfo($file, PATHINFO_EXTENSION) === 'php') {
        $className = 'App\\Models\\' . pathinfo($file, PATHINFO_FILENAME);
        if (class_exists($className) && is_subclass_of($className, 'Illuminate\Database\Eloquent\Model')) {
            $reflection = new ReflectionClass($className);
            $model = new $className;
            
            $relations = [];
            foreach ($reflection->getMethods() as $method) {
                if ($method->class == $className && !$method->getParameters()) {
                    try {
                        $returnType = $method->getReturnType();
                        if (!$returnType) {
                            $result = $method->invoke($model);
                            if ($result instanceof \Illuminate\Database\Eloquent\Relations\Relation) {
                                $relations[$method->getName()] = [
                                    'type' => class_basename(get_class($result)),
                                    'related' => get_class($result->getRelated()),
                                    'foreignKey' => method_exists($result, 'getForeignKeyName') ? $result->getForeignKeyName() : (method_exists($result, 'getForeignKey') ? $result->getForeignKey() : null),
                                    'ownerKey' => method_exists($result, 'getOwnerKeyName') ? $result->getOwnerKeyName() : null,
                                ];
                            }
                        }
                    } catch (\Throwable $e) {}
                }
            }
            $output[$className] = [
                'table' => $model->getTable(),
                'primaryKey' => $model->getKeyName(),
                'fillable' => $model->getFillable(),
                'relations' => $relations
            ];
        }
    }
}

$data = $output;

$allowed_tables = [
    'users', 'products', 'serials', 'classrooms', 'students', 'reports', 
    'mapels', 'lessons', 'themes', 'subthemes', 'lesson_items', 'exercises', 
    'exercise_points', 'posts', 'tasks', 'post_comments', 'quiz_activity_logs', 'share_exercises'
];

$csv = "## Draw.io CSV Import untuk ERD Chen's Notation\n";
$csv .= "# label: %name%\n";
$csv .= "# style: shape=%shape%;whiteSpace=wrap;html=1;\n";
$csv .= "# namespace: csvimport_\n";
$csv .= "# connect: {\"from\": \"parent\", \"to\": \"id\", \"style\": \"endArrow=none;html=1;edgeStyle=orthogonalEdgeStyle;\", \"label\": \"%edgeLabel%\"}\n";
$csv .= "# connect: {\"from\": \"parent2\", \"to\": \"id\", \"style\": \"endArrow=none;html=1;edgeStyle=orthogonalEdgeStyle;\", \"label\": \"%edgeLabel2%\"}\n";
$csv .= "# layout: auto\n";
$csv .= "id,name,shape,parent,edgeLabel,parent2,edgeLabel2\n";

$nodes = [];
$relationships = [];

foreach ($data as $model => $info) {
    $table = $info['table'];
    if (!in_array($table, $allowed_tables)) continue;
    
    $entityId = "E_" . $table;
    $nodes[] = "$entityId,$table,rectangle,,,,";
    
    if (isset($info['primaryKey']) && $info['primaryKey']) {
        $nodes[] = "A_{$table}_{$info['primaryKey']},{$info['primaryKey']},ellipse,$entityId,,,";
    }
    
    $count = 0;
    foreach ($info['fillable'] as $field) {
        if ($field != $info['primaryKey']) {
            if ($count < 5) {
                $nodes[] = "A_{$table}_{$field},$field,ellipse,$entityId,,,";
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
        
        $type = $rel['type'];
        
        $relKey1 = $table1 . '_' . $table2;
        $relKey2 = $table2 . '_' . $table1;
        
        if (in_array($relKey1, $processedRels) || in_array($relKey2, $processedRels)) {
            continue;
        }
        $processedRels[] = $relKey1;
        
        $relId = "R_" . $relIdCounter++;
        $relNameDisplay = "memiliki"; 
        
        $card1 = "1";
        $card2 = "N";
        
        if ($type == 'BelongsTo') {
            $card1 = "N";
            $card2 = "1";
        } elseif ($type == 'HasOne') {
            $card1 = "1";
            $card2 = "1";
        } elseif ($type == 'BelongsToMany') {
            $card1 = "N";
            $card2 = "N";
        }
        
        $nodes[] = "$relId,$relNameDisplay,rhombus,E_$table1,$card1,E_$table2,$card2";
    }
}

$csv .= implode("\n", $nodes);

file_put_contents('C:\Users\faisa\.gemini\antigravity-ide\brain\1d48db67-5db4-458f-87cd-ad554db006ec\drawio_chen_erd.csv', $csv);
echo "DONE!";
