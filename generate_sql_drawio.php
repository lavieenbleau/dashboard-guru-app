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
$sql = "";
$fks = [];

foreach ($data as $model => $info) {
    $table = $info['table'];
    if (!$table) continue;
    
    $sql .= "CREATE TABLE `" . $table . "` (\n";
    
    $columns = [];
    if (isset($info['primaryKey']) && $info['primaryKey']) {
        $columns[] = "  `" . $info['primaryKey'] . "` INT NOT NULL AUTO_INCREMENT PRIMARY KEY";
    }
    
    foreach ($info['fillable'] as $field) {
        if ($field != $info['primaryKey']) {
            $columns[] = "  `" . $field . "` VARCHAR(255)";
        }
    }
    
    $sql .= implode(",\n", $columns);
    $sql .= "\n);\n\n";
    
    // relationships
    foreach ($info['relations'] as $relName => $rel) {
        if (!isset($data[$rel['related']])) continue;
        $table2 = $data[$rel['related']]['table'];
        if (!$table2) continue;
        
        $type = $rel['type'];
        if ($type == 'BelongsTo') {
            $fk = $rel['foreignKey'];
            if (!$fk || strpos($fk, '_id') === false) {
                 // rough guess
                 $parts = explode('\\', $rel['related']);
                 $fk = strtolower(end($parts)) . '_id';
            }
            if (!in_array("ALTER TABLE `$table` ADD FOREIGN KEY (`$fk`) REFERENCES `$table2` (`id`);", $fks)) {
                $fks[] = "ALTER TABLE `$table` ADD FOREIGN KEY (`$fk`) REFERENCES `$table2` (`id`);";
            }
        }
    }
}

$sql .= implode("\n", $fks);

file_put_contents('C:\Users\faisa\.gemini\antigravity-ide\brain\1d48db67-5db4-458f-87cd-ad554db006ec\schema_for_drawio.sql', $sql);
echo "DONE!";
