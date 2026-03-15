<?php
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_FILES['ipa_file']) && $_FILES['ipa_file']['error'] === UPLOAD_ERR_OK) {
        
        $bundle_id = isset($_POST['bundle_id']) ? trim($_POST['bundle_id']) : '';
        $app_name = isset($_POST['app_name']) ? trim($_POST['app_name']) : '';
        $display_name = isset($_POST['display_name']) ? trim($_POST['display_name']) : '';
        
        // التحقق من وجود تعديلات
        if (empty($bundle_id) && empty($app_name) && empty($display_name)) {
            echo json_encode([
                'success' => false,
                'message' => 'يرجى إدخال تعديل واحد على الأقل'
            ]);
            exit;
        }
        
        $uploadedFile = $_FILES['ipa_file']['tmp_name'];
        $originalName = $_FILES['ipa_file']['name'];
        $fileName = uniqid() . '_' . $originalName;
        $filePath = 'uploads/' . $fileName;
        
        if (move_uploaded_file($uploadedFile, $filePath)) {
            
            // تجهيز الأمر لتعديل الملف
            $command = "python3 modify_ipa.py modify " . 
                      escapeshellarg($filePath) . " " .
                      escapeshellarg($bundle_id) . " " .
                      escapeshellarg($app_name) . " " .
                      escapeshellarg($display_name);
            
            $output = shell_exec($command . " 2>&1");
            
            if ($output) {
                $data = json_decode($output, true);
                if ($data && isset($data['success']) && $data['success']) {
                    
                    // إنشاء رابط التحميل
                    $download_url = 'download.php?file=' . urlencode(basename($data['output_file']));
                    
                    echo json_encode([
                        'success' => true,
                        'message' => 'تم تعديل الملف بنجاح',
                        'download_url' => $download_url,
                        'output_file' => $data['output_file']
                    ]);
                } else {
                    echo json_encode([
                        'success' => false,
                        'message' => isset($data['message']) ? $data['message'] : 'فشل في تعديل الملف'
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
} else {
    echo json_encode([
        'success' => false,
        'message' => 'طريقة طلب غير صحيحة'
    ]);
}
?>
