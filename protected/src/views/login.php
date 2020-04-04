<?php require_once('partials/header.php'); ?>
<h1>Log In</h1>

<form action="/login" method="POST">
    <input type="text" name="csrf" id="csrf" value="<?php echo $SESSION['csrf']; ?>" hidden>

    <?php if($USERSIGNUP === 'email'): ?>
    <label>Email (required): 
    <input type="text" name="email">
    </label><br><br>
    <?php else: ?>
    <label>Username (required): 
        <input type="text" name="username">
    </label><br><br>
    <?php endif; ?>

    <label>Password (required):
        <input type="password" name="password">
    </label><br><br>

    <input type="submit" name="submit" value="Log In"><br><br>

    <?php if($EMAIL_ENABLED): ?>
    <a href="<?php echo $SITE_URL; ?>/forgot-password">Forgot Password?</a>
    <?php endif; ?>
</form>
<?php require_once('partials/footer.php');