<?php

function debug(mixed $foo, bool $exit = true): void {
    echo '<pre style="background-color: rgb(0,0,18); color: rgb(0,175,0); padding: 7px; font-size: 17px; margin: 0;">';
    var_dump($foo);
    echo '</pre>';
    if($exit) { exit; }
}

// Escapa / Sanitizar el HTML
function s($html) : string {
    $s = htmlspecialchars($html);
    return $s;
}