<?php

namespace common\helpers;

use yii;
use yii\helpers\Url;
use yii\web\UploadedFile;
use yii\helpers\FileHelper;
use yii\web\Response;


class _Files
{
    private $basePath;

    public $folder;
    public $model;
    public $prefixName;
    public $alias;

    public $setting;
    public $postFiles;

    public $fileContents;
    public $jsonFileContents;

    public $pathFolder;

    public function __construct($setting)
    {
        $this->postFiles = $_FILES;
        $this->setting = $setting;
    }

    public static function upload($settings)
    {
        return (new _Files($settings))->tryUpload();
    }



    /** ========== สั่งให้ลอง upload ========== */

    public function tryUpload()
    {
        [

            'attribute' => $attributeName,
            'model' => $model,
            'folder' => $this->pathFolder

        ] = $this->setting;


        $fileContents = null;

        if (isset($model) && !is_null($model)) {
            $fileContents = $this->uploadWithModel($model, $attributeName);
        } else {
            $fileContents = $this->uploadwithAttribute($attributeName);
        }

        $this->fileContents = $fileContents;

        return $this;
    }








    /** ========== upload ด้วย attribute ของ model ที่ส่งมา ========== */

    private function uploadWithModel($model, $attributeName)
    {
        $files = UploadedFile::getInstances($model, $attributeName);

        if (count($files) >= 1) {
            return (isset($files) && !empty($files)) ? $this->getJsonFileContents($files) : null;
        } else {
            return null;
        }
    }






    /** ========== upload ด้วย attribute ที่ส่งมา ========== */

    private function uploadwithAttribute($attributeName)
    {
        $files = UploadedFile::getInstancesByName($attributeName);

        if (count($files) >= 1) {
            return (isset($files) && !empty($files)) ? $this->getJsonFileContents($files) : null;
        } else {
            return null;
        }
    }




    /** ========== method สุดท้าย เพื่อดึงค่า json file content ที่ถูกสร้าง ========== */

    public function getArray()
    {
        return $this->jsonFileContents;
    }

    public function getJsonString()
    {
        return json_encode($this->jsonFileContents);
    }





    /** ========== สร้าง json string ที่จะ return ออกไป ========== */

    public function getJsonFileContents($files)
    {
        $jsonFileContents = [];
        foreach ($files as $file) {
            $jsonFileContents[] = $this->setupFileContents($file);
        }

        $this->jsonFileContents = $jsonFileContents;

        return json_encode($jsonFileContents);
    }




    /** ========== สร้าง array ข้อมูลของไฟล์ที่อัปโหลด ========== */

    private function setupFileContents($file)
    {

        $fileName = $this->generateFileName($file);
        $pathFolder = $this->getPathFolder();
        $this->createDirectory($pathFolder);

        $jsonFileContent = [];
        if ($file->saveAs($pathFolder . $fileName)) {
            $jsonFileContent = [
                'key' => $this->pullFileKey($fileName),
                'originalName' => $file->baseName . '.' . $file->extension,
                'name' => $fileName,
                'extension' => $file->extension,
                'size' => $file->size,
                'type' => $file->type,
                'folder' => $this->pathFolder,
            ];
        }

        return $jsonFileContent;
    }

    /** ========== ฟังก์ชั่น ดึง key เพื่อเก็บลง database ========== */
    private function pullFileKey($fileName)
    {
        $fileName = explode('.', $fileName);
        $fileName = $fileName[0];

        return $fileName;
    }








    /** ========== สร้างชื่อไฟล์แบบ random ========== */

    private function generateFileName($file)
    {
        $fileName = md5($file->baseName . time() . uniqid()) . '.' . $file->extension;

        return $fileName;
    }




    /** ========== เรียก path ที่ถูกต้องของ folder ที่เก็บ ========== */

    private function getPathFolder()
    {
        $pathFolder = realpath(dirname(__FILE__) . '/../../') . $this->pathFolder;
        $pathFolder = str_replace('\\', '/', $pathFolder);
        $pathFolder = explode('/', $pathFolder);

        $normalizePathFolder = join('/', array_merge($pathFolder));
        $normalizePathFolder = preg_replace('/(\/+)/', '/', $normalizePathFolder);

        if (substr($normalizePathFolder, -1) != '/') {
            $normalizePathFolder = $normalizePathFolder . '/';
        }

        return $normalizePathFolder;
    }






    /** ========== สร้าง folder ========== */

    private function createDirectory($pathFolder)
    {
        if (!is_dir($pathFolder)) {
            FileHelper::createDirectory($pathFolder, 0777);
        }
    }




    /** ========== ฟังก์ชั่นทำให้เป็น path ที่ถูกต้องของ folder ที่เก็บ ========== */

    public function normalizeFolder($folder)
    {
        $folder = str_replace('\\', '/', $folder);
        $folder = explode('/', $folder);

        $folder = join('/', $folder);
        $folder = preg_replace('/(\/+)/', '/', $folder);

        if (substr($folder, 0, 1) != '/') {
            $folder = '/' . $folder;
        }

        if (substr($folder, -1) != '/') {
            $folder = $folder . '/';
        }

        return $folder;
    }






    /** ========== ฟังก์ชั่นบันทึก 1 รูป ต่อ 1 row ========== */

    public static function saveOneToOne($model, array $fileContent)
    {
        if (!empty($fileContent)) {
            $modelTableName = $model->tableName();

            $fileRow = [];
            foreach ($fileContent as $file) {
                $fileRow[] = [
                    'file_key' => $file['key'],
                    'file_contents' => json_encode($file),
                ];
            }

            $fileField = [
                'file_key',
                'file_contents',
            ];

            Yii::$app->db
                ->createCommand()
                ->batchInsert($modelTableName, $fileField, $fileRow)
                ->execute();
        }
    }

    public static function saveManyToOne()
    {
        // 
    }






    /** ========== ฟังก์ชั่น ดึงค่าต่าง ๆ ========== */

    public static function isJson($value)
    {
        json_decode($value);
        return (json_last_error() == JSON_ERROR_NONE);
    }

    public static function getOriginalFileName(array $jsonFileContent)
    {
        $file = $jsonFileContent;
        return isset($file['originalName']) ? $file['originalName'] : null;
    }

    public static function getFileName(array $jsonFileContent)
    {
        $file = $jsonFileContent;
        return isset($file['name']) ? $file['name'] : null;
    }

    public static function getFileKey(array $jsonFileContent)
    {
        $file = $jsonFileContent;
        return isset($file['name']) ? $file['name'] : null;
    }

    public static function getFileExtension(array $jsonFileContent)
    {
        $file = $jsonFileContent;
        return isset($file['extension']) ? $file['extension'] : null;
    }

    public static function getFileSize(array $jsonFileContent)
    {
        $file = $jsonFileContent;
        return isset($file['size']) ? $file['size'] : null;
    }

    public static function getFileType(array $jsonFileContent)
    {
        $file = $jsonFileContent;
        return isset($file['type']) ? $file['type'] : null;
    }






    /** ========== ฟังก์ชั่นเกี่ยวกับเรียกรูปภาพ ========== */

    public function getFileUrl($jsonFileData)
    {
        $file = json_decode($jsonFileData, true);
        $filePath = self::getCorrectFolder($file['folder']) . $file['name'];

        return Url::base(true) . $filePath;
    }

    public function viewFile($fileCode)
    {
        try {
            $response = new Response;
            return $response->sendFile($this->getLocalFilePath($fileCode), $this->getFileName($fileCode), [
                'mimeType' => $this->getFileType($fileCode),
                'inline' => true,
            ]);
        } catch (\Throwable $th) {
            throw new \yii\web\HttpException(404, 'File not found.');
        }
    }

    public function downloadFile($fileCode, $originalName = false)
    {
        if ($originalName == false) {
            $fileName = $this->getFileName($fileCode);
        } else {
            $fileName = $this->getFileOriginalName($fileCode);
        }

        try {
            $response = new Response;
            return $response->sendFile($this->getLocalFilePath($fileCode), $fileName, [
                'mimeType' => $this->getFileType($fileCode),
                'inline' => false,
            ]);
        } catch (\Throwable $th) {
            throw new \yii\web\HttpException(404, 'File not found.');
        }
    }

    public static function removeFile($jsonFileData)
    {
        $fileLocation = self::getLocalFilePath($jsonFileData);
        FileHelper::unlink($fileLocation);
    }

    public function andUseIn($alias, $jsonFileData)
    {
        $file = json_decode($jsonFileData, true);

        $this->basePath = Yii::getAlias('@' . $alias . '/storage');
        $this->folder = $file['folder'];
        $newPath = self::getCorrectPath();
        self::createDirectory($newPath);
        $newFileLocation = $newPath . $file['name'];

        $this->basePath = $file['basePath'];
        $this->folder = $file['folder'];
        $oldPath = self::getCorrectPath();
        $oldFileLocation = $oldPath . $file['name'];

        if (file_exists($newFileLocation)) {
            // echo "The file exists";
        } else {
            // echo "The file does not exist";
            copy($oldFileLocation, $newFileLocation);
            // echo "copied";
        }
    }

    public function generateThumbnail($jsonFileData, $width = null, $height = null)
    {
        // TODO thumbnail
    }
}
