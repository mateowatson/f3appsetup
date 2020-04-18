<?php require_once('partials/header.php'); ?>
<h1>Reset Password</h1>

<form action="/reset-password" method="POST">
    <input type="text" name="csrf" id="csrf" value="<?php echo $SESSION['csrf']; ?>" hidden>

    <label>Enter your username: 
    <input type="text" name="username" value="<?php echo $default_username_value; ?>">
    </label><br><br>

    <label>Enter the reset code we emailed you: 
    <input type="text" name="resetcode" value="<?php echo $default_resetcode_value; ?>">
    </label><br><br>

    <label>Enter your desired new password: 
    <input type="password" name="password">
    </label><br><br>

    <input type="submit" name="submit" value="Submit">
</form>
<?php require_once('partials/footer.php');