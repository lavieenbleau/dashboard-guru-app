<?php

namespace App\Services;

use App\Models\ExerciseItem;

class EvaluationService
{
    /**
     * Evaluate a student's answer against an exercise item.
     * 
     * @param ExerciseItem $item The question item
     * @param mixed $studentAnswer The answer submitted by the student (can be string or array)
     * @return bool True if correct, false otherwise
     */
    public function evaluateAnswer(ExerciseItem $item, $studentAnswer): bool
    {
        $correctAnswers = $item->answer;
        
        // Ensure correctAnswers is an array and not empty
        if (!is_array($correctAnswers) || empty($correctAnswers)) {
            return false;
        }

        $modelId = (int)$item->exercise_model_id;

        // List of models that use choice-based answers (keys)
        // Assuming: 1=Pilihan Ganda, 2=Pilihan Ganda Banyak, 5=Iya/Tidak, 6=Pernyataan
        $choiceModels = [1, 2, 5, 6];
        
        // List of models that use text-based answers
        // Assuming: 3=Jawaban Singkat/Isian, 4=Uraian, 7=Argumen
        $textModels = [3, 4, 7];

        if (in_array($modelId, $choiceModels)) {
            // Choice models
            if (is_array($studentAnswer)) {
                // Multiple choices selected
                $diff1 = array_diff($studentAnswer, $correctAnswers);
                $diff2 = array_diff($correctAnswers, $studentAnswer);
                return empty($diff1) && empty($diff2);
            } else {
                // Single choice selected
                return in_array($studentAnswer, $correctAnswers);
            }
        } elseif (in_array($modelId, $textModels)) {
            // Text-based models
            $correctText = $correctAnswers[0] ?? '';
            if (is_string($studentAnswer) && is_string($correctText)) {
                // Case-insensitive comparison with whitespace trimmed
                return strtolower(trim($studentAnswer)) === strtolower(trim($correctText));
            }
        }
        
        // Default false if model is unknown or format is invalid
        return false;
    }
}
