<?php
// Dolu saatleri PHP ile alalım
$doluSaatler = [];
$baglanti = new mysqli("localhost", "root", "", "randevuberber");
if (!$baglanti->connect_error) {
    $tarih = date("Y-m-d");
    $sorgu = $baglanti->prepare("SELECT TIME_FORMAT(randevu_saati, '%H:%i') AS saat FROM randevular WHERE DATE(alindigi_tarih) = ?");
    if ($sorgu) {
        $sorgu->bind_param("s", $tarih);
        $sorgu->execute();
        $sonuc = $sorgu->get_result();
        while ($row = $sonuc->fetch_assoc()) {
            $doluSaatler[] = $row['saat'];
        }
    }
    $baglanti->close();
}

// POST işlemleri
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $adsoyad = $_POST["adsoyad"];
    $telefon = $_POST["telefon"];
    $saat = $_POST["randevu_saati"];

    $baglanti = new mysqli("localhost", "root", "", "randevuberber");
    if ($baglanti->connect_error) {
        die("Bağlantı hatası: " . $baglanti->connect_error);
    }

    $tarih = date("Y-m-d");
    $alindigi_tarih = date("Y-m-d H:i:s");
    $gun = date("w");

    // Ayarlar kontrolü
    $sorgu = $baglanti->query("SELECT randevu_acik FROM ayarlar LIMIT 1");
    if (!$sorgu) {
        die("Ayarlar tablosuna erişilemedi: " . $baglanti->error);
    }

    $ayar = $sorgu->fetch_assoc();
    if ($ayar["randevu_acik"] != 1) {
        echo "<script>alert('Şu anda randevu sistemi kapalıdır.'); window.location.href='randevual.php';</script>";
        exit;
    }

    if ($gun == 0) {
        echo "<script>alert('Pazar günü randevu alınamaz.'); window.location.href='randevual.php';</script>";
        exit;
    }

    $simdiki_saat = date("H:i");
    if ($saat <= $simdiki_saat) {
        echo "<script>alert('Geçmiş saat için randevu alınamaz.'); window.location.href='randevual.php';</script>";
        exit;
    }

    // Randevuyu ekle
    $ekle = $baglanti->prepare("INSERT INTO randevular (adsoyad, telefon, randevu_saati, alindigi_tarih) VALUES (?, ?, ?, ?)");
    $ekle->bind_param("ssss", $adsoyad, $telefon, $saat, $alindigi_tarih);
    if ($ekle->execute()) {
        echo "<div style='
            max-width: 400px;
            margin: 100px auto;
            padding: 30px;
            border: 2px solid #28a745;
            border-radius: 10px;
            font-family: Arial, sans-serif;
            text-align: center;
            background-color: #eaffea;
        '>
            <h2 style='color: #28a745;'>✅ Randevunuz Alındı</h2>
            <p><strong>Ad Soyad:</strong> $adsoyad</p>
            <p><strong>Telefon:</strong> $telefon</p>
            <p><strong>Randevu Saati:</strong> $saat</p>
            <p style='margin-top: 20px;'>Lütfen randevu saatinden <strong>10 dakika önce</strong> dükkanda olunuz.</p>
            <a href='index.html' style='display: inline-block; margin-top: 20px; padding: 10px 20px; background-color: #28a745; color: white; text-decoration: none; border-radius: 5px;'>Ana Sayfaya Dön</a>
        </div>";
    } else {
        echo "<script>alert('Bir hata oluştu.'); window.location.href='randevual.php';</script>";
    }

    $baglanti->close();
    exit;
}

// JavaScript'e aktarılacak veri
$js_dolu_saatler = json_encode($doluSaatler);
?>
<!DOCTYPE html>
<html lang="tr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Randevu Al - Mamos The Barber</title>
  <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;700&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
  <style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    html, body { overflow-x: hidden; }

    body {
      font-family: 'Outfit', sans-serif;
      background: linear-gradient(rgba(0,0,0,0.7), rgba(0,0,0,0.85)), url('images/anabg.jpeg') no-repeat center center/cover;
      background-attachment: fixed;
      color: white;
      min-height: 100vh;
    }

    header {
      position: fixed;
      top: 0;
      width: 100%;
      background: rgba(0, 0, 0, 0.6);
      backdrop-filter: blur(10px);
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 15px 30px;
      z-index: 999;
    }

    .logo {
      display: flex;
      align-items: center;
      gap: 12px;
    }

    .logo img {
      height: 50px;
      border-radius: 10px;
    }

    .logo span {
      font-size: 26px;
      font-weight: bold;
    }

    nav {
      display: flex;
      gap: 25px;
    }

    nav a {
      color: white;
      font-size: 16px;
      font-weight: 600;
      text-decoration: none;
      position: relative;
      padding: 6px 10px;
      transition: 0.3s;
    }

    nav a i { margin-right: 6px; }

    nav a::after {
      content: '';
      position: absolute;
      bottom: -4px;
      left: 0;
      width: 0%;
      height: 2px;
      background-color: #f1c40f;
      transition: 0.3s ease;
    }

    nav a:hover::after { width: 100%; }

    .menu-toggle {
      display: none;
      font-size: 28px;
      color: white;
      background: none;
      border: none;
      cursor: pointer;
      z-index: 1001;
    }

    @media (max-width: 768px) {
      .menu-toggle { display: block; }

      nav {
        position: absolute;
        top: 70px;
        left: 0;
        right: 0;
        background-color: rgba(0,0,0,0.9);
        flex-direction: column;
        align-items: flex-start;
        padding: 15px 30px;
        display: none;
      }

      nav.show { display: flex; }

      nav a {
        width: 100%;
        padding: 10px 0;
      }
    }

    main {
      padding: 130px 20px 60px;
      max-width: 800px;
      margin: auto;
    }

    h1 {
      text-align: center;
      font-size: 28px;
      margin-bottom: 30px;
    }

    .times {
      display: flex;
      flex-wrap: wrap;
      gap: 15px;
      justify-content: center;
      margin-bottom: 40px;
    }

    .time-slot {
      background: #f1c40f;
      color: black;
      padding: 10px 20px;
      border-radius: 10px;
      cursor: pointer;
      font-weight: 600;
      transition: 0.3s;
      position: relative;
    }

    .time-slot:hover,
    .time-slot.selected {
      background: white;
      color: #000;
    }

    .time-slot.dolu {
      background: #dc3545 !important;
      color: white !important;
      cursor: not-allowed;
      opacity: 0.7;
    }
    
    .time-slot.dolu::after {
      content: 'DOLU';
      position: absolute;
      top: -5px;
      right: -5px;
      background: black;
      color: white;
      font-size: 10px;
      padding: 2px 5px;
      border-radius: 10px;
      border: 1px solid white;
      font-weight: bold;
    }

    .form-container {
      display: grid;
      gap: 15px;
      opacity: 0;
      transform: translateY(50px);
      transition: opacity 0.6s ease-out, transform 0.6s ease-out;
      pointer-events: none;
    }

    .form-container.show {
      opacity: 1;
      transform: translateY(0);
      pointer-events: all;
    }

    .form-container input,
    .form-container button {
      transform: translateY(20px);
      opacity: 0;
      animation: slideIn 0.6s ease forwards;
    }

    .form-container.show input:nth-child(1) {
      animation-delay: 0.1s;
    }

    .form-container.show input:nth-child(2) {
      animation-delay: 0.2s;
    }

    .form-container.show button {
      animation-delay: 0.3s;
    }

    @keyframes slideIn {
      to {
        transform: translateY(0);
        opacity: 1;
      }
    }

    .form-container input {
      padding: 10px;
      border-radius: 8px;
      border: none;
      font-size: 16px;
    }

    .form-container button {
      padding: 10px;
      background: #f1c40f;
      border: none;
      font-weight: bold;
      font-size: 16px;
      border-radius: 8px;
      cursor: pointer;
      transition: 0.3s;
    }

    .form-container button:hover {
      background: white;
      color: black;
    }
  </style>
</head>
<body>
  <header>
    <div class="logo">
      <img src="images/logo.jpg" alt="Logo" />
      <span>Mamos The Barber</span>
    </div>
    <button class="menu-toggle" id="menu-toggle"><i class="fas fa-bars"></i></button>
    <nav id="nav">
      <a href="index.html"><i class="fas fa-home"></i> Ana Sayfa</a>
      <a href="galeri.html"><i class="fas fa-image"></i> Galeri</a>
      <a href="iletisim.html"><i class="fas fa-envelope"></i> İletişim</a>
    </nav>
  </header>

  <main>
    <h1 id="today-date"></h1>
    <div class="times" id="timeSlots"></div>

    <form class="form-container" id="reservationForm" method="POST" action="randevual.php">
      <input type="text" name="adsoyad" placeholder="Ad Soyad" required />
      <input type="tel" name="telefon" placeholder="Cep Telefonu" required />
      <input type="hidden" name="randevu_saati" id="selectedTime" />
      <button type="submit">Randevuyu Onayla</button>
    </form>
  </main>

  <script>
    // PHP'den gelen dolu saatler
    const DOLU_SAATLER = <?php echo $js_dolu_saatler; ?>;
    console.log("Dolu saatler:", DOLU_SAATLER);

    const toggleBtn = document.getElementById('menu-toggle');
    const nav = document.getElementById('nav');
    toggleBtn.addEventListener('click', () => nav.classList.toggle('show'));

    const timeSlotsDiv = document.getElementById("timeSlots");
    const form = document.getElementById("reservationForm");
    const today = new Date();
    const dateText = today.toLocaleDateString('tr-TR', {
      weekday: 'long',
      year: 'numeric',
      month: 'long',
      day: 'numeric'
    });
    document.getElementById("today-date").innerText = dateText;

    function generateTimeSlots() {
      let start = new Date();
      start.setHours(10, 0, 0, 0);
      const end = new Date();
      end.setHours(20, 0, 0, 0);

      timeSlotsDiv.innerHTML = '';

      while (start < end) {
        const hour = start.getHours();
        const minutes = start.getMinutes();
        const formatted = `${hour.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}`;

        if (hour !== 13) {
          const div = document.createElement('div');
          div.className = 'time-slot';
          div.textContent = formatted;
          
          // Dolu saat kontrolü
          if (DOLU_SAATLER.includes(formatted)) {
            div.classList.add('dolu');
          } else {
            div.onclick = () => selectTime(div, formatted);
          }
          
          timeSlotsDiv.appendChild(div);
        }

        start.setMinutes(start.getMinutes() + 40);
      }
    }

    function selectTime(elem, time) {
      document.querySelectorAll('.time-slot').forEach(e => e.classList.remove('selected'));
      elem.classList.add('selected');
      document.getElementById("selectedTime").value = time;
      form.classList.remove('show');
      void form.offsetWidth;
      form.classList.add('show');
    }

    // Sayfa yüklendiğinde çalıştır
    window.addEventListener('DOMContentLoaded', generateTimeSlots);
  </script>
</body>
</html>