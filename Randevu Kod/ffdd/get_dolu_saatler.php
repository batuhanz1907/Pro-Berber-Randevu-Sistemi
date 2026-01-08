<?php
header('Content-Type: application/json');

$baglanti = new mysqli("localhost", "root", "", "randevuberber");
if ($baglanti->connect_error) {
    die("Bağlantı hatası: " . $baglanti->connect_error);
}

$tarih = date("Y-m-d");
$sorgu = $baglanti->prepare("SELECT randevu_saati FROM randevular WHERE DATE(alindigi_tarih) = ?");
$sorgu->bind_param("s", $tarih);
$sorgu->execute();
$sonuc = $sorgu->get_result();

$doluSaatler = [];
while ($row = $sonuc->fetch_assoc()) {
    $doluSaatler[] = $row['randevu_saati'];
}

echo json_encode($doluSaatler);
$baglanti->close();
?>