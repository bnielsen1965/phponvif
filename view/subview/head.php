<?php namespace NoCon\Framework; // namespace needed to access classes ?>
<!DOCTYPE html>
<html ng-app="cameraApp">
<head>

<title><?php echo Router::$ARGS['PAGE_TITLE']; ?></title>

<link rel="shortcut icon" href="<?php echo Router::$ARGS['SITE_URL']; ?>favicon.ico" />
<meta charset="UTF-8">

<link href="<?php echo Router::$ARGS['CSS_URL']; ?>styles.css" rel="stylesheet">

</head>
<body ng-controller="CameraController">

    <!-- header start -->
    <div>
        <h1 class="title">PHP/ONVIF</h1>
    </div>
    <!-- header end -->

<?php //Router::includeView('subview/nav');