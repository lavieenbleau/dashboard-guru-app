<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CleanupDuplicates extends Command
{
    protected $signature = 'cleanup:duplicates';
    protected $description = 'Cleanup duplicate records in database';

    public function handle()
    {
        $this->info('Starting cleanup...');
        
        // Cleanup duplicate exercise_types
        $this->info('Cleaning exercise_types...');
        $exerciseTypes = DB::table('exercise_types')
            ->select('kode', DB::raw('MIN(id) as min_id'))
            ->groupBy('kode')
            ->get();
        
        foreach ($exerciseTypes as $type) {
            DB::table('exercise_types')
                ->where('kode', $type->kode)
                ->where('id', '!=', $type->min_id)
                ->delete();
        }
        
        // Cleanup duplicate classrooms
        $this->info('Cleaning duplicate classrooms...');
        $classrooms = DB::table('classrooms')
            ->select('serial_id', 'name', DB::raw('MIN(id) as min_id'))
            ->groupBy('serial_id', 'name')
            ->havingRaw('COUNT(*) > 1')
            ->get();
        
        foreach ($classrooms as $classroom) {
            $deleted = DB::table('classrooms')
                ->where('serial_id', $classroom->serial_id)
                ->where('name', $classroom->name)
                ->where('id', '!=', $classroom->min_id)
                ->delete();
            
            if ($deleted > 0) {
                $this->info("Deleted {$deleted} duplicate(s) of classroom: {$classroom->name}");
            }
        }
        
        $this->info('Cleanup completed!');
        return 0;
    }
}
