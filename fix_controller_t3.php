<?php
$content = file_get_contents('app/Http/Controllers/Guru/RekapNilaiController.php');
// The array building was missed. We will insert it right before `$rekapData[] = [`
$search = "\$rekapData[] = [";
$replace = <<<PHP
            // Populate cleanStudentDetails
            \$cleanStudentDetails[] = [
                'id' => \$student->id,
                'name' => \$student->name,
                'nis' => \$student->nis,
                'nilai_akhir' => \$nilaiAkhir,
                'tugas' => \$studentDetails['tasks'] ?? [],
                'akm' => \$studentDetails['akm'] ?? [],
                'uh' => \$studentDetails['uh'] ?? [],
                'pts' => \$studentDetails['pts'] ?? [],
                'pas' => \$studentDetails['pas'] ?? []
            ];

            \$rekapData[] = [
PHP;

if (strpos($content, '$cleanStudentDetails[] =') === false) {
    $content = str_replace($search, $replace, $content);
    file_put_contents('app/Http/Controllers/Guru/RekapNilaiController.php', $content);
    echo "Controller patched successfully.\n";
} else {
    echo "Controller already patched.\n";
}
