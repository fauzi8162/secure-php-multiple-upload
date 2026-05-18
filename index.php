<?php
/**
 * SCRIPT UPLOAD GAMBAR MULTIPLE - PRODUCTION READY
 * Fitur: Nama file acak, Validasi MIME Type, Limit Ukuran, Multiple Upload.
 */
session_start();

$targetDir = "uploads/";
$maxFileSize = 10 * 1024 * 1024; // Limit 10MB per file
$allowedMimeTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
$messages = [];

$messages = isset($_SESSION['upload_messages']) ? $_SESSION['upload_messages'] : [];
unset($_SESSION['upload_messages']);

// Buat folder uploads jika belum ada
if (!is_dir($targetDir)) {
    mkdir($targetDir, 0755, true);
    
    // Tambahkan pelindung .htaccess otomatis di dalam folder uploads
    $htaccessContent = "<FilesMatch \"\.(php|phtml|php3|php4|php5|php7|php8|phps|pl|py|jsp|asp|sh|cgi)$\">\n";
    $htaccessContent .= "    Order Allow,Deny\n";
    $htaccessContent .= "    Deny from all\n";
    $htaccessContent .= "</FilesMatch>\n";
    $htaccessContent .= "Options -ExecCGI\n";
    $htaccessContent .= "php_flag engine off\n";
    
    file_put_contents($targetDir . '.htaccess', $htaccessContent);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['gambar'])) {
    $files = $_FILES['gambar'];
    $fileCount = count($files['name']);
	$localMessages = []; // Tampung pesan di variabel lokal dulu

    // Loop melalui setiap file yang diupload
    for ($i = 0; $i < $fileCount; $i++) {
        $fileNameOriginal = $files['name'][$i];
        $fileTmpName      = $files['tmp_name'][$i];
        $fileSize         = $files['size'][$i];
        $fileError        = $files['error'][$i];

        // Skip jika tidak ada file yang dipilih
        if ($fileError === UPLOAD_ERR_NO_FILE) continue;

        // 1. Cek Error Internal PHP
        if ($fileError !== UPLOAD_ERR_OK) {
            $localMessages[] = "❌ File <b>$fileNameOriginal</b> gagal diupload (Error code: $fileError).";
            continue;
        }

        // 2. Cek Ukuran File
        if ($fileSize > $maxFileSize) {
            $localMessages[] = "❌ File <b>$fileNameOriginal</b> terlalu besar (Maks " . ($maxFileSize / 1024 / 1024) . "MB).";
            continue;
        }

        // 3. Cek Tipe MIME (Keamanan: Memastikan file benar-benar gambar)
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->file($fileTmpName);

        if (in_array($mimeType, $allowedMimeTypes)) {
            // Petakan MIME Type ke ekstensi yang aman secara manual
			$extensionMap = [
				'image/jpeg' => 'jpg',
				'image/png'  => 'png',
				'image/gif'  => 'gif',
				'image/webp' => 'webp'
			];
			
            // 4. Buat Nama Baru yang Unik (Keamanan: Menghindari script injection via nama file)
            $extension = $extensionMap[$mimeType]; // Pasti aman karena diambil dari sistem, bukan user
			
            $newFileName = bin2hex(random_bytes(10)) . "." . $extension;
            $targetPath = $targetDir . $newFileName;

            // 5. Eksekusi Pemindahan File
            if (move_uploaded_file($fileTmpName, $targetPath)) {
                //$messages[] = "✅ <b>$fileNameOriginal</b> berhasil diunggah sebagai <code>$newFileName</code>";
				$sizeInKB = round($fileSize / 1024, 2);
				$localMessages[] = "✅ <b>$fileNameOriginal</b> ({$sizeInKB} KB)  berhasil diunggah sebagai <code>FileName</code>";
            } else {
                $localMessages[] = "❌ Gagal menyimpan file <b>$fileNameOriginal</b>.";
            }
        } else {
            $localMessages[] = "❌ File <b>$fileNameOriginal</b> ditolak! Hanya JPG, PNG, GIF, dan WEBP yang diizinkan.";
        }
    }
	$_SESSION['upload_messages'] = $localMessages;
	header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Multiple Image Upload - Production</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f4f7f6; display: flex; justify-content: center; padding: 20px; }
        .upload-card { background: #fff; padding: 30px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); width: 100%; max-width: 500px; }
        h2 { color: #333; margin-top: 0; }
        .alert-box { margin-bottom: 20px; padding: 15px; border-radius: 6px; font-size: 14px; background: #f9f9f9; border-left: 4px solid #ddd; }
        input[type="file"] { display: block; margin: 20px 0; padding: 10px; border: 1px dashed #bbb; width: 100%; box-sizing: border-box; }
        button { background-color: #2ecc71; color: white; border: none; padding: 12px 20px; border-radius: 6px; cursor: pointer; font-weight: bold; width: 100%; }
        button:hover { background-color: #27ae60; }
        .info { font-size: 12px; color: #777; margin-top: 10px; }
    </style>
</head>
<body>

<div class="upload-card">
    <h2>Upload Gambar</h2>

    <?php if (!empty($messages)): ?>
        <div class="alert-box">
            <?php foreach ($messages as $msg) echo $msg . "<br>"; ?>
        </div>
    <?php endif; ?>

    <form action="" method="post" enctype="multipart/form-data">
        <label>Pilih satu atau beberapa gambar:</label>
        <input type="file" name="gambar[]" accept="image/*" multiple required>
        
        <button type="submit">Unggah Sekarang</button>
        
        <div class="info">
            * Maksimal <?php echo ($maxFileSize / 1024 / 1024); ?> MB per file.<br>
            * Format didukung: JPG, PNG, GIF, WEBP.
        </div>
    </form>
</div>

</body>
</html>
