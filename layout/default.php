<?php

namespace NoCon\Framework;

Router::includeView('subview/header'); // start the page header
Router::includeView('subview/head'); // load the content head
Router::includeView(); // load the current view content
Router::includeView('subview/foot'); // load the content footer
