<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\LanguageRequest;
use App\Models\Language;
use Illuminate\Http\Request;

class LanguagesController extends Controller
{
    public function index(){
        $languages = Language::select()->paginate(PAGINATION_COUNT);
        return view('admin.languages.index',compact('languages'));
    }

    public function create(){
        return view('admin.languages.create');
    }

    public function store(LanguageRequest $request){
        try{
            if(!$request->has('active')){
              $request->request->add(['active' => 0]);
            }

            Language::create($request->except(['_token']));
            return redirect()->route('admin.languages')->with(['success'=>'تم حفظ اللغة بنجاح']);

        }catch(\Exception $ex){
            return redirect()->route('admin.languages')->with(['error'=>'هناك خطأ']);
        }

    }

    public function edit($id){
        $language = Language::select()->find($id);
        if(!$language){
            return redirect()->route('admin.languages')->with(['error'=>'هذه اللغة غير موجودة']);
        }
        return view('admin.languages.edit',compact('language'));
    }

    public function update(LanguageRequest $request,$id){
        $language = Language::find($id);
        if(!$language){
            return redirect()->route('admin.languages.edit',$id)->with(['error'=>'هذه اللغة غير موجودة']);
        }
        try{
            if(!$request->has('active')){
               $request->request->add(['active' => 0]);
            }else{
                $request->request->add(['active' => 1]);
            }
            $language->update($request->except(['_token']));
            return redirect()->route('admin.languages')->with(['success'=>'تم تعديل اللغة بنجاح']);

        }catch(\Exception $ex){
            return redirect()->route('admin.languages')->with(['error'=>'هناك خطأ']);
        }
    }

    public function destroy($id){
        $language = Language::find($id);
        if(!$language){
            return redirect()->route('admin.languages')->with(['error'=>'هذه اللغة غير موجودة']);
        }
        try{
            $language->delete();
            return redirect()->route('admin.languages')->with(['success'=>'تم حذف اللغة بنجاح']);

        }catch(\Exception $ex){
            return redirect()->route('admin.languages')->with(['error'=>'هناك خطأ']);
        }
    }
}
