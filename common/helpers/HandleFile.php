<?php

namespace common\helpers;

use yii;
use yii\helpers\Url;
use yii\web\UploadedFile;
use yii\helpers\FileHelper;
use yii\web\Response;




// ███╗   ███╗██╗   ██╗         
// ████╗ ████║██║   ██║         
// ██╔████╔██║██║   ██║         
// ██║╚██╔╝██║██║   ██║         
// ██║ ╚═╝ ██║╚██████╔╝         
// ╚═╝     ╚═╝ ╚═════╝          

// ██╗   ██╗     ██╗    ██████╗ 
// ██║   ██║    ███║   ██╔═████╗
// ██║   ██║    ╚██║   ██║██╔██║
// ╚██╗ ██╔╝     ██║   ████╔╝██║
//  ╚████╔╝      ██║██╗╚██████╔╝
//   ╚═══╝       ╚═╝╚═╝ ╚═════╝ 




class HandleFile
{
    private $basePath;

    public $folder;
    public $model;
    public $prefixName;
    public $alias;

    public function doUpload($field, $model = null)
    {
        try {
            // have model
            if ($this->issetNotEmpty($model)) {
                // have field
                if ($this->issetNotEmpty($field)) {
                    // one field
                    if (!is_array($field)) {

                        $files = UploadedFile::getInstances($model, $field);

                        if (count($files) == 1) {
                            // one file
                            if ($this->issetNotEmpty($files)) {

                                return $this->oneFile($files);
                            } else {
                                // upload fail
                                return false;
                            }
                        } else {
                            // multiple file
                            if ($this->issetNotEmpty($files)) {

                                return $this->manyFile($files);
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


    // ███████╗██╗   ██╗███╗   ██╗ ██████╗████████╗██╗ ██████╗ ███╗   ██╗
    // ██╔════╝██║   ██║████╗  ██║██╔════╝╚══██╔══╝██║██╔═══██╗████╗  ██║
    // █████╗  ██║   ██║██╔██╗ ██║██║        ██║   ██║██║   ██║██╔██╗ ██║
    // ██╔══╝  ██║   ██║██║╚██╗██║██║        ██║   ██║██║   ██║██║╚██╗██║
    // ██║     ╚██████╔╝██║ ╚████║╚██████╗   ██║   ██║╚██████╔╝██║ ╚████║
    // ╚═╝      ╚═════╝ ╚═╝  ╚═══╝ ╚═════╝   ╚═╝   ╚═╝ ╚═════╝ ╚═╝  ╚═══╝


    public function oneFile($files)
    {
        $jsonFileData = [];
        foreach ($files as $file) {
            $jsonFileData = $this->setupFileCode($file);
        }

        return json_encode($jsonFileData);
    }

    public function manyFile($files)
    {
        $jsonFileData = [];
        foreach ($files as $file) {
            $jsonFileData[] = $this->setupFileCode($file);
        }

        return json_encode($jsonFileData);
    }

    public function useIn($alias, $folder)
    {
        $this->setBasePath($alias);
        $this->alias = $alias;
        $this->folder = $folder;
    }

    private function setBasePath($alias)
    {
        return $this->basePath = Yii::getAlias('@' . $alias);
    }

    private function setupFileCode($file)
    {
        $fileName = $this->generateFileName($file);
        $localPath = $this->getLocalPath();
        $this->createDirectory($localPath);

        $jsonFileData = [];
        if ($file->saveAs($localPath . $fileName)) {
            $jsonFileData = [
                'key' => $this->pullFileKey($fileName),
                'originalName' => $file->baseName . '.' . $file->extension,
                'name' => $fileName,
                'extension' => $file->extension,
                'size' => $file->size,
                'type' => $file->type,
                'alias' => $this->alias,
                'folder' => $this->normalizeFolder($this->folder),
            ];
        }

        return $jsonFileData;
    }

    private function generateFileName($file)
    {
        $fileName = md5($file->baseName . time() . uniqid()) . '.' . $file->extension;

        if ($this->issetNotEmpty($this->prefixName)) {
            return $this->prefixName . $fileName;
        } else {
            return $fileName;
        }
    }

    private function getLocalPath()
    {
        $basePath = str_replace('\\', '/', $this->basePath);
        $basePath = explode('/', $basePath);

        $folder = str_replace('\\', '/', $this->folder);
        $folder = explode('/', $folder);

        $localPath = join('/', array_merge($basePath, $folder));
        $localPath = preg_replace('/(\/+)/', '/', $localPath);
        // $localPath = FileHelper::normalizePath($localPath);

        if (substr($localPath, -1) != '/') {
            $localPath = $localPath . '/';
        }

        return $localPath;
    }

    private function getLocalFilePath($fileCode)
    {
        $file = json_decode($fileCode);

        $this->basePath = $this->setBasePath($file->alias);
        $this->folder = $file->folder;

        $localFilePath = $this->getLocalPath() . $file->name;

        return $localFilePath;
    }

    private function createDirectory($folder)
    {
        if (!is_dir($folder)) {
            FileHelper::createDirectory($folder, 0777);
        }
    }

    private function pullFileKey($fileName)
    {
        $fileName = explode('.', $fileName);
        $fileName = $fileName[0];

        return $fileName;
    }

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

    private function issetNotEmpty($value)
    {
        return (isset($value) && !empty($value)) ? true : false;
    }



    //  ██████╗ ███████╗████████╗    ███████╗██╗██╗     ███████╗    ██████╗  █████╗ ████████╗ █████╗ 
    // ██╔════╝ ██╔════╝╚══██╔══╝    ██╔════╝██║██║     ██╔════╝    ██╔══██╗██╔══██╗╚══██╔══╝██╔══██╗
    // ██║  ███╗█████╗     ██║       █████╗  ██║██║     █████╗      ██║  ██║███████║   ██║   ███████║
    // ██║   ██║██╔══╝     ██║       ██╔══╝  ██║██║     ██╔══╝      ██║  ██║██╔══██║   ██║   ██╔══██║
    // ╚██████╔╝███████╗   ██║       ██║     ██║███████╗███████╗    ██████╔╝██║  ██║   ██║   ██║  ██║
    //  ╚═════╝ ╚══════╝   ╚═╝       ╚═╝     ╚═╝╚══════╝╚══════╝    ╚═════╝ ╚═╝  ╚═╝   ╚═╝   ╚═╝  ╚═╝



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





    // ██╗  ██╗ █████╗ ███╗   ██╗██████╗ ██╗     ███████╗    ███████╗██╗██╗     ███████╗
    // ██║  ██║██╔══██╗████╗  ██║██╔══██╗██║     ██╔════╝    ██╔════╝██║██║     ██╔════╝
    // ███████║███████║██╔██╗ ██║██║  ██║██║     █████╗      █████╗  ██║██║     █████╗  
    // ██╔══██║██╔══██║██║╚██╗██║██║  ██║██║     ██╔══╝      ██╔══╝  ██║██║     ██╔══╝  
    // ██║  ██║██║  ██║██║ ╚████║██████╔╝███████╗███████╗    ██║     ██║███████╗███████╗
    // ╚═╝  ╚═╝╚═╝  ╚═╝╚═╝  ╚═══╝╚═════╝ ╚══════╝╚══════╝    ╚═╝     ╚═╝╚══════╝╚══════╝

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
