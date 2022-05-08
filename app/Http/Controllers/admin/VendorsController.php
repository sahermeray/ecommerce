<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\VendorRequest;
use App\Models\MainCategory;
use App\Models\Vendor;
use App\Notifications\VendorNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use DB;
use Illuminate\Support\Str;

class VendorsController extends Controller
{
    public function index(){
        $vendors = Vendor::selection()->paginate(PAGINATION_COUNT);
        return view('admin.vendors.index',compact('vendors'));
    }

    public function create(){
        $categories = MainCategory::where('translation_of',0)->active()->get();
        return view('admin.vendors.create',compact('categories'));
    }

    public function store(VendorRequest $request){
        try {
            if (!$request->has('active')) {
                $request->request->add(['active' => 0]);
            } else {
                $request->request->add(['active' => 1]);
            }

            $filePath = "";
            if ($request->has('logo')) {
                $filePath = uploadImage('vendors', $request->logo);
            }

            $vendor = Vendor::create([
                'name' => $request->name,
                'mobile' => $request->mobile,
                'email' => $request->email,
                'active' => $request->active,
                'address' => $request->address,
                'logo' => $filePath,
                'category_id' => $request->category_id,
                'password' => bcrypt($request->password)

            ]);
            Notification::send($vendor,new VendorNotification($vendor));

            return redirect()->route('admin.vendors')->with(['success'=>'تم حفظ التاجر بنجاح']);
        }catch (\Exception $ex){

            return redirect()->route('admin.vendors')->with(['error'=>'حدث خطأ ما']);
        }
    }

    public function edit($id){
        try{
            $categories = MainCategory::where('translation_of',0)->active()->get();
            $vendor = Vendor::selection()->find($id);
            if(!$vendor){
                return redirect()->route('admin.vendors')->with(['error'=>'حدث خطأ ما']);
            }
            return view('admin.vendors.edit',compact('vendor','categories'));

        }catch(\Exception $ex){
            return redirect()->route('admin.vendors')->with(['error'=>'حدث خطأ ما']);
        }

    }

    public function update(VendorRequest $request,$id)
    {
        try {
            $vendor = Vendor::selection()->find($id);
            if (!$vendor) {
                return redirect()->route('admin.vendors')->with(['error' => 'حدث خطأ ما']);
            }

            if(!$request->has('active')){
                $request->request->add(['active'=>0]);
            }else{
                $request->request->add(['active'=>1]);
            }

            DB::beginTransaction();
            if ($request->has('logo')) {
                $filePath = uploadImage('vendors', $request->logo);
                Vendor::where('id', $id)->update([
                    'logo' => $filePath,
                ]);
            }

            if ($request->has('password') && $request->password !=null) {
                Vendor::where('id', $id)->update([
                    'password' => $request->password
                ]);
            }

            Vendor::where('id', $id)->update([
                'name'=>$request->name,
                'mobile'=>$request->mobile,
                'email'=>$request->email,
                'category_id'=>$request->category_id,
                'address'=>$request->address,
                'active'=>$request->active
                ]);
            DB::commit();
            return redirect()->route('admin.vendors')->with(['success'=>'تم التعديل التاجر بنجاح']);

        }catch (\Exception $ex){
            DB::rollback();
            return redirect()->route('admin.vendors')->with(['error'=>'حدث خطأ ما']);
        }
    }

    public function destroy($id){
        try {
            $vendor = Vendor::find($id);
            if(!$vendor){
                return redirect()->route('admin.vendors')->with(['error'=>'هذا المتجر غير موجود']);
            }

            // تستخدم unlike لحذف الصورة من مجلدات المشروع ولكنها لا تقبل http لذلك نقوم بأخذ المسار الذي تعيده getPhotoAttribute من بعد كلمة assets
            $image = Str::after($vendor->logo,'assets/');
            // ايضا تحتاج unlike لمسار الصورة ابتداء من app...لذلك نستخدم base_path لنحصل على هذا المسار ونضيف له assets
            $image = base_path('assets/'.$image);
            // الان نستطيع الحذف
            unlink($image);

            $vendor->delete();
            return redirect()->route('admin.vendors')->with(['success'=>'تم الحذف بنجاح']);
        }catch (\Exception $ex){
            return redirect()->route('admin.vendors')->with(['error'=>'حدث خطأ ما']);
        }
    }

    public function changeStatus($id){
        try{
            $vendor = Vendor::find($id);
            if(!$vendor){
                return redirect()->route('admin.vendors')->with(['error'=>'هذا القسم غير موجود']);
            }
            $status = $vendor->active == 0 ? 1 : 0;
            $vendor->update(['active'=>$status]);
            return redirect()->route('admin.vendors')->with(['success'=>'تم تغيير الحالة بنجاح']);

        }catch (\Exception $ex){
            return redirect()->route('admin.vendors')->with(['error'=>'حدث خطأ ما']);
        }
    }
}
