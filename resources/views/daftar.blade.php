<html>
    <head>
        <title>login</title><link rel="stylesheet" href="style.css">      

    </head>
    <body color="#effff4">
        <form action="prosesdaftar" method="post">
            @csrf
            <label>Username</label><br>
            <input type="text" name="username"><br>
            <label>Password</label><br>
            <input type="password" name="password" id="password"/><br>
            <label>Konfirmasi Password</label><br>
            <input type="password" name="confirm_password" id="password"/><br><br>
            <button type="submit">Register</button><br><br>
            <p>Sudah punya akun? <a href="login">Log in</a></p>
        </form>
    </body>
</html>