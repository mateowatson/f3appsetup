<?php require_once('partials/header.php'); ?>
<h1>Resend Email Confirmation</h1>

<form action="/confirm" method="POST">
    <input type="text" name="csrf" id="csrf" value="<?php echo $SESSION['csrf']; ?>" hidden>

    <?php if($USERSIGNUP !== 'email'): ?>
    <label>Username (required): 
        <input type="text" name="username">
    </label><br><br>
    <?php endif; ?>

    <label>Email (required): 
    <input type="text" name="email">
    </label><br><br>

    <input type="submit" name="submit" value="Resend Confirmation">
</form>
<?php require_once('partials/footer.php');