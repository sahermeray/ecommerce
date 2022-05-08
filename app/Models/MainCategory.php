<?php

namespace App\Models;

use App\Observers\MainCategoryObserver;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;

class MainCategory extends Model
{
    use HasFactory;

    protected $table = 'main_categories';

    protected $fillable = ['name','translation_lang','translation_of','slug','photo','active','created_at','updated_at'];

    public function scopeActive($query){
        return $query->where('active',1);
    }

    public function scopeSelection($query){
        return $query->select('id','name','translation_lang','slug','photo','active','translation_of');
    }

    public function getActive(){
        return $this->active == 1 ? 'مفعل' : 'غير مفعل';
    }

    public function getPhotoAttribute($val){
        return ($val !== null) ? asset('assets/'.$val) : "";
    }

    //هنا علاقة has many ضمن نفس الجدول ...حيث translation_of هو الforeign key
    // وهي تعطينا ترجمات ال main category
    public function categories(){
        return $this->hasMany(self::class,'translation_of');
    }

    public function subCategories(){
        return $this->hasMany(SubCategory::class,'category_id','id');
    }

    public function vendors(){
        return $this->hasMany('App\Models\Vendor','category_id','id');
    }

    protected static function boot(){
        parent::boot();
        MainCategory::observe(MainCategoryObserver::class);
    }

    public function scopeDefaultCategory($query){
        return $query->where('translation_of',0);
    }
}
