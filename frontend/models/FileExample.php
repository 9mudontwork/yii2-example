<?php

namespace frontend\models;

use Yii;

/**
 * This is the model class for table "file_example".
 *
 * @property int $id
 * @property string|null $file_code
 * @property string|null $file_key
 */
class FileExample extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'file_example';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['file_code'], 'string'],
            [['file_key'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'file_code' => 'File Code',
            'file_key' => 'File Key',
        ];
    }
}
