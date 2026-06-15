<?php
$content = file_get_contents('app/Http/Controllers/Guru/RekapNilaiController.php');

// 1. Update showLesson to include detail lists in $rekapData
$showLessonRegex = '/public function showLesson\(.*?return view\(\'guru\.rekap-nilai\.show-class\', \$viewData\);\n    \}/s';
preg_match($showLessonRegex, $content, $matches);
if (!empty($matches)) {
    $m = $matches[0];
    
    // We need to build $detailColumns again so we can list tasks/exercises for the modal
    $replacement = <<<'PHP'
        // Build unique columns for Detail Modal Tab
        $uniquePosts = Post::whereIn('id', $validPostIds)->orderBy('created_at')->get();
        $uniqueExercises = Exercise::whereIn('id', $validExerciseIds)->with('exerciseType')->orderBy('created_at')->get();

        $detailColumns = [
            'tasks' => collect(),
            'akm' => collect(),
            'uh' => collect(),
            'pts' => collect(),
            'pas' => collect()
        ];

        foreach ($uniquePosts as $p) {
            $detailColumns['tasks']->push(['id' => $p->id, 'title' => $p->title]);
        }
        foreach ($uniqueExercises as $ex) {
            $typeName = strtolower($ex->exerciseType->name ?? '');
            $item = ['id' => $ex->id, 'title' => $ex->title];
            if (str_contains($typeName, 'akm')) {
                $detailColumns['akm']->push($item);
            } elseif (str_contains($typeName, 'ulangan harian')) {
                $detailColumns['uh']->push($item);
            } elseif (str_contains($typeName, 'pts')) {
                $detailColumns['pts']->push($item);
            } elseif (str_contains($typeName, 'pas')) {
                $detailColumns['pas']->push($item);
            }
        }

        $allTasks = Task::whereIn('student_id', $students->pluck('id'))
PHP;
    $m = preg_replace('/\$allTasks = Task::whereIn\(\'student_id\', \$students->pluck\(\'id\'\)\)/s', $replacement, $m);

    // Now insert the collection of $studentDetails
    $replacementLoop = <<<'PHP'
            $studentDetails = [
                'tasks' => [],
                'akm' => [],
                'uh' => [],
                'pts' => [],
                'pas' => []
            ];

            foreach ($sTasks as $t) {
                if (!is_null($t->point)) {
                    $tugas['sum'] += $t->point;
                    $tugas['count']++;
                    $studentDetails['tasks'][$t->post_id] = $t->point;
                }
            }

            foreach ($sExPoints as $ex) {
                if (!is_null($ex->exercise_point)) {
                    $typeName = $ex->exercise && $ex->exercise->exerciseType ? strtolower($ex->exercise->exerciseType->name) : '';
                    if (str_contains($typeName, 'akm')) {
                        $akm['sum'] += $ex->exercise_point;
                        $akm['count']++;
                        $studentDetails['akm'][$ex->exercise_id] = $ex->exercise_point;
                    } elseif (str_contains($typeName, 'ulangan harian')) {
                        $uh['sum'] += $ex->exercise_point;
                        $uh['count']++;
                        $studentDetails['uh'][$ex->exercise_id] = $ex->exercise_point;
                    } elseif (str_contains($typeName, 'pts')) {
                        $pts['sum'] += $ex->exercise_point;
                        $pts['count']++;
                        $studentDetails['pts'][$ex->exercise_id] = $ex->exercise_point;
                    } elseif (str_contains($typeName, 'pas')) {
                        $pas['sum'] += $ex->exercise_point;
                        $pas['count']++;
                        $studentDetails['pas'][$ex->exercise_id] = $ex->exercise_point;
                    }
                }
            }
PHP;
    
    // Replace the two loops for $sTasks and $sExPoints with the one above
    $m = preg_replace('/foreach \(\$sTasks as \$t\) \{.*?\} \}\}/s', $replacementLoop, $m);
    
    // Replace the array append for $rekapData[]
    $rekapDataAppendOld = '/\$rekapData\[\] = \[\s*\'student\' => \$student,\s*\'tugas\' => \[\'avg\' => \$rataTugas, \'count\' => \$tugas\[\'count\'\]\],\s*\'akm\' => \[\'avg\' => \$rataAKM, \'count\' => \$akm\[\'count\'\]\],\s*\'uh\' => \[\'avg\' => \$rataUH, \'count\' => \$uh\[\'count\'\]\],\s*\'pts\' => \[\'avg\' => \$rataPTS, \'count\' => \$pts\[\'count\'\]\],\s*\'pas\' => \[\'avg\' => \$rataPAS, \'count\' => \$pas\[\'count\'\]\],\s*\'nilai_akhir\' => \$nilaiAkhir\s*\];/s';
    
    $rekapDataAppendNew = <<<'PHP'
            $rekapData[] = [
                'student' => $student,
                'tugas' => ['avg' => $rataTugas, 'count' => $tugas['count']],
                'akm' => ['avg' => $rataAKM, 'count' => $akm['count']],
                'uh' => ['avg' => $rataUH, 'count' => $uh['count']],
                'pts' => ['avg' => $rataPTS, 'count' => $pts['count']],
                'pas' => ['avg' => $rataPAS, 'count' => $pas['count']],
                'nilai_akhir' => $nilaiAkhir,
                'detail' => $studentDetails
            ];
PHP;
    $m = preg_replace($rekapDataAppendOld, $rekapDataAppendNew, $m);
    
    $viewDataOld = '/\'lowestScore\' => \$gradedStudents > 0 \? \$lowestScore : 0\s*\];/s';
    $viewDataNew = <<<'PHP'
'lowestScore' => $gradedStudents > 0 ? $lowestScore : 0,
            'detailColumns' => $detailColumns
        ];
PHP;
    $m = preg_replace($viewDataOld, $viewDataNew, $m);

    $content = str_replace($matches[0], $m, $content);
}

// 2. Remove dead methods
$content = preg_replace('/public function getStudentDetailAjax\(.*?\n    \}/s', '', $content);
$content = preg_replace('/\/\*\*.*?@deprecated Digantikan oleh AJAX Modal pada v2\.1.*?\*\/\s*public function showStudent\(.*?\n    \}/s', '', $content);
$content = preg_replace('/public function downloadStudentPdf\(.*?\n    \}/s', '', $content);

file_put_contents('app/Http/Controllers/Guru/RekapNilaiController.php', $content);
echo "Modification complete.\n";
