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

// Tablo oluşturma kısmı (Aynen korundu)
$conn->query("CREATE TABLE IF NOT EXISTS ayarlar (
    id INT AUTO_INCREMENT PRIMARY KEY,
    randevu_acik TINYINT(1) DEFAULT 1
)");

$conn->query("CREATE TABLE IF NOT EXISTS randevu_gecmis (
    id INT AUTO_INCREMENT PRIMARY KEY,
    adsoyad VARCHAR(255),
    telefon VARCHAR(20),
    randevu_saati DATETIME,
    alindigi_tarih DATE,
    geldi TINYINT(1)
)");

$conn->query("CREATE TABLE IF NOT EXISTS randevusistem (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sistem_durum TINYINT(1) DEFAULT 1
)");

// DÜZELTİLMİŞ KISIM - Geçmiş randevu aktarımı
$bugun = date("Y-m-d");
if (!isset($_SESSION['son_temizlik']) || $_SESSION['son_temizlik'] != $bugun) {
    
    // Bugünden önceki randevuları geçmişe taşı
    $conn->query("
        INSERT INTO randevu_gecmis (adsoyad, telefon, randevu_saati, alindigi_tarih, geldi)
        SELECT 
            adsoyad, 
            telefon, 
            CONCAT(DATE(alindigi_tarih), ' ', randevu_saati) as randevu_tarihsaat,
            alindigi_tarih,
            COALESCE(geldi, 0)
        FROM randevular
        WHERE DATE(alindigi_tarih) < '$bugun'
    ");
    
    // Bugünden öncekileri sil
    $conn->query("DELETE FROM randevular WHERE DATE(alindigi_tarih) < '$bugun'");
    
    $_SESSION['son_temizlik'] = $bugun;
    $_SESSION['bilgi'] = "Geçmiş randevular taşındı!";
}

// Sistem ayarları (Aynen korundu)
$ayarsorgu = $conn->query("SELECT * FROM ayarlar LIMIT 1");
if ($ayarsorgu->num_rows == 0) {
    $conn->query("INSERT INTO ayarlar (randevu_acik) VALUES (1)");
    $randevuDurum = 1;
} else {
    $ayar = $ayarsorgu->fetch_assoc();
    $randevuDurum = $ayar['randevu_acik'];
}

// POST işlemleri (Aynen korundu)
if (isset($_POST['durum'])) {
    $durum = $_POST['durum'] == '1' ? '1' : '0';
    $conn->query("UPDATE ayarlar SET randevu_acik = '$durum'");
    $randevuDurum = $durum;
}

if (isset($_GET['sil'])) {
    $id = intval($_GET['sil']);
    $conn->query("DELETE FROM randevular WHERE id = $id");
}

if (isset($_POST['durumguncelle'])) {
    $id = intval($_POST['id']);
    $geldi = $_POST['geldi'] === '1' ? 1 : 0;
    $conn->query("UPDATE randevular SET geldi = $geldi WHERE id = $id");
}

// Bugünün randevularını çek (Aynen korundu)
$bugun = date("Y-m-d");
$randevular = $conn->query("SELECT * FROM randevular WHERE DATE(alindigi_tarih) = '$bugun' ORDER BY randevu_saati ASC");
?>

<!DOCTYPE html>
<html lang="tr">
<head>
  <meta charset="UTF-8">
  <title>Kontrol Paneli</title>
  <style>
    body { font-family: Arial; background: #f4f4f4; padding: 30px; }
    h2 { margin-bottom: 20px; }
    table { width: 100%; border-collapse: collapse; background: white; }
    th, td { border: 1px solid #ccc; padding: 10px; text-align: center; }
    th { background: #eee; }
    form { margin-bottom: 20px; display: inline-block; }
    .btn { padding: 5px 10px; background: #f39c12; border: none; color: white; cursor: pointer; border-radius: 4px; }
    .btn-sil { background: #e74c3c; }
    .btn-yesil { background: #2ecc71; }
    .btn-gri { background: #95a5a6; }
    .uyari {
      padding: 10px;
      color: white;
      margin-bottom: 20px;
      font-weight: bold;
      border-radius: 5px;
    }
    .acik { background-color: #2ecc71; }
    .kapali { background-color: #e74c3c; }
  </style>
</head>
<body>

<h2>Randevu Kontrol Paneli</h2>

<?php if (isset($_SESSION['bilgi'])): ?>
<div class="uyari" style="background:#17a2b8;"><?= $_SESSION['bilgi'] ?></div>
<?php unset($_SESSION['bilgi']); endif; ?>

<div class="uyari <?= $randevuDurum ? 'acik' : 'kapali' ?>">
  Randevu Sistemi Şu Anda: <?= $randevuDurum ? 'Açık' : 'Kapalı' ?>
</div>

<a href="gelenmesajlar.php" class="btn btn-gri" style="margin-bottom: 20px; margin-right: 10px;">Gelen Mesajlar</a>

<form method="post">
  <label><strong>Randevu Sistemi Durumu:</strong></label>
  <select name="durum">
    <option value="1" <?= $randevuDurum ? 'selected' : '' ?>>Açık</option>
    <option value="0" <?= !$randevuDurum ? 'selected' : '' ?>>Kapalı</option>
  </select>
  <button type="submit" class="btn">Kaydet</button>
</form>

<a href="gecmisrandevular.php" class="btn btn-gri" style="float: right; margin-bottom: 20px;">Geçmiş Randevular</a>

<table>
  <tr>
    <th>ID</th>
    <th>Ad Soyad</th>
    <th>Telefon</th>
    <th>Randevu Saati</th>
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
    <td><?= date("d.m.Y H:i", strtotime($row['alindigi_tarih'])) ?></td>
    <td>
      <form method="post">
        <input type="hidden" name="id" value="<?= $row['id'] ?>">
        <input type="hidden" name="durumguncelle" value="1">
        <select name="geldi">
          <option value="1" <?= $row['geldi'] == 1 ? 'selected' : '' ?>>Geldi</option>
          <option value="0" <?= $row['geldi'] === '0' ? 'selected' : '' ?>>Gelmedi</option>
        </select>
        <button class="btn btn-yesil" type="submit">Kaydet</button>
      </form>
    </td>
    <td>
      <a class="btn btn-sil" href="?sil=<?= $row['id'] ?>" onclick="return confirm('Silinsin mi?')">Sil</a>
    </td>
  </tr>
  <?php endwhile; ?>
</table>

</body>
</html>