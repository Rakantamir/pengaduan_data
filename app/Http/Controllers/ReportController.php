<?php

namespace App\Http\Controllers;

use App\Models\Report;
use App\Models\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PDF;
use Excel;
use App\Exports\reportExport;

class ReportController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function exportPDF()
    {
        $data = Report::with('response')->get()->toArray();
        view()->share('reports',$data);
        $pdf = PDF::loadview('print', $data)->setPaper('a4', 'landscape');
        return $pdf->download('data_pengaduan_keseluruhan.pdf');
    }

    public function exportExcel()
    {
        $file_name= 'data_keseluruhan_pengaduan.xlsx';
        return Excel::download(new reportExport,$file_name);
    }

    public function index()
    {
        
        $reports = Report::orderBy('created_at', 'DESC')->simplePaginate(2); 
        return view('index', compact('reports'));

        // $reports = Report::all(); 
        // return view('index', compact('reports'));

        // ASC : ascending -> terkecil terbesar 1-100 / a-z
        // DESC : descending -> terbesar terkecil 100-1 /z-a
    }

    //Request $request ditambahkan karna pada halaman data ada fitue search nya dan akan mengambil teks yg diinput search

    public function createdPDF($id)
    {
        $data = Report::with('response')->where('id',$id)->get()->toArray();
        view()->share('reports',$data);
        $pdf = PDF::loadView('print', $data)->setPaper('a4', 'landscape');
        return $pdf->download('data_pengaduan.pdf');
    }
    public function data(Request $request)
    {
        //ambil data yang diinput ke input name nya seacrh
        $search = $request->search;


        $reports = Report::with ('response')->where('nama', 'LIKE', '%' . $search . '%')->orderBy('created_at', 'DESC')->get(); 
        return view('data', compact('reports'));
    }

    public function dataPetugas(Request $request)
    {
        $search = $request->search;
        $reports =Report::where('nama', 'LIKE', '%' . $search .'%')->orderBy('created_at', 'DESC')->get();
        return view('data_petugas', compact ('reports'));
        }

    // Request $request untuk mengambil data
    public function auth(Request $request)
    {
        //validasi
        $request->validate([
            'email' => 'required',
            'password' => 'required',
        ]);

        // ambil data dan simpan di variable
        $user = $request->only('email', 'password');

        //simpen data ke auth dengan Auth::attempt
        //cek proses penyimpanan ke auth berhasik ato tidak lewar if else
        if (Auth::attempt($user)) {
            if (Auth::user()->role == 'admin') {
                return redirect()->route ('data');
            }elseif(Auth::user()->role == 'petugas') {
                return redirect()->route('data.petugas');
            }
        }else {
            return redirect()->back()->with('gagal', 'Gagal login, coba ulang lagi !');
        }
    }

    public function logout()
    {
        Auth::logout();
        return redirect()->route('login');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'nik' => 'required',
            'nama' => 'required',
            'no_telp' => 'required|max:13',
            'pengaduan' => 'required',
            'foto' => 'required|image|mimes:jpg,jpeg,png,svg',
        ]);
        
        // pindah foto ke folder public
        $path = public_path('assets/image/');
        $image = $request->file('foto');
        $imgName = rand() . '.' .$image->extension(); // foto.jpg : 1234.jpg
        $image->move($path, $imgName);

        // tambah data ke db
        Report::create([
            'nik' => $request->nik,
            'nama' => $request->nama,
            'no_telp' => $request->no_telp,
            'pengaduan' => $request->pengaduan,
            'foto' => $imgName,
        ]);

        return redirect()->back()->with('success', 'Berhasil menambahkan data !');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Report  $report
     * @return \Illuminate\Http\Response
     */
    public function show(Report $report)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Report  $report
     * @return \Illuminate\Http\Response
     */
    public function edit(Report $report)
    {
        
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Report  $report
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Report $report)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Report  $report
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $data = Report::where('id', $id)->firstOrFail();
        //$data isinya -> nik sampe foto dr pengaduan 
        //hapus foto data dr folder public : path . nama fotonya
        //nama foto nya diambil dari $data yang diatas trs ngambil dari column 'foto'
        $image = public_path('assets/image/'.$data['foto']);
        unlink($image);

        $data->delete();
        Response::where('report_id', $id)->delete();
        return redirect()->back();
    }
}
