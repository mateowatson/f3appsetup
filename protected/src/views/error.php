<?php require_once('partials/header.php'); ?>

<h1>Error</h1>

<?php if($SITE_ENV === 'development'): ?>
<h2><?php echo $ERROR['text']; ?></h2>
<p>Error code: <?php echo $ERROR['code']; ?></p>
<?php if($ERROR['trace']): ?>
<pre><?php echo $ERROR['trace']; ?></pre>
<?php endif; ?>
<?php else: ?>
<h2>Error code: <?php echo $ERROR['code']; ?></h2>
<?php endif; ?>

<?php require_once('partials/footer.php'); ?>