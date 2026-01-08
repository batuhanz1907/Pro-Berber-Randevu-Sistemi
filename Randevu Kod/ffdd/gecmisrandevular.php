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

// Silme işlemleri (Aynen korundu)
if (isset($_GET['sil'])) {
    $id = intval($_GET['sil']);
    $conn->query("DELETE FROM randevu_gecmis WHERE id = $id");
}

if (isset($_GET['tumunu_sil'])) {
    $conn->query("DELETE FROM randevu_gecmis");
}

// DÜZELTİLMİŞ KISIM - Sorgu güncellendi
$arama = isset($_GET['arama']) ? $conn->real_escape_string($_GET['arama']) : '';
$tarih = isset($_GET['tarih']) ? $conn->real_escape_string($_GET['tarih']) : '';

$query = "SELECT 
    id, 
    adsoyad, 
    telefon, 
    DATE_FORMAT(randevu_saati, '%H:%i') as randevu_saati,
    DATE_FORMAT(randevu_saati, '%d.%m.%Y') as randevu_tarihi,
    DATE_FORMAT(alindigi_tarih, '%d.%m.%Y %H:%i') as alindigi_tarih,
    geldi
FROM randevu_gecmis WHERE 1";

if (!empty($arama)) {
    $query .= " AND (adsoyad LIKE '%$arama%' OR telefon LIKE '%$arama%')";
}
if (!empty($tarih)) {
    $query .= " AND DATE(randevu_saati) = '$tarih'";
}

$query .= " ORDER BY randevu_saati DESC";

$randevular = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="tr">
<head>
  <meta charset="UTF-8">
  <title>Geçmiş Randevular</title>
  <style>
    body { font-family: Arial; background: #f4f4f4; padding: 30px; }
    h2 { margin-bottom: 20px; }
    table { width: 100%; border-collapse: collapse; background: white; }
    th, td { border: 1px solid #ccc; padding: 10px; text-align: center; }
    th { background: #eee; }
    .btn { padding: 6px 12px; background: #3498db; border: none; color: white; cursor: pointer; border-radius: 4px; text-decoration: none; }
    .btn-sil { background: #e74c3c; }
    .btn-gri { background: #95a5a6; }
    .btn-yesil { background: #2ecc71; }
    .form-inline input[type="text"],
    .form-inline input[type="date"] {
        padding: 6px;
        margin-right: 10px;
        border: 1px solid #ccc;
        border-radius: 4px;
    }
    .form-inline { margin-bottom: 20px; }
  </style>
</head>
<body>

<h2>Geçmiş Randevular</h2>

<a href="kontrolpaneli.php" class="btn btn-gri">← Geri Dön</a>

<form method="GET" class="form-inline">
    <input type="text" name="arama" placeholder="İsim veya telefon ara..." value="<?= htmlspecialchars($arama) ?>">
    <input type="date" name="tarih" value="<?= htmlspecialchars($tarih) ?>">
    <button class="btn btn-yesil" type="submit">Filtrele</button>
    <a href="gecmisrandevular.php" class="btn btn-gri">Filtreyi Sıfırla</a>
</form>

<a href="?tumunu_sil=1" class="btn btn-sil" onclick="return confirm('Tüm geçmiş randevuları silmek istediğine emin misin?')">🗑 Tümünü Sil</a>

<table>
  <tr>
    <th>ID</th>
    <th>Ad Soyad</th>
    <th>Telefon</th>
    <th>Randevu Saati</th>
    <th>Randevu Tarihi</th>
    <th>Alım Tarihi</th>
    <th>Durum</th>
    <th>İşlem</th>
  </tr>
  <?php while($row = $randevular->fetch_assoc()): ?>
  <tr>
    <td><?= $row['id'] ?></td>
    <td><?= htmlspecialchars($row['adsoyad']) ?></td>
    <td><?= htmlspecialchars($row['telefon']) ?></td>
    <td><?= $row['randevu_saati'] ?></td>
    <td><?= $row['randevu_tarihi'] ?></td>
    <td><?= $row['alindigi_tarih'] ?></td>
    <td>
      <?php if ($row['geldi'] == 1): ?>
        <span style="color: green;">Geldi</span>
      <?php else: ?>
        <span style="color: red;">Gelmedi</span>
      <?php endif; ?>
    </td>
    <td>
      <a class="btn btn-sil" href="?sil=<?= $row['id'] ?>" onclick="return confirm('Bu randevuyu silmek istediğine emin misin?')">Sil</a>
    </td>
  </tr>
  <?php endwhile; ?>
</table>

</body>
</html>