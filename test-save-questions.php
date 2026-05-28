<!DOCTYPE html>
<html>
<head>
    <title>Test AI Save Questions</title>
    <meta charset="utf-8">
    <style>
        body { font-family: Arial; padding: 20px; }
        .section { margin: 20px 0; padding: 15px; border: 1px solid #ccc; border-radius: 5px; }
        .success { background: #d4edda; border-color: #c3e6cb; }
        .error { background: #f8d7da; border-color: #f5c6cb; }
        pre { background: #f8f9fa; padding: 10px; overflow-x: auto; }
        .btn { display: inline-block; padding: 10px 20px; background: #007bff; color: white; text-decoration: none; border-radius: 4px; margin: 5px; }
        .btn:hover { background: #0056b3; }
    </style>
</head>
<body>
    <h1>🧪 Test AI Save Questions</h1>
    
    <?php
    require __DIR__ . '/vendor/autoload.php';
    
    use Illuminate\Support\Facades\DB;
    
    $app = require_once __DIR__ . '/bootstrap/app.php';
    $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
    $kernel->bootstrap();
    
    echo "<div class='section'>";
    echo "<h2>1. Check Session Data</h2>";
    
    if (isset($_SESSION['ai_generated_questions'])) {
        echo "<div class='success'>";
        echo "<strong>✅ Session data exists</strong><br>";
        echo "Question count: " . count($_SESSION['ai_generated_questions']['questions']) . "<br>";
        echo "<pre>" . print_r($_SESSION['ai_generated_questions'], true) . "</pre>";
        echo "</div>";
    } else {
        echo "<div class='error'>";
        echo "<strong>❌ No session data found</strong><br>";
        echo "Session needs to be populated first by generating questions.";
        echo "</div>";
    }
    echo "</div>";
    
    echo "<div class='section'>";
    echo "<h2>2. Check Recent Exercises</h2>";
    
    try {
        $recentExercises = DB::table('exercises')
            ->where('is_admin', 0)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get(['id', 'title', 'lesson_id', 'exercise_type_id', 'created_at']);
        
        echo "<div class='success'>";
        echo "<strong>✅ Recent Custom Exercises (is_admin = 0):</strong><br>";
        echo "Total count: " . $recentExercises->count() . "<br>";
        
        if ($recentExercises->count() > 0) {
            echo "<table border='1' cellpadding='5' style='border-collapse: collapse; width: 100%; margin-top: 10px;'>";
            echo "<tr><th>ID</th><th>Title</th><th>Lesson ID</th><th>Type ID</th><th>Created At</th></tr>";
            foreach ($recentExercises as $ex) {
                echo "<tr>";
                echo "<td>{$ex->id}</td>";
                echo "<td>{$ex->title}</td>";
                echo "<td>{$ex->lesson_id}</td>";
                echo "<td>{$ex->exercise_type_id}</td>";
                echo "<td>{$ex->created_at}</td>";
                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo "<p><em>No custom exercises found yet.</em></p>";
        }
        echo "</div>";
    } catch (Exception $e) {
        echo "<div class='error'>";
        echo "<strong>❌ Error:</strong> " . $e->getMessage();
        echo "</div>";
    }
    
    echo "</div>";
    
    echo "<div class='section'>";
    echo "<h2>3. Check Exercise Items</h2>";
    
    try {
        $recentItems = DB::table('exercise_items')
            ->where('is_user', 1)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get(['id', 'exercise_id', 'question', 'answer', 'created_at']);
        
        echo "<div class='success'>";
        echo "<strong>✅ Recent Custom Exercise Items (is_user = 1):</strong><br>";
        echo "Total count: " . $recentItems->count() . "<br>";
        
        if ($recentItems->count() > 0) {
            echo "<table border='1' cellpadding='5' style='border-collapse: collapse; width: 100%; margin-top: 10px;'>";
            echo "<tr><th>ID</th><th>Exercise ID</th><th>Question</th><th>Answer</th><th>Created At</th></tr>";
            foreach ($recentItems as $item) {
                $questionPreview = mb_substr($item->question, 0, 100) . '...';
                echo "<tr>";
                echo "<td>{$item->id}</td>";
                echo "<td>{$item->exercise_id}</td>";
                echo "<td>{$questionPreview}</td>";
                echo "<td>{$item->answer}</td>";
                echo "<td>{$item->created_at}</td>";
                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo "<p><em>No custom exercise items found yet.</em></p>";
        }
        echo "</div>";
    } catch (Exception $e) {
        echo "<div class='error'>";
        echo "<strong>❌ Error:</strong> " . $e->getMessage();
        echo "</div>";
    }
    
    echo "</div>";
    
    echo "<div class='section'>";
    echo "<h2>4. Check Laravel Logs</h2>";
    
    $logFile = __DIR__ . '/storage/logs/laravel.log';
    if (file_exists($logFile)) {
        $logContent = file_get_contents($logFile);
        $aiLogs = [];
        
        // Extract AI-related log entries
        $lines = explode("\n", $logContent);
        foreach ($lines as $line) {
            if (stripos($line, 'AI Save') !== false || 
                stripos($line, 'Exercise created') !== false ||
                stripos($line, 'ExerciseItem created') !== false ||
                stripos($line, 'Lesson found') !== false) {
                $aiLogs[] = $line;
            }
        }
        
        if (count($aiLogs) > 0) {
            echo "<div class='success'>";
            echo "<strong>✅ Found AI-related logs:</strong><br>";
            echo "<pre>" . implode("\n", array_slice($aiLogs, -20)) . "</pre>";
            echo "</div>";
        } else {
            echo "<div class='error'>";
            echo "<strong>⚠️ No AI-related logs found</strong><br>";
            echo "This means the save method hasn't been called yet or logging isn't working.";
            echo "</div>";
        }
    } else {
        echo "<div class='error'>";
        echo "<strong>❌ Log file not found</strong>";
        echo "</div>";
    }
    
    echo "</div>";
    
    echo "<div class='section'>";
    echo "<h2>Next Steps:</h2>";
    echo "<ol>";
    echo "<li><a href='/aplikasi/1/soal/ai-generator' class='btn'>Generate Questions</a></li>";
    echo "<li>After generating, click 'Preview'</li>";
    echo "<li>Then click 'Simpan ke Bank Soal'</li>";
    echo "<li><a href='/test-save-questions.php' class='btn'>Refresh This Page</a> to see results</li>";
    echo "</ol>";
    echo "</div>";
    ?>
</body>
</html>
