<?php require_once('partials/header.php'); ?>
<h1>Log In</h1>

<form action="/login" method="POST">
    <label>Username (required): 
        <input type="text" name="username">
    </label><br><br>

    <label>Password (required):
        <input type="password" name="password">
    </label><br><br>

    <input type="submit" name="submit" value="Log In">
</form>
<?php require_once('partials/footer.php');