<?php

namespace App\Http\Controllers;

use App\Models\M_Counter_Category;
use App\Models\M_Ticket_Category;
use App\Models\M_Color;
use App\Models\M_Config;
use App\Models\M_Group;
use App\Models\M_Counter;
use App\Models\M_Customer;
use App\Models\M_Role;
use App\Models\M_Status;
use App\Models\M_Ticket;
use App\Models\M_User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class C_Api extends Controller
{
  // CATEGORYL
  public function categoryLs()
  {
    $data = M_Counter_Category::with(['Color'])->orderBy('id', 'asc')->get();
    $transformedData = $data->map(function ($item) {
      $colorData = null;
      if ($item->color) {
        $colorData = [
          'id' => $item->color->id,
          'hexcode' => $item->color->hexcode,
        ];
      }

      return [
        'id' => $item->id,
        'name' => $item->name,
        'kode' => $item->kode,
        'color' => $colorData,
        'created_at' => Carbon::parse($item->created_at)->toDateTimeString(),
        'updated_at' => Carbon::parse($item->updated_at)->toDateTimeString(),
      ];
    });
    return response()->json([
      'status' => true,
      'message' => 'Data ditemukan',
      'data' => $transformedData,
    ], 200);
  }

  public function categoryL(string $id)
  {
    $data = M_Counter_Category::with(['Color'])->find($id);

    if ($data) {
      $colorData = null;
      if ($data->color) {
        $colorData = [
          'id' => $data->color->id,
          'hexcode' => $data->color->hexcode,
        ];
      }

      return response()->json([
        'status' => true,
        'message' => 'Data ditemukan',
        'data' => [
          'id' => $data->id,
          'name' => $data->name,
          'kode' => $data->kode,
          'color' => $colorData,
          'created_at' => Carbon::parse($data->created_at)->toDateTimeString(),
          'updated_at' => Carbon::parse($data->updated_at)->toDateTimeString(),
        ]
      ], 200);
    } else {
      return response()->json([
        'status' => false,
        'message' => 'Data tidak ditemukan'
      ]);
    }
  }

  public function categoryLStore(Request $request)
  {
    $rules = [
      'name' => 'required',
      'kode' => 'required',
      'hexcode' => 'required'
    ];

    $nullableAttributes = [];
    foreach ($nullableAttributes as $attribute) {
      if ($request->has($attribute)) {
        $rules[$attribute] = 'nullable';
      }
    }

    $validator = Validator::make($request->all(), $rules);
    if ($validator->fails()) {
      return response()->json([
        'status' => false,
        'message' => 'Gagal memasukkan data',
        'data' => $validator->errors()
      ]);
    }

    $color = M_Color::where('hexcode', $request->hexcode)->first();

    if (!$color) {
      $color = M_Color::create([
        'hexcode' => $request->hexcode
      ]);
    }

    $dataCategoryL = new M_Counter_Category;
    $dataCategoryL->name = $request->name;
    $dataCategoryL->kode = $request->kode;
    $dataCategoryL->color_id = $color->id;

    foreach ($nullableAttributes as $attribute) {
      if ($request->has($attribute)) {
        $dataCategoryL->$attribute = $request->$attribute;
      }
    }

    $dataCategoryL->save();

    return response()->json([
      'status' => true,
      'message' => 'Sukses memasukkan data'
    ]);
  }

  public function categoryLUpdate(Request $request, string $id)
  {
    $dataCategoryL = M_Counter_Category::find($id);
    if (empty($dataCategoryL)) {
      return response()->json([
        'status' => false,
        'message' => 'Data tidak ditemukan'
      ], 404);
    }

    $rules = [
      'name' => 'nullable',
      'kode' => 'nullable',
      'hexcode' => 'nullable'
    ];

    $validator = Validator::make($request->all(), $rules);
    if ($validator->fails()) {
      return response()->json([
        'status' => false,
        'message' => 'Gagal melakukan update data',
        'data' => $validator->errors()
      ]);
    }

    if ($request->has('hexcode')) {
      $color = M_Color::where('hexcode', $request->hexcode)->first();
      if (!$color) {
        $color = M_Color::create([
          'hexcode' => $request->hexcode
        ]);
      }
      $dataCategoryL->color_id = $color->id;
    }

    $dataCategoryL->fill($request->only(['name', 'kode']));
    $dataCategoryL->save();

    return response()->json([
      'status' => true,
      'message' => 'Sukses melakukan update data'
    ]);
  }

  public function categoryLDestroy(string $id)
  {
    $dataCategoryL = M_Counter_Category::find($id);
    if (empty($dataCategoryL)) {
      return response()->json([
        'status' => false,
        'message' => 'Data tidak ditemukan'
      ], 404);
    }
    $post = $dataCategoryL->delete();

    return response()->json([
      'status' => true,
      'message' => 'Sukses melakukan delete data'
    ]);
  }

  // CATEGORYT
  public function categoryTs()
  {
    $data = M_Ticket_Category::orderBy('id', 'asc')->get();
    return response()->json([
      'status' => true,
      'message' => 'Data ditemukan',
      'data' => $data
    ], 200);
  }

  public function categoryT(string $id)
  {
    $data = M_Ticket_Category::find($id);
    if ($data) {
      return response()->json([
        'status' => true,
        'message' => 'Data ditemukan',
        'data' => $data
      ], 200);
    } else {
      return response()->json([
        'status' => false,
        'message' => 'Data tidak ditemukan'
      ]);
    }
  }

  public function categoryTStore(Request $request)
  {
    $dataCategoryT = new M_Ticket_Category;

    $rules = [
      'name' => 'required',
      'kode' => 'required'
    ];
    $validator = Validator::make($request->all(), $rules);
    if ($validator->fails()) {
      return response()->json([
        'status' => false,
        'message' => 'Gagal memasukkan data',
        'data' => $validator->errors()
      ]);
    }

    $dataCategoryT->name = $request->name;
    $dataCategoryT->kode = $request->kode;

    $post = $dataCategoryT->save();

    return response()->json([
      'status' => true,
      'message' => 'Sukses memasukkan data'
    ]);
  }

  public function categoryTUpdate(Request $request, string $id)
  {
    $dataCategoryT = M_Ticket_Category::find($id);
    if (empty($dataCategoryT)) {
      return response()->json([
        'status' => false,
        'message' => 'Data tidak ditemukan'
      ], 404);
    }

    $nullableAttributes = ['name', 'kode'];
    foreach ($nullableAttributes as $attribute) {
      $rules[$attribute] = 'nullable';
    }

    $validator = Validator::make($request->all(), $rules);
    if ($validator->fails()) {
      return response()->json([
        'status' => false,
        'message' => 'Gagal melakukan update data',
        'data' => $validator->errors()
      ]);
    }

    foreach ($nullableAttributes as $attribute) {
      if ($request->has($attribute)) {
        $dataCategoryT->$attribute = $request->$attribute;
      }
    }

    $post = $dataCategoryT->save();

    return response()->json([
      'status' => true,
      'message' => 'Sukses melakukan update data'
    ]);
  }

  public function categoryTDestroy(string $id)
  {
    $dataCategoryT = M_Ticket_Category::find($id);
    if (empty($dataCategoryT)) {
      return response()->json([
        'status' => false,
        'message' => 'Data tidak ditemukan'
      ], 404);
    }

    $post = $dataCategoryT->delete();

    return response()->json([
      'status' => true,
      'message' => 'Sukses melakukan delete data'
    ]);
  }

  // COLOR
  public function colors()
  {
    $data = M_Color::orderBy('id', 'asc')->get();
    return response()->json([
      'status' => true,
      'message' => 'Data ditemukan',
      'data' => $data
    ], 200);
  }

  public function color(string $id)
  {
    $data = M_Color::find($id);
    if ($data) {
      return response()->json([
        'status' => true,
        'message' => 'Data ditemukan',
        'data' => $data
      ], 200);
    } else {
      return response()->json([
        'status' => false,
        'message' => 'Data tidak ditemukan'
      ]);
    }
  }

  public function colorStore(Request $request)
  {
    $dataColor = new M_Color;

    $rules = [
      'hexcode' => 'required'
    ];
    $validator = Validator::make($request->all(), $rules);
    if ($validator->fails()) {
      return response()->json([
        'status' => false,
        'message' => 'Gagal memasukkan data',
        'data' => $validator->errors()
      ]);
    }

    $dataColor->hexcode = $request->hexcode;

    $post = $dataColor->save();

    return response()->json([
      'status' => true,
      'message' => 'Sukses memasukkan data'
    ]);
  }

  public function colorUpdate(Request $request, string $id)
  {
    $dataColor = M_Color::find($id);
    if (empty($dataColor)) {
      return response()->json([
        'status' => false,
        'message' => 'Data tidak ditemukan'
      ], 404);
    }

    $nullableAttributes = ['hexcode'];
    foreach ($nullableAttributes as $attribute) {
      $rules[$attribute] = 'nullable';
    }

    $validator = Validator::make($request->all(), $rules);
    if ($validator->fails()) {
      return response()->json([
        'status' => false,
        'message' => 'Gagal melakukan update data',
        'data' => $validator->errors()
      ]);
    }

    foreach ($nullableAttributes as $attribute) {
      if ($request->has($attribute)) {
        $dataColor->$attribute = $request->$attribute;
      }
    }

    $post = $dataColor->save();

    return response()->json([
      'status' => true,
      'message' => 'Sukses melakukan update data'
    ]);
  }

  public function colorDestroy(string $id)
  {
    $dataColor = M_Color::find($id);
    if (empty($dataColor)) {
      return response()->json([
        'status' => false,
        'message' => 'Data tidak ditemukan'
      ], 404);
    }

    $post = $dataColor->delete();

    return response()->json([
      'status' => true,
      'message' => 'Sukses melakukan delete data'
    ]);
  }

  // CONFIG
  public function configs()
  {
    $data = M_Config::with(['Color', 'Status'])->get();
    $transformedData = $data->map(function ($item) {
      $color1Data = null;
      if ($item->color) {
        $color1Data = [
          'id' => $item->color->id,
          'hexcode' => $item->color->hexcode,
        ];
      }

      $color2Data = null;
      if ($item->color) {
        $color2Data = [
          'id' => $item->color->id,
          'hexcode' => $item->color->hexcode,
        ];
      }

      $color3Data = null;
      if ($item->color) {
        $color3Data = [
          'id' => $item->color->id,
          'hexcode' => $item->color->hexcode,
        ];
      }

      $statusData = null;
      if ($item->status) {
        $statusData = [
          'id' => $item->status->id,
          'status' => $item->status->status,
        ];
      }
      return [
        'id' => $item->id,
        'logo1' => $item->logo1,
        'logo2' => $item->logo2,
        'loading' => $item->loading,
        'nama_instansi' => $item->nama_instansi,
        'running_text' => $item->running_text,
        'status' => $statusData,
        'color1' => $color1Data,
        'color2' => $color2Data,
        'color3' => $color3Data,
        'created_at' => Carbon::parse($item->created_at)->toDateTimeString(),
        'updated_at' => Carbon::parse($item->updated_at)->toDateTimeString(),
      ];
    });
    return response()->json([
      'status' => true,
      'message' => 'Data ditemukan',
      'data' => $transformedData,
    ], 200);
  }

  public function configUpdate(Request $request, string $id)
  {
    $dataConfig = M_Config::find($id);
    if (empty($dataConfig)) {
      return response()->json([
        'status' => false,
        'message' => 'Data tidak ditemukan'
      ], 404);
    }

    $rules = [
      'nama_instansi' => 'required'
    ];

    $nullableAttributes = ['logo1', 'logo2', 'loading', 'status', 'color1_id', 'color2_id', 'color3_id'];
    foreach ($nullableAttributes as $attribute) {
      if ($request->has($attribute)) {
        $rules[$attribute] = 'nullable';
      }
    }

    $validator = Validator::make($request->all(), $rules);
    if ($validator->fails()) {
      return response()->json([
        'status' => false,
        'message' => 'Gagal melakukan update data',
        'data' => $validator->errors()
      ]);
    }

    $dataConfig->nama_instansi = $request->nama_instansi;
    $dataConfig->running_text = $request->running_text;

    foreach ($nullableAttributes as $attribute) {
      if ($request->has($attribute)) {
        $dataConfig->$attribute = $request->$attribute;
      }
    }

    $post = $dataConfig->save();

    return response()->json([
      'status' => true,
      'message' => 'Sukses melakukan update data'
    ]);
  }

  // GROUP
  public function groups()
  {
    $data = M_Group::orderBy('id', 'asc')->get();
    return response()->json([
      'status' => true,
      'message' => 'Data ditemukan',
      'data' => $data
    ], 200);
  }

  public function group(string $id)
  {
    $data = M_Group::find($id);
    if ($data) {
      return response()->json([
        'status' => true,
        'message' => 'Data ditemukan',
        'data' => $data
      ], 200);
    } else {
      return response()->json([
        'status' => false,
        'message' => 'Data tidak ditemukan'
      ]);
    }
  }

  public function groupStore(Request $request)
  {
    $dataGroup = new M_Group;

    $rules = [
      'name' => 'required'
    ];
    $validator = Validator::make($request->all(), $rules);
    if ($validator->fails()) {
      return response()->json([
        'status' => false,
        'message' => 'Gagal memasukkan data',
        'data' => $validator->errors()
      ]);
    }

    $dataGroup->name = $request->name;

    $post = $dataGroup->save();

    return response()->json([
      'status' => true,
      'message' => 'Sukses memasukkan data'
    ]);
  }

  public function groupUpdate(Request $request, string $id)
  {
    $dataGroup = M_Group::find($id);
    if (empty($dataGroup)) {
      return response()->json([
        'status' => false,
        'message' => 'Data tidak ditemukan'
      ], 404);
    }

    $nullableAttributes = ['name'];
    foreach ($nullableAttributes as $attribute) {
      $rules[$attribute] = 'nullable';
    }

    $validator = Validator::make($request->all(), $rules);
    if ($validator->fails()) {
      return response()->json([
        'status' => false,
        'message' => 'Gagal melakukan update data',
        'data' => $validator->errors()
      ]);
    }

    foreach ($nullableAttributes as $attribute) {
      if ($request->has($attribute)) {
        $dataGroup->$attribute = $request->$attribute;
      }
    }

    $post = $dataGroup->save();

    return response()->json([
      'status' => true,
      'message' => 'Sukses melakukan update data'
    ]);
  }

  public function groupDestroy(string $id)
  {
    $dataGroup = M_Group::find($id);
    if (empty($dataGroup)) {
      return response()->json([
        'status' => false,
        'message' => 'Data tidak ditemukan'
      ], 404);
    }

    $post = $dataGroup->delete();

    return response()->json([
      'status' => true,
      'message' => 'Sukses melakukan delete data'
    ]);
  }

  // LOKET
  public function lokets()
  {
    $data = M_Counter::with(['Color', 'Group', 'Status'])->orderBy('id', 'asc')->get();
    $transformedData = $data->map(function ($item) {
      $groupData = null;
      if ($item->group) {
        $groupData = [
          'id' => $item->group->id,
          'name' => $item->group->name,
        ];
      }

      $statusData = null;
      if ($item->status) {
        $statusData = [
          'id' => $item->status->id,
          'status' => $item->status->status,
        ];
      }

      $colorData = null;
      if ($item->color) {
        $colorData = [
          'id' => $item->color->id,
          'hexcode' => $item->color->hexcode,
        ];
      }

      return [
        'id' => $item->id,
        'name' => $item->name,
        'group' => $groupData,
        'status' => $statusData,
        'color' => $colorData,
        'created_at' => Carbon::parse($item->created_at)->toDateTimeString(),
        'updated_at' => Carbon::parse($item->updated_at)->toDateTimeString(),
      ];
    });
    return response()->json([
      'status' => true,
      'message' => 'Data ditemukan',
      'data' => $transformedData,
    ], 200);
  }

  public function loket(string $id)
  {
    $data = M_Counter::with(['Color', 'Group', 'Status'])->find($id);

    if ($data) {
      $groupData = null;
      if ($data->group) {
        $groupData = [
          'id' => $data->group->id,
          'name' => $data->group->name,
        ];
      }

      $statusData = null;
      if ($data->status) {
        $statusData = [
          'id' => $data->status->id,
          'status' => $data->status->status,
        ];
      }

      $colorData = null;
      if ($data->color) {
        $colorData = [
          'id' => $data->color->id,
          'hexcode' => $data->color->hexcode,
        ];
      }

      return response()->json([
        'status' => true,
        'message' => 'Data ditemukan',
        'data' => [
          'id' => $data->id,
          'name' => $data->name,
          'group' => $groupData,
          'status' => $statusData,
          'color' => $colorData,
          'created_at' => Carbon::parse($data->created_at)->toDateTimeString(),
          'updated_at' => Carbon::parse($data->updated_at)->toDateTimeString(),
        ]
      ], 200);
    } else {
      return response()->json([
        'status' => false,
        'message' => 'Data tidak ditemukan'
      ]);
    }
  }

  public function loketStore(Request $request)
  {
    $rules = [
      'name' => 'required',
      'hexcode' => 'required'
    ];

    $nullableAttributes = ['status', 'group_id'];
    foreach ($nullableAttributes as $attribute) {
      if ($request->has($attribute)) {
        $rules[$attribute] = 'nullable';
      }
    }

    $validator = Validator::make($request->all(), $rules);
    if ($validator->fails()) {
      return response()->json([
        'status' => false,
        'message' => 'Gagal memasukkan data',
        'data' => $validator->errors()
      ]);
    }

    $color = M_Color::where('hexcode', $request->hexcode)->first();

    if (!$color) {
      $color = M_Color::create([
        'hexcode' => $request->hexcode
      ]);
    }

    $dataLoket = new M_Counter;
    $dataLoket->name = $request->name;
    $dataLoket->kode = $request->kode;
    $dataLoket->color_id = $color->id;
    $dataLoket->status = 2;

    foreach ($nullableAttributes as $attribute) {
      if ($request->has($attribute)) {
        $dataLoket->$attribute = $request->$attribute;
      }
    }

    $dataLoket->save();

    return response()->json([
      'status' => true,
      'message' => 'Sukses memasukkan data'
    ]);
  }

  public function loketUpdate(Request $request, string $id)
  {
    $dataLoket = M_Counter::find($id);
    if (empty($dataLoket)) {
      return response()->json([
        'status' => false,
        'message' => 'Data tidak ditemukan'
      ], 404);
    }

    $rules = [
      'name' => 'nullable',
      'group_id' => 'nullable',
      'status' => 'nullable',
      'hexcode' => 'nullable'
    ];

    $validator = Validator::make($request->all(), $rules);
    if ($validator->fails()) {
      return response()->json([
        'status' => false,
        'message' => 'Gagal melakukan update data',
        'data' => $validator->errors()
      ]);
    }

    if ($request->has('hexcode')) {
      $color = M_Color::where('hexcode', $request->hexcode)->first();
      if (!$color) {
        $color = M_Color::create([
          'hexcode' => $request->hexcode
        ]);
      }
      $dataLoket->color_id = $color->id;
    }

    $dataLoket->fill($request->only(['name', 'group_id', 'status']));
    $dataLoket->save();

    return response()->json([
      'status' => true,
      'message' => 'Sukses melakukan update data'
    ]);
  }

  public function loketDestroy(string $id)
  {
    $dataLoket = M_Counter::find($id);
    if (empty($dataLoket)) {
      return response()->json([
        'status' => false,
        'message' => 'Data tidak ditemukan'
      ], 404);
    }

    $post = $dataLoket->delete();

    return response()->json([
      'status' => true,
      'message' => 'Sukses melakukan delete data'
    ]);
  }

  // PENGUNJUNG
  public function pengunjungs()
  {
    $data = M_Customer::orderBy('id', 'asc')->get();
    return response()->json([
      'status' => true,
      'message' => 'Data ditemukan',
      'data' => $data
    ], 200);
  }

  public function pengunjung(string $id)
  {
    $data = M_Customer::find($id);
    if ($data) {
      return response()->json([
        'status' => true,
        'message' => 'Data ditemukan',
        'data' => $data
      ], 200);
    } else {
      return response()->json([
        'status' => false,
        'message' => 'Data tidak ditemukan'
      ]);
    }
  }

  public function pengunjungRegis(string $id)
  {
    $data = M_Customer::where('no_induk', '=', $id)->first();
    if ($data) {
      return response()->json([
        'status' => true,
        'message' => 'Data ditemukan',
        'data' => $data
      ], 200);
    } else {
      return response()->json([
        'status' => false,
        'message' => 'Data tidak ditemukan'
      ]);
    }
  }

  public function pengunjungStore(Request $request)
  {
    $rules = [
      'name' => 'required',
      'email' => 'required|email',
      'no_telp' => 'required',
      'alamat' => 'required',
      'tempat_lahir' => 'required',
      'tanggal_lahir' => 'required|date'
    ];

    $nullableAttributes = ['no_induk'];
    foreach ($nullableAttributes as $attribute) {
      if ($request->has($attribute)) {
        $rules[$attribute] = 'nullable';
      }
    }

    $validator = Validator::make($request->all(), $rules);
    if ($validator->fails()) {
      return response()->json([
        'status' => false,
        'message' => 'Gagal memasukkan data',
        'data' => $validator->errors()
      ]);
    }

    $dataPengunjung = new M_Customer;
    $dataPengunjung->name = $request->name;
    $dataPengunjung->email = $request->email;
    $dataPengunjung->no_telp = $request->no_telp;
    $dataPengunjung->alamat = $request->alamat;
    $dataPengunjung->tempat_lahir = $request->tempat_lahir;
    $dataPengunjung->tanggal_lahir = $request->tanggal_lahir;

    foreach ($nullableAttributes as $attribute) {
      if ($request->has($attribute)) {
        $dataPengunjung->$attribute = $request->$attribute;
      }
    }

    $post = $dataPengunjung->save();

    return response()->json([
      'status' => true,
      'message' => 'Sukses memasukkan data'
    ]);
  }

  public function pengunjungUpdate(Request $request, string $id)
  {
    $dataPengunjung = M_Customer::find($id);
    if (empty($dataPengunjung)) {
      return response()->json([
        'status' => false,
        'message' => 'Data tidak ditemukan'
      ], 404);
    }

    $nullableAttributes = ['no_induk', 'name', 'email', 'no_telp', 'alamat', 'tempat_lahir', 'tanggal_lahir'];
    foreach ($nullableAttributes as $attribute) {
      $rules[$attribute] = 'nullable';
    }

    $validator = Validator::make($request->all(), $rules);
    if ($validator->fails()) {
      return response()->json([
        'status' => false,
        'message' => 'Gagal melakukan update data',
        'data' => $validator->errors()
      ]);
    }

    foreach ($nullableAttributes as $attribute) {
      if ($request->has($attribute)) {
        $dataPengunjung->$attribute = $request->$attribute;
      }
    }

    $post = $dataPengunjung->save();

    return response()->json([
      'status' => true,
      'message' => 'Sukses melakukan update data'
    ]);
  }

  public function pengunjungDestroy(string $id)
  {
    $dataPengunjung = M_Customer::find($id);
    if (empty($dataPengunjung)) {
      return response()->json([
        'status' => false,
        'message' => 'Data tidak ditemukan'
      ], 404);
    }

    $post = $dataPengunjung->delete();

    return response()->json([
      'status' => true,
      'message' => 'Sukses melakukan delete data'
    ]);
  }

  // ROLES
  public function roles()
  {
    $data = M_Role::orderBy('id', 'asc')->get();
    return response()->json([
      'status' => true,
      'message' => 'Data ditemukan',
      'data' => $data
    ], 200);
  }

  public function role(string $id)
  {
    $data = M_Role::find($id);
    if ($data) {
      return response()->json([
        'status' => true,
        'message' => 'Data ditemukan',
        'data' => $data
      ], 200);
    } else {
      return response()->json([
        'status' => false,
        'message' => 'Data tidak ditemukan'
      ]);
    }
  }

  public function roleStore(Request $request)
  {
    $rules = [
      'name' => 'required'
    ];

    $nullableAttributes = ['unique_code'];
    foreach ($nullableAttributes as $attribute) {
      if ($request->has($attribute)) {
        $rules[$attribute] = 'nullable';
      }
    }

    $validator = Validator::make($request->all(), $rules);
    if ($validator->fails()) {
      return response()->json([
        'status' => false,
        'message' => 'Gagal memasukkan data',
        'data' => $validator->errors()
      ]);
    }

    $dataRole = new M_Role;
    $dataRole->name = $request->name;

    foreach ($nullableAttributes as $attribute) {
      if ($request->has($attribute)) {
        $dataRole->$attribute = $request->$attribute;
      }
    }

    $post = $dataRole->save();

    return response()->json([
      'status' => true,
      'message' => 'Sukses memasukkan data'
    ]);
  }

  public function roleUpdate(Request $request, string $id)
  {
    $dataRole = M_Role::find($id);
    if (empty($dataRole)) {
      return response()->json([
        'status' => false,
        'message' => 'Data tidak ditemukan'
      ], 404);
    }

    $nullableAttributes = ['name', 'unique_code'];
    foreach ($nullableAttributes as $attribute) {
      $rules[$attribute] = 'nullable';
    }

    $validator = Validator::make($request->all(), $rules);
    if ($validator->fails()) {
      return response()->json([
        'status' => false,
        'message' => 'Gagal melakukan update data',
        'data' => $validator->errors()
      ]);
    }

    foreach ($nullableAttributes as $attribute) {
      if ($request->has($attribute)) {
        $dataRole->$attribute = $request->$attribute;
      }
    }

    $post = $dataRole->save();

    return response()->json([
      'status' => true,
      'message' => 'Sukses melakukan update data'
    ]);
  }


  public function roleDestroy(string $id)
  {
    $dataRole = M_Role::find($id);
    if (empty($dataRole)) {
      return response()->json([
        'status' => false,
        'message' => 'Data tidak ditemukan'
      ], 404);
    }

    $post = $dataRole->delete();

    return response()->json([
      'status' => true,
      'message' => 'Sukses melakukan delete data'
    ]);
  }

  // TIKET
  public function tikets()
  {
    $data = M_Ticket::with(['Pengunjung', 'CategoryL.Color', 'CategoryT', 'Loket', 'Status'])->orderBy('id', 'asc')->get();
    $transformedData = $data->map(function ($item) {
      $pengunjungData = null;
      if ($item->pengunjung) {
        $pengunjungData = [
          'id' => $item->pengunjung->id,
          'no_induk' => $item->pengunjung->no_induk,
          'nama' => $item->pengunjung->name,
          'email' => $item->pengunjung->email,
          'no_telp' => $item->pengunjung->no_telp,
          'alamat' => $item->pengunjung->alamat,
          'tempat_lahir' => $item->pengunjung->tempat_lahir,
          'tanggal_lahir' => $item->pengunjung->tanggal_lahir
        ];
      }

      $categoryLData = null;
      if ($item->categoryL) {
        $colorData = null;
        if ($item->categoryL->color) {
          $colorData = [
            'id' => $item->categoryL->color->id,
            'hexcode' => $item->categoryL->color->hexcode
          ];
        }

        $categoryLData = [
          'id' => $item->categoryL->id,
          'nama' => $item->categoryL->name,
          'kode' => $item->categoryL->kode,
          'color' => $colorData
        ];
      }

      $categoryTData = null;
      if ($item->categoryT) {
        $categoryTData = [
          'id' => $item->categoryT->id,
          'nama' => $item->categoryT->name,
          'kode' => $item->categoryT->kode
        ];
      }

      $loketData = null;
      if ($item->loket) {
        $loketData = [
          'id' => $item->loket->id,
          'nama' => $item->loket->name,
          'kode' => $item->loket->kode
        ];
      }

      $statusData = null;
      if ($item->status) {
        $statusData = [
          'id' => $item->status->id,
          'status' => $item->status->status
        ];
      }

      return [
        'id' => $item->id,
        'no_booking' => $item->no_booking,
        'no_antrian' => $item->no_antrian,
        'pengunjung' => $pengunjungData,
        'categoryL' => $categoryLData,
        'categoryT' => $categoryTData,
        'position' => $item->position,
        'loket' => $loketData,
        'status' => $statusData,
        'durasi' => $item->durasi,
        'note' => $item->note,
        'created_at' => Carbon::parse($item->created_at)->toDateTimeString(),
        'updated_at' => Carbon::parse($item->updated_at)->toDateTimeString(),
      ];
    });
    return response()->json([
      'status' => true,
      'message' => 'Data ditemukan',
      'data' => $transformedData,
    ], 200);
  }

  public function tiket(string $id)
  {
    $data = M_Ticket::with(['Pengunjung', 'CategoryL.Color', 'CategoryT', 'Loket', 'Status'])->find($id);

    if ($data) {
      $pengunjungData = null;
      if ($data->pengunjung) {
        $pengunjungData = [
          'id' => $data->pengunjung->id,
          'no_induk' => $data->pengunjung->no_induk,
          'name' => $data->pengunjung->name,
          'email' => $data->pengunjung->email,
          'no_telp' => $data->pengunjung->no_telp,
          'alamat' => $data->pengunjung->alamat,
          'tempat_lahir' => $data->pengunjung->tempat_lahir,
          'tanggal_lahir' => $data->pengunjung->tanggal_lahir,
        ];
      }

      $categoryLData = null;
      if ($data->categoryL) {
        $colorData = null;
        if ($data->categoryL->color) {
          $colorData = [
            'id' => $data->categoryL->color->id,
            'hexcode' => $data->categoryL->color->hexcode,
          ];
        }

        $categoryLData = [
          'id' => $data->categoryL->id,
          'name' => $data->categoryL->name,
          'kode' => $data->categoryL->kode,
          'color' => $colorData,
        ];
      }

      $categoryTData = null;
      if ($data->categoryT) {
        $categoryTData = [
          'id' => $data->categoryT->id,
          'name' => $data->categoryT->name,
          'kode' => $data->categoryT->kode,
        ];
      }

      $loketData = null;
      if ($data->loket) {
        $loketData = [
          'id' => $data->loket->id,
          'name' => $data->loket->name,
          'kode' => $data->loket->kode,
        ];
      }

      $statusData = null;
      if ($data->status) {
        $statusData = [
          'id' => $data->status->id,
          'status' => $data->status->status,
        ];
      }

      return response()->json([
        'status' => true,
        'message' => 'Data ditemukan',
        'data' => [
          'id' => $data->id,
          'no_booking' => $data->no_booking,
          'no_antrian' => $data->no_antrian,
          'pengunjung' => $pengunjungData,
          'categoryL' => $categoryLData,
          'categoryT' => $categoryTData,
          'position' => $data->position,
          'loket' => $loketData,
          'status' => $statusData,
          'durasi' => $data->durasi,
          'note' => $data->note,
          'created_at' => Carbon::parse($data->created_at)->toDateTimeString(),
          'updated_at' => Carbon::parse($data->updated_at)->toDateTimeString(),
        ]
      ], 200);
    } else {
      return response()->json([
        'status' => false,
        'message' => 'Data tidak ditemukan'
      ]);
    }
  }

  public function tiketStore(Request $request)
  {
    $rules = [
      'no_antrian' => 'required',
      'loket_id' => 'required',
      'status' => 'required'
    ];

    $nullableAttributes = ['no_booking', 'categoryL_id', 'categoryT_id', 'position', 'pengunjung_id', 'durasi', 'note'];
    foreach ($nullableAttributes as $attribute) {
      if ($request->has($attribute)) {
        $rules[$attribute] = 'nullable';
      }
    }

    $validator = Validator::make($request->all(), $rules);
    if ($validator->fails()) {
      return response()->json([
        'status' => false,
        'message' => 'Gagal memasukkan data',
        'data' => $validator->errors()
      ]);
    }

    $dataTiket = new M_Ticket;
    $dataTiket->no_antrian = $request->no_antrian;
    $dataTiket->loket_id = $request->loket_id;
    $dataTiket->status = $request->status;

    foreach ($nullableAttributes as $attribute) {
      if ($request->has($attribute)) {
        $dataTiket->$attribute = $request->$attribute;
      }
    }

    $post = $dataTiket->save();

    return response()->json([
      'status' => true,
      'message' => 'Sukses memasukkan data'
    ]);
  }

  public function tiketUpdate(Request $request, string $id)
  {
    $dataTiket = M_Ticket::find($id);
    if (empty($dataTiket)) {
      return response()->json([
        'status' => false,
        'message' => 'Data tidak ditemukan'
      ], 404);
    }

    $nullableAttributes = ['no_booking', 'no_antrian', 'pengunjung_id', 'categoryL_id', 'categoryT_id', 'position', 'loket_id', 'status', 'durasi', 'note'];
    foreach ($nullableAttributes as $attribute) {
      $rules[$attribute] = 'nullable';
    }

    $validator = Validator::make($request->all(), $rules);
    if ($validator->fails()) {
      return response()->json([
        'status' => false,
        'message' => 'Gagal melakukan update data',
        'data' => $validator->errors()
      ]);
    }

    foreach ($nullableAttributes as $attribute) {
      if ($request->has($attribute)) {
        $dataTiket->$attribute = $request->$attribute;
      }
    }

    $post = $dataTiket->save();

    return response()->json([
      'status' => true,
      'message' => 'Sukses melakukan update data'
    ]);
  }


  public function tiketDestroy(string $id)
  {
    $dataTiket = M_Ticket::find($id);
    if (empty($dataTiket)) {
      return response()->json([
        'status' => false,
        'message' => 'Data tidak ditemukan'
      ], 404);
    }

    $post = $dataTiket->delete();

    return response()->json([
      'status' => true,
      'message' => 'Sukses melakukan delete data'
    ]);
  }

  // USER
  public function users()
  {
    $data = M_User::with(['Role', 'Loket'])->orderBy('id', 'asc')->get();
    $transformedData = $data->map(function ($item) {
      $roleData = null;
      if ($item->role) {
        $roleData = [
          'id' => $item->role->id,
          'nama' => $item->role->name,
        ];
      }
      $loketData = null;
      if ($item->loket) {
        $loketData = [
          'id' => $item->loket->id,
          'nama' => $item->loket->name,
          'kode' => $item->loket->kode,
        ];
      }
      return [
        'id' => $item->id,
        'name' => $item->name,
        'email' => $item->email,
        'password' => $item->password,
        'role' => $roleData,
        'loket' => $loketData,
        'created_at' => Carbon::parse($item->created_at)->toDateTimeString(),
        'updated_at' => Carbon::parse($item->updated_at)->toDateTimeString(),
      ];
    });
    return response()->json([
      'status' => true,
      'message' => 'Data ditemukan',
      'data' => $transformedData,
    ], 200);
  }

  public function user(string $id)
  {
    $data = M_User::with(['Role', 'Loket'])->find($id);

    if ($data) {
      $userData = [
        'id' => $data->id,
        'name' => $data->name,
        'email' => $data->email,
        'password' => $data->password,
        'role' => [
          'id' => $data->role ? $data->role->id : null,
          'nama' => $data->role ? $data->role->name : null,
        ],
        'loket' => [
          'id' => $data->loket ? $data->loket->id : null,
          'nama' => $data->loket ? $data->loket->name : null,
          'kode' => $data->loket ? $data->loket->kode : null,
        ],
        'created_at' => Carbon::parse($data->created_at)->toDateTimeString(),
        'updated_at' => Carbon::parse($data->updated_at)->toDateTimeString(),
      ];

      return response()->json([
        'status' => true,
        'message' => 'Data ditemukan',
        'data' => $userData,
      ], 200);
    } else {
      return response()->json([
        'status' => false,
        'message' => 'Data tidak ditemukan',
      ]);
    }
  }

  public function userStore(Request $request)
  {
    $rules = [
      'name' => 'required',
      'email' => 'required|email',
      'password' => 'required'
    ];

    $nullableAttributes = ['role_id', 'loket_id'];
    foreach ($nullableAttributes as $attribute) {
      if ($request->has($attribute)) {
        $rules[$attribute] = 'nullable';
      }
    }

    $validator = Validator::make($request->all(), $rules);
    if ($validator->fails()) {
      return response()->json([
        'status' => false,
        'message' => 'Gagal memasukkan data',
        'data' => $validator->errors()
      ]);
    }

    $dataUser = new M_User;
    $dataUser->name = $request->name;
    $dataUser->email = $request->email;
    $dataUser->password = Hash::make($request->password);

    foreach ($nullableAttributes as $attribute) {
      if ($request->has($attribute)) {
        $dataUser->$attribute = $request->$attribute;
      }
    }

    $post = $dataUser->save();

    return response()->json([
      'status' => true,
      'message' => 'Sukses memasukkan data'
    ]);
  }

  public function userUpdate(Request $request, string $id)
  {
    $dataUser = M_User::find($id);
    if (empty($dataUser)) {
      return response()->json([
        'status' => false,
        'message' => 'Data tidak ditemukan'
      ], 404);
    }

    $nullableAttributes = ['name', 'email', 'password', 'role_id', 'loket_id'];
    foreach ($nullableAttributes as $attribute) {
      $rules[$attribute] = 'nullable';
    }

    $validator = Validator::make($request->all(), $rules);
    if ($validator->fails()) {
      return response()->json([
        'status' => false,
        'message' => 'Gagal melakukan update data',
        'data' => $validator->errors()
      ]);
    }

    foreach ($nullableAttributes as $attribute) {
      if ($request->has($attribute)) {
        if ($attribute === 'password') {
          $dataUser->$attribute = Hash::make($request->$attribute);
        } else {
          $dataUser->$attribute = $request->$attribute;
        }
      }
    }

    $post = $dataUser->save();

    return response()->json([
      'status' => true,
      'message' => 'Sukses melakukan update data'
    ]);
  }


  public function userDestroy(string $id)
  {
    $dataUser = M_User::find($id);
    if (empty($dataUser)) {
      return response()->json([
        'status' => false,
        'message' => 'Data tidak ditemukan'
      ], 404);
    }

    $post = $dataUser->delete();

    return response()->json([
      'status' => true,
      'message' => 'Sukses melakukan delete data'
    ]);
  }
}
