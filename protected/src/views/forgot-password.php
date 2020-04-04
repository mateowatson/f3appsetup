<?php require_once('partials/header.php'); ?>
<h1>Forgot Password</h1>

<form action="/forgot-password" method="POST">
    <input type="text" name="csrf" id="csrf" value="<?php echo $SESSION['csrf']; ?>" hidden>

    <?php if($EMAIL_ENABLED): ?>
    <label>Enter the email address at which you would like to receive the reset
    link: 
    <input type="text" name="email">
    </label><br><br>

    <input type="submit" name="submit" value="Submit">
    <?php endif; ?>
</form>
<?php require_once('partials/footer.php');