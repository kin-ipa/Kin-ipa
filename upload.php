<?php
header('Content-Type: application/json');

// إنشاء المجلدات إذا لم تكن موجودة
$folders = ['uploads', 'temp', 'modified'];
foreach ($folders as $folder) {
    if (!file_exists($folder)) {
        mkdir($folder, 0777, true);
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = isset($_GET['action']) ? $_GET['action'] : 'upload';
    
    if ($action === 'read') {
        // قراءة معلومات الـ plist من ملف IPA
        if (isset($_FILES['ipa_file']) && $_FILES['ipa_file']['error'] === UPLOAD_ERR_OK) {
            $uploadedFile = $_FILES['ipa_file']['tmp_name'];
            $fileName = uniqid() . '_' . $_FILES['ipa_file']['name'];
            $filePath = 'uploads/' . $fileName;
            
            if (move_uploaded_file($uploadedFile, $filePath)) {
                // استدعاء سكريبت Python لقراءة الـ plist
                $command = escapeshellcmd("python3 modify_ipa.py read " . escapeshellarg($filePath));
                $output = shell_exec($command . " 2>&1");
                
                if ($output) {
                    $data = json_decode($output, true);
                    if ($data && isset($data['success'])) {
                        echo json_encode($data);
                    } else {
                        echo json_encode([
                            'success' => false,
                            'message' => 'فشل في تحليل ملف IPA'
                        ]);
                    }
                } else {
                    echo json_encode([
                        'success' => false,
                        'message' => 'فشل في تشغيل سكريبت المعالجة'
                    ]);
                }
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'فشل في رفع الملف'
                ]);
            }
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'لم يتم رفع أي ملف'
            ]);
        }
    }
}
?>
