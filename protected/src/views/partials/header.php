<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title><?php echo $SITE_NAME; ?></title>
    <link rel="stylesheet" href="<?php echo $SITE_URL; ?>/css/app.css">
</head>
<body>

<?php if($view_errors): ?>
<div role="alert">
    <?php foreach($view_errors as $error): ?>
    <p><?php echo $this->raw($error); ?></p>
    <?php endforeach; ?>
</div>
<?php endif; ?>

<?php if($view_confirmations): ?>
<div role="alert">
    <?php foreach($view_confirmations as $confirmation): ?>
    <p><?php echo $this->raw($confirmation); ?></p>
    <?php endforeach; ?>
</div>
<?php endif; ?>