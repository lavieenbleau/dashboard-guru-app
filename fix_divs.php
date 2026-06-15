<?php
$content = file_get_contents('resources/views/guru/rekap-nilai/show-class.blade.php');
$search = <<<HTML
                            </div>
                          </div>

                          </div>

                    @endif
HTML;
$replace = <<<HTML
                            </div>
                    @endif
HTML;
$content = str_replace($search, $replace, $content);
file_put_contents('resources/views/guru/rekap-nilai/show-class.blade.php', $content);
echo "Fixed divs.\n";
