<html>
    <head>
        <title>login</title><link rel="stylesheet" href="style.css">      

    </head>
    <body color="#effff4">
        <form action="prosesdaftar" method="post">
            @csrf
            <label>Nama</label><br>
            <input type="text" name="name"><br>
            <label>No Telepon</label><br>
            <input type="text" name="phone"><br>
            <label>Sekolah</label><br>
            <input type="text" name="school_id"><br>
            <label>Password</label><br>
            <input type="password" name="password" id="password"/><br>
            <label>Konfirmasi Password</label><br>
            <input type="password" name="confirm_password" id="password"/><br><br>
            <button type="submit">Register</button><br><br>
            <p>Sudah punya akun? <a href="login">Log in</a></p>
        </form>
    </body>
</html>