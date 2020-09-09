<?php
// ไฟล์นี้ไม่ควรถูกแก้ไข
// muhammad

// _Files::upload จะ return ค่า เป็น array หรือ null เสมอ

/**  ===================================================== */
//              ตัวอย่างในการใช้บันทึกรูป
//              บันทึก 1 รูป ต่อ 1 row
/*

$fileUploadContents = _Files::upload([
    'attribute' => 'file_contents',
    'model' => $model,
    'folderPath' => '/common/files-storage/'
]);

$fileUploadContents->saveOnTable($model);

*/
/**  ===================================================== */




/**  ===================================================== */
//              ตัวอย่างในการใช้บันทึกรูป
//              บันทึก หลายรูป ใน 1 field
/*

$fileUploadContents = _Files::upload([
    'attribute' => 'file_contents',
    'model' => $model,
    'folderPath' => '/common/files-storage/'
]);

$model->file_key = _Files::generateKey();
$model->file_contents = $fileUploadContents->multipleSave();

$fileUploadContents->saveOnTable($model);

*/
/**  ===================================================== */

namespace common\helpers;

use yii;
use yii\helpers\Url;
use yii\web\UploadedFile;
use yii\helpers\FileHelper;
use yii\web\Response;


class _Files
{
    // ตั้งค่า fiels ของ table ตรงนี้ ถ้าต้องการใช้ table
    public $fieldFileKey = 'file_key';
    public $fieldFileContent = 'file_content';







    public $setting;

    public $folderPath;

    public $fileContents = null;




    public function __construct($setting = null)
    {
        $this->setting = $setting;
    }

    public static function upload($settings)
    {
        return (new _Files($settings))->tryUpload();
    }



    /** ========== สั่งให้ลอง upload ========== */

    public function tryUpload()
    {
        if (!$this->setting) return null;

        [

            'attribute' => $attributeName,
            'model' => $model,
            'folderPath' => $this->folderPath

        ] = $this->setting;

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
            return (isset($files) && !empty($files)) ? $this->getFileContents($files) : null;
        } else {
            return null;
        }
    }






    /** ========== upload ด้วย attribute ที่ส่งมา ========== */

    private function uploadwithAttribute($attributeName)
    {
        $files = UploadedFile::getInstancesByName($attributeName);

        if (count($files) >= 1) {
            return (isset($files) && !empty($files)) ? $this->getFileContents($files) : null;
        } else {
            return null;
        }
    }




    /** ========== method สุดท้าย เพื่อดึงค่า json file content ที่ถูกสร้าง ========== */

    public function save()
    {
        return json_encode($this->fileContents[0]);
    }

    public function multipleSave()
    {
        return json_encode($this->fileContents);
    }

    public function getJson()
    {
        return json_encode($this->fileContents);
    }





    /** ========== สร้าง json string ที่จะ return ออกไป ========== */

    public function getFileContents($files)
    {
        $summaryFileContents = [];
        foreach ($files as $file) {
            $summaryFileContents[] = $this->setupFileContents($file);
        }

        return $summaryFileContents;
    }




    /** ========== สร้าง array ข้อมูลของไฟล์ที่อัปโหลด ========== */

    private function setupFileContents($file)
    {

        $fileName = $this->generateFileName($file);
        $folderPath = $this->normalizefolderPath();
        $this->createDirectory($folderPath);

        $allFileContents = [];
        if ($file->saveAs($folderPath . $fileName)) {
            $allFileContents = [
                'key' => $this->generateKey4File(),
                'originalName' => $file->baseName . '.' . $file->extension,
                'name' => $fileName,
                'extension' => $file->extension,
                'size' => $file->size,
                'type' => $file->type,
                'folder' => $this->folderPath,
            ];
        }

        return $allFileContents;
    }

    /** ========== ฟังก์ชั่น ดึง key เพื่อเก็บลง database ========== */
    public function generateKey4File($length = 10)
    {
        $randomString = substr(
            str_shuffle(
                str_repeat(
                    $x = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ',
                    ceil($length / strlen($x))
                )
            ),
            1,
            $length
        );

        return md5($randomString . time() . uniqid());
    }

    public static function generateKey($length = 10)
    {
        $randomString = substr(
            str_shuffle(
                str_repeat(
                    $x = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ',
                    ceil($length / strlen($x))
                )
            ),
            1,
            $length
        );

        return md5($randomString . time() . uniqid());
    }








    /** ========== สร้างชื่อไฟล์แบบ random ========== */

    private function generateFileName($file)
    {
        $fileName = md5($file->baseName . time() . uniqid()) . '.' . $file->extension;

        return $fileName;
    }




    /** ========== เรียก path ที่ถูกต้องของ folder ที่เก็บ ========== */

    private function normalizefolderPath()
    {
        $folderPath = realpath(dirname(__FILE__) . '/../../') . $this->folderPath;
        $folderPath = str_replace('\\', '/', $folderPath);
        $folderPath = explode('/', $folderPath);

        $normalizefolderPath = join('/', array_merge($folderPath));
        $normalizefolderPath = preg_replace('/(\/+)/', '/', $normalizefolderPath);

        if (substr($normalizefolderPath, -1) != '/') {
            $normalizefolderPath = $normalizefolderPath . '/';
        }

        return $normalizefolderPath;
    }






    /** ========== สร้าง folder ========== */

    private function createDirectory($folderPath)
    {
        if (!is_dir($folderPath)) {
            FileHelper::createDirectory($folderPath, 0777);
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

    public function saveOnTable($model)
    {

        $fileContent = $this->fileContents;

        if (!empty($fileContent)) {
            $modelTableName = $model->tableName();

            $fileRow = [];
            foreach ($fileContent as $file) {
                $fileRow[] = [
                    $this->fieldFileKey => $file['key'],
                    $this->fieldFileContent => json_encode($file),
                ];
            }

            $fileField = [
                $this->fieldFileKey,
                $this->fieldFileContent,
            ];

            $batchInsert = Yii::$app->db
                ->createCommand()
                ->batchInsert($modelTableName, $fileField, $fileRow)
                ->execute();

            // $batchInsert จะ return ออกมาเป็นตัวเลขจำนวนของ files ที่บันทึก
            if (!empty($batchInsert) && is_numeric($batchInsert)) {
                return true;
            } else {
                return null;
            }
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

    public static function mutateFileContents($fileContents)
    {
        return is_array($fileContents) ? $fileContents : json_decode($fileContents, true);
    }

    public static function getOriginalFileName($fileContents)
    {
        $file = self::mutateFileContents($fileContents);
        return isset($file['originalName']) ? $file['originalName'] : null;
    }

    public static function getFileName($fileContents)
    {
        $file = self::mutateFileContents($fileContents);
        return isset($file['name']) ? $file['name'] : null;
    }

    public static function getFileKey($fileContents)
    {
        $file = self::mutateFileContents($fileContents);
        return isset($file['key']) ? $file['key'] : null;
    }

    public static function getFileExtension($fileContents)
    {
        $file = self::mutateFileContents($fileContents);
        return isset($file['extension']) ? $file['extension'] : null;
    }

    public static function getFileSize($fileContents)
    {
        $file = self::mutateFileContents($fileContents);
        return isset($file['size']) ? $file['size'] : null;
    }

    public static function getFileType($fileContents)
    {
        $file = self::mutateFileContents($fileContents);
        return isset($file['type']) ? $file['type'] : null;
    }

    public static function getFolderPath($fileContents)
    {
        $file = self::mutateFileContents($fileContents);
        return isset($file['folder']) ? $file['folder'] : null;
    }






    /** ========== ฟังก์ชั่นเกี่ยวกับเรียกรูปภาพ ========== */

    public static function encodeText($text)
    {
        $text = str_replace(array('+', '/'), array('-', '_'), base64_encode($text));
        $text = substr_replace($text, 'rbjGiVvjWF', 1, 0);
        return strrev($text);
    }

    public static function decodeText($text)
    {
        $text = strrev($text);
        $text = str_replace('rbjGiVvjWF', '', $text);
        $text = base64_decode(str_replace(array('-', '_'), array('+', '/'), $text));
        return $text;
    }

    public static function getFileUrl($fileContents)
    {
        $file = json_decode($fileContents, true);
        $encodeText = self::encodeText($file['folder'] . '|' . $file['name'] . '|' . $file['type']);

        return $encodeText;
    }

    public static function revealFile($urlEncode)
    {
        try {
            $response = new Response;

            $urlEncode = self::decodeText($urlEncode);
            $file = explode('|', $urlEncode);

            $filePath = realpath(dirname(__FILE__) . '/../../') . $file[0] . $file[1];

            return $response->sendFile($filePath, $file[1], [
                'mimeType' => $file[2],
                'inline' => true,
            ]);
        } catch (\Throwable $th) {
            // return $th->getMessage();
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
