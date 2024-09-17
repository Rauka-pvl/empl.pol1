<?php

namespace App\Http\Controllers;

use App\Models\KabGrade;
use App\Models\Kabinet;
use App\Models\Tab;
use Illuminate\Http\Request;
use Storage;

class KabinetController extends Controller
{
    public function estimate(Request $request)
    {
        if (isset($request->grade)) {
            $image = $request->image;
            // $fileName = '123.png';

            $image_parts = explode(";base64,", $image);
            $image_type = $image_parts[0];
            $image_base64 = base64_decode($image_parts[1]);
            $fileName = uniqid() . '.png';

            Storage::put('public/grade/' . $fileName, $image_base64);
            if ($request->createD) {
                $data = [
                    'kab_id' => $request->kab_id,
                    'grade' => $request->grade,
                    'photo' => $fileName,
                    'created_at' => $request->createD ?? date('Y-m-d H:i:s'),
                    'updated_at' => $request->createD ?? date('Y-m-d H:i:s')
                ];
            } else {
                $data = [
                    'kab_id' => $request->kab_id,
                    'grade' => $request->grade,
                    'photo' => $fileName,
                ];
            }
            $grade = KabGrade::create($data);
            if ($grade->exists) {
                return json_encode(true);
            } else {
                $error = 'Error!';
                return view('auth.login', compact('error'));
            }
        }
    }
    public function getKabInfo(Request $request)
    {
        $kab = Kabinet::select('kabinets.*', 'sub.name as sub')->join('sub', 'sub.id', '=', 'kabinets.sub')->where('kabinets.kab', '=', $request->id)->where('kabinets.corpus', '=', $request->corpus)->first();
        return response()->json($kab);
    }
    public function checkTab(Request $request)
    {
        if ($request->kab) {
            $tab = Tab::where('kab', '=', (int) $request->kab)->first();
            if ($tab) {
                $tab->dataTime = date('Y-m-d H:i:s');
                $tab->save();
                return response()->json(true);
            } else {
                if (
                    Tab::create([
                        'kab' => $request->kab,
                        'corpus' => $request->corpus,
                        'dataTime' => date('Y-m-d H:i:s')
                    ])
                ) {
                    return response()->json(true);
                }
            }
        } else
            return response()->json(false);
    }
}
