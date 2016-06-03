<?php

namespace NoCon\Framework;

/*
 * This determines the overall layout of the page by controlling the
 * sequence in which views that make up the page are loaded.
 */

// use view layout if provided
if (file_exists(Config::get('framework', 'layoutPath') . 'viewlayout/' . Router::getViewName() . '.php')) {
    include Config::get('framework', 'layoutPath') .  'viewlayout/' . Router::getViewName() . '.php';
}
else {
    // use the default layout
    include Config::get('framework', 'layoutPath') . 'default.php';
}
