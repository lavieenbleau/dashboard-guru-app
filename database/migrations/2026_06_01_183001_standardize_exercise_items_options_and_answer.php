<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Add new columns
        Schema::table('exercise_items', function (Blueprint $table) {
            $table->json('options')->nullable()->after('question');
        });

        // 2. Migrate existing data
        $items = DB::table('exercise_items')->get();
        
        foreach ($items as $item) {
            $newOptions = [];
            $newAnswer = [];
            
            // Process options (selection)
            if ($item->selection) {
                $oldSelection = json_decode($item->selection, true);
                if (is_array($oldSelection)) {
                    foreach ($oldSelection as $key => $text) {
                        if ($text !== null && $text !== '') {
                            $newOptions[] = [
                                'key' => $key,
                                'text' => $text
                            ];
                        }
                    }
                }
            }
            
            // Process answer
            if ($item->answer !== null && $item->answer !== '') {
                // If answer is already a JSON array, json_decode will return array
                $decodedAnswer = json_decode($item->answer, true);
                
                if (is_array($decodedAnswer)) {
                    // It was already a JSON array, e.g. ["A"]
                    $newAnswer = $decodedAnswer;
                } else {
                    // If it was a plain string "A", or "Indonesia"
                    // If multiple choice many, it might be "A,C"
                    if (str_contains($item->answer, ',')) {
                        $parts = array_map('trim', explode(',', $item->answer));
                        $newAnswer = array_values(array_filter($parts));
                    } else {
                        $newAnswer = [$item->answer];
                    }
                }
            }
            
            DB::table('exercise_items')
                ->where('id', $item->id)
                ->update([
                    'options' => empty($newOptions) ? null : json_encode($newOptions),
                    'answer' => empty($newAnswer) ? null : json_encode($newAnswer),
                ]);
        }
        
        // 3. Drop old selection column
        Schema::table('exercise_items', function (Blueprint $table) {
            $table->dropColumn('selection');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('exercise_items', function (Blueprint $table) {
            $table->json('selection')->nullable()->after('question');
        });
        
        // Restore data
        $items = DB::table('exercise_items')->get();
        
        foreach ($items as $item) {
            $oldSelection = [];
            if ($item->options) {
                $optionsArray = json_decode($item->options, true);
                if (is_array($optionsArray)) {
                    foreach ($optionsArray as $opt) {
                        if (isset($opt['key']) && isset($opt['text'])) {
                            $oldSelection[$opt['key']] = $opt['text'];
                        }
                    }
                }
            }
            
            $oldAnswer = null;
            if ($item->answer) {
                $ansArray = json_decode($item->answer, true);
                if (is_array($ansArray)) {
                    $oldAnswer = implode(',', $ansArray);
                }
            }
            
            DB::table('exercise_items')
                ->where('id', $item->id)
                ->update([
                    'selection' => empty($oldSelection) ? null : json_encode($oldSelection),
                    'answer' => $oldAnswer,
                ]);
        }
        
        Schema::table('exercise_items', function (Blueprint $table) {
            $table->dropColumn('options');
        });
    }
};
