<?php

namespace App\Models;

use \Core\Model;

/**
 * Definition of TableData Model class
 */
class TableData extends Model
{
    // database table name to which model class belongs
    public $table = 'table_data';

    // table primary key
    public $primaryKey = 'id';

    public $saltKey = 'xT0opE3D';
}
