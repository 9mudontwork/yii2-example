<?php

/**
 * // Muhammad Imoeb
 * ===== วิธีใช้ =====
 * 
 * 
 * */

use common\helpers\HandleFile;

// อัปโหลดไฟล์
$handleFile = new HandleFile('frontend');
$handleFile->folder = '/uploads/xxxx/';
$fileCode = $handleFile->doUpload('image', $model);

$model->image = $fileCode;
$model->image_key = $handleFile->getFileKey($fileCode);

// ไฟล์หน้า view
$handleFile = new HandleFile();
$files = [];

foreach ($projectDocumentFile as $value) {
    $files[] = [
        'originalName' => $handleFile->getFileOriginalName($value['file_code']),
        'name' => $handleFile->getFileName($value['file_code']),
        'fileUrl' => $handleFile->getFileUrl($value['file_code']),
        'downloadUrl' => '/project-document/download-document/?id=' . $value['id'],

    ];
}

// ลบไฟล์
HandleFile::removeFile($file['file_code']);

// โหลดไฟล์
$handleFile->downloadFile($projectDocumentFile['file_code']);

// ต้องการใช้ใน environments อื่นด้วย
$handleFile->andUseIn('backend', $fileCode);
