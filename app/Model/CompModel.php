<?php
namespace App\Model;

// use GeneaLabs\LaravelModelCaching\Traits\Cachable;
use Illuminate\Database\Eloquent\Model;
use App;
use User;
use Auth;

class CompModel extends Model
{
  // use Cachable;

  protected $connection = 'IOTRC001';

  public function __construct($connection = null, $attributes = array())
  {
    if($connection) {
      $this->connection = $connection;

    } elseif(isset(Auth::user()->company_code)) {
          $this->connection = Auth::user()->company_code;
    
    } else {
        $this->connection = 'IOTRC001';
    }
    // config(['laravel-model-caching.cache-prefix' => $this->connection]);
    
    parent::__construct($attributes);
  }

  protected static function boot()
  {
    parent::boot();

  }
}
