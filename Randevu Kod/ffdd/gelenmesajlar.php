<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: admingiris.php");
    exit();
}

$conn = new mysqli("localhost", "root", "", "randevuberber");
if ($conn->connect_error) {
    die("Bağlantı hatası: " . $conn->connect_error);
}

$mesajlar = $conn->query("SELECT * FROM mesajlar ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="tr">
<head>
  <meta charset="UTF-8">
  <title>Gelen Mesajlar</title>
  <style>
    body { font-family: Arial; background: #f4f4f4; padding: 30px; }
    h2 { margin-bottom: 20px; }
    table { width: 100%; border-collapse: collapse; background: white; }
    th, td { border: 1px solid #ccc; padding: 10px; text-align: left; }
    th { background: #eee; }
    .btn-gri {
      padding: 5px 10px;
      background: #95a5a6;
      border: none;
      color: white;
      text-decoration: none;
      border-radius: 4px;
    }
  </style>
</head>
<body>

<h2>Gelen Mesajlar</h2>
<a href="kontrolpaneli.php" class="btn-gri" style="margin-bottom: 20px; display: inline-block;">← Geri Dön</a>

<table>
  <tr>
    <th>Ad Soyad</th>
    <th>E-posta</th>
    <th>Mesaj</th>
    <th>Tarih</th>
  </tr>
  <?php while ($row = $mesajlar->fetch_assoc()): ?>
  <tr>
    <td><?= htmlspecialchars($row['adsoyad']) ?></td>
    <td><?= htmlspecialchars($row['email']) ?></td>
    <td><?= nl2br(htmlspecialchars($row['mesaj'])) ?></td>
    <td><?= $row['tarih'] ?></td>
  </tr>
  <?php endwhile; ?>
</table>

</body>
</html>
