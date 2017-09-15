<!DOCTYPE html>
<html>
  <head>
    <title>Unearthed 2017 | Datacake</title>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="description" content="">
    <?php $this->loadPartialView("./templates/_mobile_meta.php"); ?>
    <style>
    html, body { margin:0; padding:0; overflow:hidden;}
    </style>
    <!-- Assets we'll need - Three.js, controls, and the webVR manager and polyfill -->
    <?php $this->loadPartialView("./templates/_webvr_three.php"); ?>
  </head>
  <body>
    <?php echo $view; ?>
  </body>
</html>
