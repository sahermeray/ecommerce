<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Vendor extends Model
{
    use Notifiable;

    protected $table = 'vendors';

    protected $fillable = ['name','mobile','address','email','password','category_id','active','created_at','updated_at','logo'];

    protected $hidden = ['category_id','password'];

   public function scopeActive($query){
       return $query->where('active',1);
   }

    public function getLogoAttribute($val){
        return ($val !== null) ? asset('assets/'.$val) : "";
    }

    public function getActive(){
        return $this->active == 1 ? 'مفعل' : 'غير مفعل';
    }

    public function scopeSelection($query){
        return $query->select('id','name','active','category_id','address','email','logo','mobile');
    }

    public function category(){
        return $this->belongsTo('App\Models\MainCategory','category_id','id');
    }

}
