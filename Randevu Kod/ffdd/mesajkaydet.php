<?php
$conn = new mysqli("localhost", "root", "", "randevuberber");
if ($conn->connect_error) {
    die("Bağlantı hatası: " . $conn->connect_error);
}

$adsoyad = $_POST['adsoyad'];
$email = $_POST['email'];
$mesaj = $_POST['mesaj'];

$stmt = $conn->prepare("INSERT INTO mesajlar (adsoyad, email, mesaj) VALUES (?, ?, ?)");
$stmt->bind_param("sss", $adsoyad, $email, $mesaj);

if ($stmt->execute()) {
    echo "<script>alert('Mesajınız iletildi!'); window.location.href='index.html';</script>";
} else {
    echo "Hata: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
