<?php

// จัดการ model
// muhammad

namespace common\components;

use Yii;
use common\components\_;

class _Model
{
    private $transaction;
    public  $errorMessage = [];

    public function __construct()
    {
        $this->transaction = Yii::$app->db->beginTransaction();
    }

    public function commit()
    {
        return $this->transaction->commit();
    }

    public function rollback()
    {
        return $this->transaction->rollBack();
    }

    private function setErrorMessage($message)
    {
        array_push($this->errorMessage, $message);
    }

    public function getStringErrorMessage()
    {
        return _::toJsonString($this->errorMessage);
    }

    public function save($model, array $data)
    {
        if (_::issetNotEmpty($data)) {
            foreach ($data as $key => $value) {
                $model->$key = _::getValue($value);
            }
        }

        if ($model->save() && $model->validate()) {
            return $this;
        } else {
            $this->setErrorMessage($model->getFirstErrors());
            throw new \Exception($this->getStringErrorMessage());
        }
    }
}
