<?php
session_start();
$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $kullanici = $_POST['kullanici'];
    $sifre = $_POST['sifre'];

    if ($kullanici == "admin" && $sifre == "30adminerkan30") {
        $_SESSION['admin'] = true;
        header("Location: kontrolpaneli.php");
        exit();
    } else {
        $error = "Kullanıcı adı veya şifre yanlış!";
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
  <meta charset="UTF-8">
  <title>Admin Giriş</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background: #111;
      color: white;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
    }
    .giris-kutusu {
      background: #222;
      padding: 30px;
      border-radius: 10px;
      box-shadow: 0 0 10px #000;
    }
    input {
      width: 100%;
      padding: 10px;
      margin: 8px 0;
      border: none;
      border-radius: 5px;
    }
    button {
      background-color: #f1c40f;
      border: none;
      padding: 10px 20px;
      font-weight: bold;
      cursor: pointer;
      border-radius: 5px;
      color: black;
    }
    .error {
      color: red;
    }
  </style>
</head>
<body>
  <div class="giris-kutusu">
    <h2>Admin Giriş</h2>
    <?php if ($error != '') echo "<p class='error'>$error</p>"; ?>
    <form method="POST">
      <input type="text" name="kullanici" placeholder="Kullanıcı Adı" required>
      <input type="password" name="sifre" placeholder="Şifre" required>
      <button type="submit">Giriş Yap</button>
    </form>
  </div>
</body>
</html>
