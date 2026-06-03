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

$mermaid = "```mermaid\nerDiagram\n";
$plantuml = "```plantuml\n@startuml\nhide circle\nskinparam linetype ortho\n\n";

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
        
        $type = $rel['type'];
        
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
            $mermaid .= "    {$table1} }o--o{ {$table2} : \"belongs_to_many\"\n";
            $plantuml .= "{$table1} }o--o{ {$table2} : \"\"\n";
        }
    }
}
$mermaid .= "```\n";
$plantuml .= "@enduml\n```\n";

$markdown = "# Analisis ERD LMS SCI Media Online (Aktual)\n\n";
$markdown .= "## 1. Daftar Entitas & Atribut\n\n";
foreach ($data as $model => $info) {
    if (!$info['table']) continue;
    $markdown .= "### Tabel: `{$info['table']}`\n";
    if (isset($info['primaryKey']) && $info['primaryKey']) {
        $markdown .= "- **PK**: `{$info['primaryKey']}`\n";
    }
    $fks = [];
    $attrs = [];
    foreach ($info['fillable'] as $field) {
        if (strpos($field, '_id') !== false) {
            $fks[] = $field;
        } else {
            $attrs[] = $field;
        }
    }
    if (count($fks) > 0) {
        $markdown .= "- **FK**: " . implode(', ', array_map(function($f){return "`$f`";}, $fks)) . "\n";
    }
    if (count($attrs) > 0) {
        $markdown .= "- **Atribut**: " . implode(', ', array_map(function($a){return "`$a`";}, $attrs)) . "\n\n";
    }
}

$markdown .= "## 2. Penjelasan Relasi Antar Entitas\n\n";
$markdown .= "Berdasarkan analisis source code (Model Eloquent) secara langsung, berikut adalah relasi (kardinalitas) yang digunakan sistem saat ini:\n\n";
foreach ($data as $model => $info) {
    $table1 = $info['table'];
    if (!$table1) continue;
    foreach ($info['relations'] as $relName => $rel) {
        if (!isset($data[$rel['related']])) continue;
        $table2 = $data[$rel['related']]['table'];
        if (!$table2) continue;
        $type = $rel['type'];
        $markdown .= "- **{$table1}** ke **{$table2}**: `{$type}`\n";
    }
}

$markdown .= "\n## 3. Diagram ERD (Mermaid)\n\n" . $mermaid . "\n\n";
$markdown .= "## 4. Diagram ERD (PlantUML)\n\n" . $plantuml . "\n\n";
$markdown .= "## 5. Rekomendasi Penyederhanaan untuk Tugas Akhir\n\n";
$markdown .= "Untuk keperluan laporan Tugas Akhir, Anda dapat menyederhanakan diagram dengan fokus pada entitas utama (core entities) seperti `users`, `students`, `classrooms`, `serials`, `lessons`, `exercises`, dan `reports`. Entitas log seperti `serial_logs`, `quiz_activity_logs`, `admin_activity_logs`, atau tabel *chat/meeting* (`cs_messages`, `online_meetings`) dapat dihilangkan dari ERD utama untuk menjaga 가독성 (keterbacaan), kecuali jika fitur-fitur tersebut merupakan fokus utama dari judul/pembahasan TA Anda.\n";

file_put_contents('C:\Users\faisa\.gemini\antigravity-ide\brain\1d48db67-5db4-458f-87cd-ad554db006ec\erd_analysis.md', $markdown);
echo "DONE!";
