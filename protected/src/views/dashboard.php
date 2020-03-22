<?php require_once('partials/header.php'); ?>
<h1>Dashboard</h1>
<p>Logged in as <?php echo $SESSION['username']; ?></p>
<form action="/logout" method="POST">
    <input type="text" name="csrf" id="csrf" value="<?php echo $SESSION['csrf']; ?>" hidden>
    <input type="submit" name="submit" value="Log Out">
</form>
<?php require_once('partials/footer.php');