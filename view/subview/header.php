<?php

namespace NoCon\Framework;

/**
 * view header processing before content starts
 */

// set helper variables
Router::$ARGS['PAGE_TITLE'] = Config::get('application', 'title') . ' - ' . ucwords(preg_replace(array('|_|', '|-|'), ' ', Router::getViewName()));
Router::$ARGS['SITE_URL'] = Config::get('framework', 'siteURL');
Router::$ARGS['CSS_URL'] = Config::get('framework', 'cssURL');
Router::$ARGS['IMG_URL'] = Config::get('framework', 'imageURL');
Router::$ARGS['JS_URL'] = Config::get('framework', 'jsURL');
