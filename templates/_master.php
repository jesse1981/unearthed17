<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Unearthed 2017 | Datacake</title>
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes" />
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent" />
    <style>
    html, body { margin:0; padding:0; overflow:hidden;}
    </style>
    <!-- Assets we'll need - Three.js, controls, and the webVR manager and polyfill -->
    <script src="js/three.config.js"></script>
    <script src="js/es6-promise.min.js"></script>
    <script src="js/three.min.js"></script>
    <script src="js/three.flycontrols.js"></script>
    <script src="js/three.terrainloader.js"></script>
    <script src="js/VRControls.js"></script>
    <script src="js/VREffect.js"></script>
    <script src="js/webvr-polyfill.js"></script>
    <script src="js/webvr-manager.js"></script>
  </head>
  <body>
    <?php echo $view; ?>
  </body>
</html>
