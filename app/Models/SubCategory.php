<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubCategory extends Model
{
    use HasFactory;

    protected $table = 'sub_categories';

    protected $fillable = ['name','parent_id','translation_lang','translation_of','slug','photo','active','created_at','updated_at'];

    public function scopeActive($query){
        return $query->where('active',1);
    }

    public function scopeSelection($query){
        return $query->select('id','parent_id','name','translation_lang','slug','photo','active','translation_of');
    }

    public function getActive(){
        return $this->active == 1 ? 'مفعل' : 'غير مفعل';
    }

    public function getPhotoAttribute($val){
        return ($val !== null) ? asset('assets/'.$val) : "";
    }

    public function mainCategory(){
        return $this->belongsTo(MainCategory::class,'category_id','id');
    }

}
