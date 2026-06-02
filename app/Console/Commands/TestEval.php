<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ExerciseItem;
use App\Services\EvaluationService;

class TestEval extends Command
{
    protected $signature = 'test:eval';

    public function handle()
    {
        $service = new EvaluationService();

        $item = new ExerciseItem();
        $item->exercise_model_id = 1;
        $item->answer = ["B"];

        $this->info("Pilihan Ganda - B: " . ($service->evaluateAnswer($item, "B") ? "CORRECT" : "WRONG"));
        $this->info("Pilihan Ganda - C: " . ($service->evaluateAnswer($item, "C") ? "CORRECT" : "WRONG"));

        $item->exercise_model_id = 2;
        $item->answer = ["A", "C"];
        $this->info("PG-Banyak-[A, C]: " . ($service->evaluateAnswer($item, ["A", "C"]) ? "CORRECT" : "WRONG"));
        $this->info("PG-Banyak-[A]: " . ($service->evaluateAnswer($item, ["A"]) ? "CORRECT" : "WRONG"));

        $item->exercise_model_id = 3;
        $item->answer = ["Indonesia"];
        $this->info("Isian-'indonesia': " . ($service->evaluateAnswer($item, "indonesia") ? "CORRECT" : "WRONG"));
        $this->info("Isian-' Indonesia ': " . ($service->evaluateAnswer($item, " Indonesia ") ? "CORRECT" : "WRONG"));
        $this->info("Isian-'Malaysia': " . ($service->evaluateAnswer($item, "Malaysia") ? "CORRECT" : "WRONG"));
    }
}
