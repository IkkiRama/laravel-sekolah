<?php

namespace App\Http\Controllers;

use App\Models\Siswa;
use App\Models\User;
use App\Models\Mapel;
use Illuminate\Http\Request;
use Illuminate\Support\Str;


// excel
use App\Exports\SiswaExport;
use Maatwebsite\Excel\Facades\Excel;

class SiswaController extends Controller
{

    public function dashboard()
    {
        return view('siswa.home');
    }


    public function index(Request $request)
    {
        if ($request->has('cari')) {
            $siswa = Siswa::where('nama_depan', 'LIKE', '%'.$request->cari.'%')->get();
        }else{
            $siswa =Siswa::all();
        }
        return view('siswa.index', compact('siswa'));
    }



    public function store(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'nama_depan' => 'required | min : 5',
            'nama_belakang' => 'required',
            'email' => 'required|email|unique:users,email',
            'jenis_kelamin' => 'required',
            'agama' => 'required',
            'foto' => 'mimes:jpg,png'
        ]);


        //Up Foto
        if ($request->hasFile('foto')) {
            $request->file('foto')->move('images/', $request->file('foto')->getClientOriginalName());
        }


        // insert ke tabel user
        $user = new User;
        $user->role = 'siswa';
        $user->name = $request->nama_depan;
        $user->email = $request->email;
        $user->password = bcrypt('12345');
        $user->remember_token = Str::random(60);
        $user->save();


        // insert ke tabel siswa
        Siswa::create([
            'id_user' => $user->id,
            'nama_depan' => $request->nama_depan,
            'nama_belakang' => $request->nama_belakang,
            'jenis_kelamin' => $request->jenis_kelamin,
            'agama' => $request->agama,
            'alamat' => $request->alamat,
            'foto' => $request->file('foto')->getClientOriginalName()
        ]);



        return redirect('/siswa')->with('sukses', 'Data Berhasil Ditambahkan');
    }



    public function edit(Siswa $siswa)
    {
        return view('siswa.ubah', compact('siswa'));
    }



    public function update(Request $request, Siswa $siswa)
    {
        // Siswa::where('id', $siswa->id)
        //     ->update([
        //         'nama_depan' => $request->nama_depan,
        //         'nama_belakang' => $request->nama_belakang,
        //         'jenis_kelamin' => $request->jenis_kelamin,
        //         'agama' => $request->agama,
        //         'alamat' => $request->alamat
        //     ]);


        $id = Siswa::find($siswa->id);
        $id->update($request->all());

        if ($request->hasFile('foto')) {
            $request->file('foto')->move('images/', $request->file('foto')->getClientOriginalName());
            $siswa->foto = $request->file('foto')->getClientOriginalName();
            $siswa->save();
        }

        return redirect('/siswa')->with('sukses', 'Data Berhasil Diubah');
    }



    public function destroy(Siswa $siswa)
    {
        Siswa::destroy($siswa->id);

        return redirect('/siswa')->with('sukses', 'Data Berhasil Dihapus');
    }



    public function profil(Siswa $siswa)
    {
        $mataPelajaran = Mapel::all();

        // menyiapkan data untuk chart
        $kategori = [];
        $data = [];

        foreach ($mataPelajaran as $mp) {
            if ($siswa->mapel()->wherePivot('mapel_id', $mp->id)->first()) {

                $kategori[] = $mp->nama;
                $data[] = $siswa->mapel()->wherePivot('mapel_id', $mp->id)->first()->pivot->nilai;
            }
        }

        // dd($data);


        return view('siswa.profil', ['siswa'=> $siswa, 'mataPelajaran' => $mataPelajaran, 'kategori' => $kategori, 'data' => $data]);
    }

    public function add_nilai(Siswa $siswa, Request $request)
    {
        if ($siswa->mapel()->where('mapel_id', $request->mapel)->exists()) {

            return redirect("/siswa/profil/$siswa->id")->with('gagal', 'Data Mata Pelajaran Sudah Ada');

        }
        $siswa->mapel()->attach($request->mapel, ['nilai' => $request->nilai]);

        return redirect("/siswa/profil/$siswa->id")->with('sukses', 'Data Nilai Berhasil Ditambahkan');
    }


    public function hapus_nilai(Siswa $siswa, Mapel $mapel)
    {
        $siswa->mapel()->detach($mapel);

        return redirect()->back()->with('sukses', 'Data Nilai Berhasil Dihapus');

    }



    // Export Ke Excel
    public function export()
    {
        return Excel::download(new SiswaExport, 'Siswa.xlsx');
    }
}
