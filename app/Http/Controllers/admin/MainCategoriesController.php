<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MainCategoriesRequest;
use App\Models\MainCategory;
use Illuminate\Http\Request;
use DB;
use Illuminate\Support\Str;


class MainCategoriesController extends Controller
{
    public function index(){
        $default_lang = get_default_lang();
        $categories = MainCategory::where('translation_lang',$default_lang)->selection()->get();
        return view('admin.mainCategories.index',compact('categories'));
    }

    public function create(){
        return view('admin.mainCategories.create');
    }

    public function store(MainCategoriesRequest $request){

        try {
            //حولنا ال array القادمة من ال request الى collection لنتمكن من القيام ب filter والبحث عن ال category باللغة الاساسية
            $main_categories = collect($request->category);
            // هنا نقوم بالبحث
            $filter = $main_categories->filter(function ($value, $key) {
                return $value['abbr'] == get_default_lang();
            });
            // هنا حولنا ناتج البحث الى array
            $default_category = array_values($filter->all())[0];

            $filePath = "";
            if ($request->has('photo')) {
                $filePath = uploadImage('maincategories', $request->photo);
            }
            DB::beginTransaction();
            $default_category_id = MainCategory::insertGetId([
                'translation_lang' => $default_category['abbr'],
                'translation_of' => 0,
                'name' => $default_category['name'],
                'slug' => $default_category['name'],
                'photo' => $filePath
            ]);

            $categories = $main_categories->filter(function ($value, $key) {
                return $value['abbr'] != get_default_lang();
            });


            if (isset($categories) && $categories->count()) {
                $categories_arr = [];
                foreach ($categories as $category) {
                    $categories_arr[] = [
                        'translation_lang' => $category['abbr'],
                        'translation_of' => $default_category_id,
                        'name' => $category['name'],
                        'slug' => $category['name'],
                        'photo' => $filePath,
                    ];
                }

                MainCategory::insert($categories_arr);
            }
            DB::commit();
            return redirect()->route('admin.maincategories')->with(['success'=>'تم الحفظ بنجاح']);
        }catch (\Exception $ex){
            DB::rollback();
            return redirect()->route('admin.maincategories')->with(['error'=>'حدث خطأ']);
        }

    }

    public function edit($id){
        //get specific category and its translations
        $category =  MainCategory::with('categories')->selection()->find($id);
        if(!$category){
            return redirect()->route('admin.maincategories')->with(['error'=>'هذا القسم غير موجود']);
        }
        return view('admin.maincategories.edit',compact('category'));
    }

    public function update(MainCategoriesRequest $request,$id){
        $category =  MainCategory::find($id);
        if(!$category){
            return redirect()->route('admin.maincategories')->with(['error'=>'هذا القسم غير موجود']);
        }
        $updated_category = array_values($request->category)[0];

        if(!$request->has('category.0.active')){
            $request->request->add(['active'=>0]);
        }else{
            $request->request->add(['active'=>1]);
        }

        MainCategory::where('id',$id)->update([
            'name' => $updated_category['name'],
            'active' => $request->active,
        ]);


        if ($request->has('photo')) {
            $filePath = uploadImage('maincategories', $request->photo);
            MainCategory::where('id',$id)->update([
                'photo' => $filePath,
            ]);
        }

        return redirect()->route('admin.maincategories')->with(['success'=>'تم التحديث بنجاح']);
    }

    public function destroy($id){
        try {
            $main_category = MainCategory::find($id);
            if(!$main_category){
                return redirect()->route('admin.maincategories')->with(['error'=>'هذا القسم غير موجود']);
            }
            $vendors = $main_category->vendors();
            if(isset($vendors) && $vendors->count()>0){
                return redirect()->route('admin.maincategories')->with(['error'=>'لا يجوز حذف هذا القسم']);
            }
           // تستخدم unlike لحذف الصورة من مجلدات المشروع ولكنها لا تقبل http لذلك نقوم بأخذ المسار الذي تعيده getPhotoAttribute من بعد كلمة assets
            $image = Str::after($main_category->photo,'assets/');
            // ايضا تحتاج unlike لمسار الصورة ابتداء من app...لذلك نستخدم base_path لنحصل على هذا المسار ونضيف له assets
            $image = base_path('assets/'.$image);
            // الان نستطيع الحذف
            unlink($image);
            // الاان نقوم بحذف ترجمات الmain category
            $main_category->categories()->delete();


            $main_category->delete();
            return redirect()->route('admin.maincategories')->with(['success'=>'تم الحذف بنجاح']);
        }catch (\Exception $ex){
            return redirect()->route('admin.maincategories')->with(['error'=>'حدث خطأ ما']);
        }
    }

    public function changeStatus($id){
        try{
            $main_category = MainCategory::find($id);
            if(!$main_category){
                return redirect()->route('admin.maincategories')->with(['error'=>'هذا القسم غير موجود']);
            }
            $status = $main_category->active == 0 ? 1 : 0;
            $main_category->update(['active'=>$status]);
            return redirect()->route('admin.maincategories')->with(['success'=>'تم تغيير الحالة بنجاح']);

        }catch (\Exception $ex){
            return redirect()->route('admin.maincategories')->with(['error'=>'حدث خطأ ما']);
        }

    }
}
