<?php
$cols = Schema::getColumnListing('exercises');
echo implode(',', $cols);
