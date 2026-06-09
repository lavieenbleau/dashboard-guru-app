import re

with open('app/Http/Controllers/Guru/TugasController.php', 'r', encoding='utf-8') as f:
    code = f.read()

# Fix listByLesson
code = re.sub(
    r'(\$tugas = Post::where.*?->where\(\'is_task\', 1\)\s*).*?(\s*\$classrooms = Classroom::where)',
    r'\1->latest()->get();\n\n\2',
    code,
    flags=re.DOTALL
)

# Fix store
code = re.sub(
    r'(\$attachmentPath = null;.*?if \(\$request->hasFile\(\'attachment\'\)\) \{.*?\})(\s*// Create tugas post.*?return redirect)',
    r'\1\n        // Create tugas post\n        Post::create([\n            \'serial_id\' => $serial->id,\n            \'user_id\' => auth()->id(),\n            \'mapel_id\' => $lesson->mapel_id,\n            \'title\' => $request->title,\n            \'description\' => $request->description,\n            \'slug\' => Str::slug($request->title) . \'-\' . time(),\n            \'link\' => $request->link,\n            \'attachment\' => $attachmentPath,\n            \'deadline\' => $request->deadline,\n            \'category\' => [\'lesson_id\' => $lesson->id],\n            \'shared_to_classes\' => $request->classroom_ids,\n            \'is_task\' => 1,\n        ]);\n\n        return redirect',
    code,
    flags=re.DOTALL
)

# Fix edit
code = re.sub(
    r'(\$task = Post::findOrFail\(\$id\);\s*\$classrooms = Classroom::where\(\'serial_id\', \$serial->id\)->orderBy\(\'name\'\)->get\(\);\s*).*?(\s*return view\(\'guru\.tugas\.edit\')',
    r'\1$sharedClasses = $task->shared_to_classes ?? [];\2',
    code,
    flags=re.DOTALL
)

# Fix update
code = re.sub(
    r'(\$task = Post::findOrFail\(\$id\);\s*// Handle attachment.*?if \(\$request->hasFile\(\'attachment\'\)\) \{.*?\} elseif \(\$request->remove_attachment\) \{.*?\})(\s*// Get all related tasks.*?return redirect)',
    r'\1\n        // Update task\n        $task->update([\n            \'title\' => $request->title,\n            \'description\' => $request->description,\n            \'link\' => $request->link,\n            \'deadline\' => $request->deadline,\n            \'attachment\' => $attachmentPath,\n            \'shared_to_classes\' => $request->classroom_ids,\n        ]);\n\n        return redirect',
    code,
    flags=re.DOTALL
)

# Fix destroy
code = re.sub(
    r'(\$task = Post::where\(\'is_task\', 1\)->findOrFail\(\$id\);\s*).*?(\s*return redirect)',
    r'\1// Delete attachment file if exists\n        if ($task->attachment && \Storage::disk(\'public\')->exists($task->attachment)) {\n            \Storage::disk(\'public\')->delete($task->attachment);\n        }\n        $task->forceDelete();\2',
    code,
    flags=re.DOTALL
)

# Fix updateClassroom
code = re.sub(
    r'(\$request->validate\(\[\s*\'classroom_ids\'.*?\]\);\s*).*?(\s*return back)',
    r'\1$task->update([\'shared_to_classes\' => $request->classroom_ids]);\2',
    code,
    flags=re.DOTALL
)

with open('app/Http/Controllers/Guru/TugasController.php', 'w', encoding='utf-8') as f:
    f.write(code)
