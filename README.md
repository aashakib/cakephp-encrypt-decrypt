# CakePHP Encrypt Decrypt
A CakePHP library to encrypt and decrypt data. 

### Features

- Encrypt data when saving and decrypt data when fetching data from database.
- Encrypt & decrypt historical data.

### Install

Via Composer

For CakePHP 4 & CakePHP 3.4+:

`composer require aashakib/cakephp-encrypt-decrypt`

For CakePHP <=3.3:

`composer require shakib/cakephp-encrypt-decrypt`

### Setup

Add the type in `bootstrap.php`
``` php
Type::map('encrypted', 'EncryptDecrypt\Database\Type\EncryptType');
```
Add config value in `config\app.php`
``` php
'Security' => [
    'encryption_key' => env('ENCRYPTION_KEY', 'YOUR-KEY'),
]
```

### Uses
Table structure: Use `BLOB \ VARBINARY` type for those columns you are want to be encrypted. Such as:
``` sql
CREATE TABLE `accounts`(
    `id` INT NOT NULL AUTO_INCREMENT,
    `full_name` VARCHAR(100) NOT NULL,    
    `account_number` VARBINARY(255) NOT NULL,    
    `email` VARBINARY(255) NOT NULL,    
    `created` DATETIME NOT NULL,
    `modified` DATETIME NULL,
    PRIMARY KEY(`id`)
) ENGINE = InnoDB;
```

Map all columns in your Table class.
``` php
use Cake\ORM\Table;
use Cake\Database\Schema\TableSchema;
use EncryptDecrypt\Traits\EncryptDecrypt;

class AccountsTable extends Table
{

   use EncryptDecrypt;
    
   /**
    * @param TableSchema $schema
    * @return TableSchema
    */
    protected function _initializeSchema(TableSchema $schema)
    {
      $schema->setColumnType('account_number', 'encrypted');
      $schema->setColumnType('email', 'encrypted');
      
      return $schema;
    }
}
```

To encrypt or decrypt historical data, add this method in your table class and run
``` php
public function encryptDecryptAllData()
{
  // columns that are in plain text
  $sourceColumns = ['column1', 'column2']; 	
  // columns that need to be encrypted / decrypted
  $destinationColumns = ['column3', 'column4'];

  return $this->encryptAll($this, $sourceColumns, $destinationColumns);
}
```
