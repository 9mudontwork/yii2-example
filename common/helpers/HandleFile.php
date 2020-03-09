<?php

namespace common\helpers;

use yii;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\UploadedFile;
use yii\helpers\FileHelper;

use common\helpers\P;

class HandleFile
{
    public $basePath;
    public $folder;
    public $model;
    public $prefixName;

    public function __construct($alias = null)
    {
        if ($alias !== null) {
            $this->basePath = Yii::getAlias('@' . $alias . '/web');
        }
    }

    public function doUpload($field, $model = null)
    {
        try {
            // have model
            if (self::issetNotEmpty($model)) {
                // have field
                if (self::issetNotEmpty($field)) {
                    // one field
                    if (!is_array($field)) {

                        $files = UploadedFile::getInstances($model, $field);

                        if (count($files) == 1) {
                            if (self::issetNotEmpty($files)) {

                                $jsonFileData = [];
                                foreach ($files as $file) {
                                    $jsonFileData[] = self::setupFileCode($file);
                                }

                                return json_encode($jsonFileData);
                            } else {
                                // upload fail
                                return false;
                            }
                        } else {
                            // multiple file
                            if (self::issetNotEmpty($files)) {

                                $jsonFileData = [];
                                foreach ($files as $file) {
                                    $jsonFileData[] = self::setupFileCode($file);
                                }

                                return json_encode($jsonFileData);
                            } else {
                                // upload fail
                                return false;
                            }
                        }
                    } else {
                        // multi field
                    }
                } else {
                    // not have field
                }
            } else {
                // not have model
            }
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    private function setupFileCode($file)
    {
        $fileName = self::generateFileName($file);
        $path = self::getCorrectPath();
        self::createDirectory($path);

        $jsonFileData = [];
        if ($file->saveAs($path . $fileName)) {
            $jsonFileData = [
                'key' => self::pullFileKey($fileName),
                'originalName' => $file->baseName . '.' . $file->extension,
                'name' => $fileName,
                'extension' => $file->extension,
                'size' => $file->size,
                'type' => $file->type,
                'basePath' => self::getCorrectBasePath($this->basePath),
                'folder' => self::getCorrectFolder($this->folder),
            ];
        }

        return $jsonFileData;
    }

    public function getCorrectPath()
    {
        $basePath = str_replace('\\', '/', $this->basePath);
        $basePath = explode('/', $basePath);

        $folder = str_replace('\\', '/', $this->folder);
        $folder = explode('/', $folder);

        $pathUploadLocation = join('/', array_merge($basePath, $folder));
        $pathUploadLocation = preg_replace('/(\/+)/', '/', $pathUploadLocation);
        // $pathUploadLocation = FileHelper::normalizePath($pathUploadLocation);

        if (substr($pathUploadLocation, -1) != '/') {
            $pathUploadLocation = $pathUploadLocation . '/';
        }

        return $pathUploadLocation;
    }

    public function getCorrectBasePath($basePath)
    {
        $basePath = str_replace('\\', '/', $basePath);
        $basePath = explode('/', $basePath);

        $basePath = join('/', $basePath);
        $basePath = preg_replace('/(\/+)/', '/', $basePath);

        if (substr($basePath, -1) != '/') {
            $basePath = $basePath . '/';
        }

        return $basePath;
    }

    public function getCorrectFolder($folder)
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


    private function createDirectory($folder)
    {
        if (!is_dir($folder)) {
            FileHelper::createDirectory($folder, 0777);
        }
    }

    private function generateFileName($file)
    {
        $fileName = md5($file->baseName . time() . uniqid()) . '.' . $file->extension;

        if (self::issetNotEmpty($this->prefixName)) {
            return $this->prefixName . $fileName;
        } else {
            return $fileName;
        }
    }

    private function pullFileKey($fileName)
    {
        $fileName = explode('.', $fileName);
        $fileName = $fileName[0];

        return $fileName;
    }

    private function issetNotEmpty($value)
    {
        return (isset($value) && !empty($value)) ? true : false;
    }

    /** GET File Data */

    public function getFileOriginalName($jsonFileData)
    {
        $file = json_decode($jsonFileData, true);

        return $file['originalName'];
    }

    public function getFileName($jsonFileData)
    {
        $file = json_decode($jsonFileData, true);

        return $file['name'];
    }

    public function getFileKey($jsonFileData)
    {
        $file = json_decode($jsonFileData, true);

        return $file['key'];
    }

    public function getFileExtension($jsonFileData)
    {
        $file = json_decode($jsonFileData, true);

        return $file['extension'];
    }

    public function getFileSize($jsonFileData)
    {
        $file = json_decode($jsonFileData, true);

        return $file['size'];
    }

    public function getFileType($jsonFileData)
    {
        $file = json_decode($jsonFileData, true);

        return $file['type'];
    }

    public function getFileUrl($jsonFileData)
    {
        $file = json_decode($jsonFileData, true);
        $filePath = self::getCorrectFolder($file['folder']) . $file['name'];

        return Url::base(true) . $filePath;
    }

    public function getFileLocation($jsonFileData)
    {
        $file = json_decode($jsonFileData, true);
        $this->basePath = $file['basePath'];
        $this->folder = $file['folder'];

        $fileName = $file['name'];

        return self::getCorrectPath() . $fileName;
    }

    public static function downloadFile($jsonFileData)
    {
        $file = json_decode($jsonFileData, true);

        $basePath = str_replace('\\', '/', $file['basePath']);
        $folder = str_replace('\\', '/', $file['folder']);
        $fileName = $file['name'];

        $filePath = $basePath . $folder . $fileName;

        return Yii::$app->response->sendFile($filePath, $file['originalName']);
    }

    public static function removeFile($jsonFileData)
    {
        $fileLocation = self::getFileLocation($jsonFileData);
        FileHelper::unlink($fileLocation);
    }

    public function andUseIn($alias, $jsonFileData)
    {
        $file = json_decode($jsonFileData, true);

        $this->basePath = Yii::getAlias('@' . $alias . '/web');
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
        // TODO
    }
}
