<?php

namespace App\Services;

class ScoreCategoryResolver
{
    /**
     * Resolve the category name based on the exercise_type_id or source_type.
     * Maps to: 'Tugas', 'AKM', 'Ulangan Harian', 'PTS', 'PAS', 'Soal Tambahan', 'Soal', 'Lainnya'
     *
     * @param mixed $item
     * @param bool $isTask Force to resolve as 'Tugas'
     * @return string
     */
    public static function resolve($item, $isTask = false)
    {
        if ($isTask || (isset($item->source_type) && $item->source_type === 'task')) {
            return 'Tugas';
        }

        $typeId = null;
        if (is_object($item)) {
            if (isset($item->exercise_type_id)) {
                $typeId = $item->exercise_type_id;
            } elseif (isset($item->exercise) && isset($item->exercise->exercise_type_id)) {
                $typeId = $item->exercise->exercise_type_id;
            } elseif (isset($item->exerciseType) && isset($item->exerciseType->id)) {
                $typeId = $item->exerciseType->id;
            }
        } elseif (is_array($item)) {
            if (isset($item['exercise_type_id'])) {
                $typeId = $item['exercise_type_id'];
            }
        }

        switch ($typeId) {
            case 1:
                return 'Ulangan Harian';
            case 2:
                return 'PTS';
            case 3:
                return 'PAS';
            case 4:
                return 'AKM';
            default:
                if (isset($item->lesson_category) && $item->lesson_category == 3) {
                    return 'Soal';
                }
                if (isset($item->is_admin) && $item->is_admin == 0) {
                    return 'Soal Tambahan';
                }
                if (isset($item->source_type) && $item->source_type === 'exercise') {
                    // Fallback for LaporanHarian DB raw objects
                    if (isset($item->lesson_category) && $item->lesson_category != 3) {
                        return 'Soal Tambahan';
                    }
                    return 'Soal';
                }
                return 'Lainnya';
        }
    }

    /**
     * Resolve badge color for UI
     */
    public static function resolveColor($categoryName)
    {
        switch ($categoryName) {
            case 'Tugas':
                return 'success';
            case 'Ulangan Harian':
                return 'primary';
            case 'PTS':
                return 'warning';
            case 'PAS':
                return 'danger';
            case 'AKM':
                return 'info';
            case 'Soal Tambahan':
                return 'info';
            case 'Soal':
            default:
                return 'secondary';
        }
    }
}
