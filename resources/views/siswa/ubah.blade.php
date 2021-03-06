@extends('layout.main')
@section('title', 'Ubah Siswa')
@section('content')
    <div class="panel panel-headline" style="min-height: 70vh;">
        <div class="panel-heading">
            <h2>Form Ubah Siswa</h2>
        </div>

        <div class="panel-body">
            <form action="{{url("/siswa/edit/$siswa->id")}}" method="post" class="mb-5" enctype="multipart/form-data">
                @method('put')
                @csrf
                <div class="form-group">
                    <label for="nama_depan">Nama Depan</label>
                    <input type="text" name="nama_depan" id="nama_depan" class="form-control" value="{{$siswa->nama_depan}}">
                </div>

                <div class="form-group">
                    <label for="nama_belakang">Nama Belakang</label>
                    <input type="text" name="nama_belakang" id="nama_belakang" class="form-control" value="{{$siswa->nama_belakang}}">
                </div>

                <div class="form-group">
                    <label for="jenis_kelamin">Jenis Kelamin</label>
                    <select name="jenis_kelamin" id="jenis_kelamin" class="form-control">
                        <option value="Laki-laki" @if($siswa->jenis_kelamin == 'Laki-laki') selected @endif>Laki-laki</option>
                        <option value="Perempuan" @if($siswa->jenis_kelamin == 'Perempuan') selected @endif>Perempuan</option>
                    </select>
                </div>


                <div class="form-group">
                    <label for="agama">Agama</label>
                    <input type="text" name="agama" id="agama" class="form-control" value="{{$siswa->agama}}">
                </div>

                <div class="form-group">
                    <label for="foto">Foto</label><br>
                    <img src="{{asset("/images/$siswa->foto")}}" class="img-thumbnail" width="120" height="120" style="margin:20px 0;">
                    <input type="file" name="foto" id="foto" class="form-form-control-file">
                </div>

                <div class="form-group">
                    <label for="alamat">Alamat</label>
                    <textarea name="alamat" id="alamat" cols="30" rows="10" class="form-control">{{$siswa->alamat}}</textarea>
                </div>

                <button class="btn btn-warning btn-sm">Ubah Data</button>
            </form>
        </div>
    </div>



@endsection
