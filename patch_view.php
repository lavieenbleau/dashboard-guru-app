<?php
$content = file_get_contents('resources/views/guru/rekap-nilai/show-class.blade.php');

// 1. Remove Tabs HTML
$content = preg_replace('/<ul class="nav nav-tabs mb-4" id="rekapTab".*?<\/ul>/s', '', $content);
$content = str_replace('<div class="tab-content p-0 shadow-none border-0" id="rekapTabContent">', '', $content);
$content = str_replace('<!-- TAB RINGKASAN -->', '', $content);
$content = preg_replace('/<!-- TAB DETAIL -->.*?<div class="tab-pane fade" id="detail".*?<\/div>\s*<\/div>\s*<\/div>/s', "</div>\n", $content);

// 2. Fix Javascript Object iteration and remove duplicate endsection
$jsSearch = <<<'JS'
                    if (cols && cols.length > 0) {
                        html += '<ul class="list-group list-group-flush">';
                        cols.forEach((col, idx) => {
JS;
$jsReplace = <<<'JS'
                    let colsArray = cols ? Object.values(cols) : [];
                    if (colsArray.length > 0) {
                        html += '<ul class="list-group list-group-flush">';
                        colsArray.forEach((col, idx) => {
JS;
$content = str_replace($jsSearch, $jsReplace, $content);

$content = preg_replace('/@endsection\s*@endsection/s', '@endsection', $content);

file_put_contents('resources/views/guru/rekap-nilai/show-class.blade.php', $content);
echo "View patched.\n";
