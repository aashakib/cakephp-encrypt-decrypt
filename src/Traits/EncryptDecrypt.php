<?php

namespace EncryptDecrypt\Traits;

use Cake\Log\LogTrait;
use Cake\ORM\Table;
use Cake\Utility\Security;

/**
 * Trait EncryptDecrypt
 * @package App\Traits
 */
trait EncryptDecrypt
{
    use LogTrait;

    /**
     * Encrypt text using AES-256
     * @param $text
     * @return string|null
     */
    public function encrypt($text){
        if (empty($text) || ($this->getKey() === null) ){
            return null;
        }

        $encrypt = Security::encrypt($text, $this->getKey());

        return $encrypt;
    }

    /**
     * Decrypt text
     * @param $text
     * @return string|null
     */
    public function decrypt($text){
        if (empty($text) || ($this->getKey() === null) ){
            return null;
        }

        $decrypt = Security::decrypt($text, $this->getKey());

        return $decrypt;
    }

    /**
     * Get key from app config
     * @return string
     */
    public function getKey(){
        $key = \Cake\Core\Configure::read('Security.encryption_key');

        return $key;
    }

    /**
     * @param Table $model
     * @param array $source
     * @param array $destination
     */
    public function encryptAll(Table $model, array $source, array $destination){
        $allColumns = array_merge([$model->getPrimaryKey()], $source);
        $getAll = $model->find()->select($allColumns);

        if($getAll->count() > 0){
            foreach ($getAll as $data){
                $encryptedData = [];
                foreach ($source as $col => $column){
                    if (!empty($column) && isset($data->{$column})){
                        if (!empty($data->{$column})){
                            $encryptedData[$destination[$col]] = $data->{$column};
                        }

                    }
                }


                if(!empty($encryptedData)){
                    $syncRow = $model->patchEntity($model->get($data->id), $encryptedData);
                    if (!$this->save($syncRow)){
                        $this->log(sprintf('Error in saving encrypted data in '.$model->getTable().' table, id:', $data->id), "info");
                    }
                }
            }
        }
    }

}
