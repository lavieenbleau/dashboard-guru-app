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

echo json_encode($output, JSON_PRETTY_PRINT);
