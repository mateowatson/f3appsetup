<?php require_once('partials/header.php'); ?>
<h1>Sign Up</h1>

<form action="/signup" method="POST">
    <label>Username (required): 
        <input type="text" name="username">
    </label><br><br>

    <?php if($USERSIGNUP === 'optional'): ?>
        <label>Email (optional): 
            <input type="email" name="email">
            <br>
            Necessary for password recovery.
        </label><br><br>
    <?php elseif($USERSIGNUP === 'email'): ?>
        <label>Email (required): 
            <input type="email" name="email">
        </label><br><br>
    <?php endif; ?>

    <label>Password (required):
        <input type="password" name="password">
    </label><br><br>

    <input type="submit" name="submit" value="Signup">
</form>
<?php require_once('partials/footer.php');