<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengaduan Masyarakat</title>
    <link rel="stylesheet" href="{{asset('assets/css/style.css')}}">
</head>

<body>
    <h2 class="title-table">Laporan Keluhan</h2>
    <div style="display: flex; justify-content: center; margin-bottom: 30px">
        <a href="logout" style="text-align: center">Logout</a>
        <div style="margin: 0 10px"> | </div>
        <a href="/" style="text-align: center">Home</a>
    </div>
    <div style="display: flex; justify-content: flex-end; align-items: center;">
        <form action="" method="GET">
            @csrf
            {{--menggunakan method GET karna route unutk masuk ke halaman data ini menggunakan ::get--}}
            <input type="text" name="search" placeholder="Cari berdasarkan nama...">
            <button type="submit" class="btn-login" style="margin-top: -1px">Cari</button>
        </form>
        {{-- refresh balik lagi ke route data karna nanti pas di kluk refresh mau bersihin riwayat pencarian 
             sebelumnya dan balikin lagi nya ke halaman data lagi--}}
             <a href="{{route('export.excel')}}" style="margin-left: 10px; margin-top: 20px">cetak excel</a>
        <a href="{{route('data')}}" style="margin-left: 10px; margin-top: 20px">Refresh</a>
        <a href="{{route('export-pdf')}}" style="margin-left: 10px; margin-top: 20px">Cetak PDF</a>
    </div>
    </div>
    <div style="padding: 0 30px">
        <table>
            <thead>
                <tr>
                    <th width="5%">No</th>
                    <th>NIK</th>
                    <th>Nama</th>
                    <th>Telp</th>
                    <th>Pengaduan</th>
                    <th>Gambar</th>
                    <th>Status Respon</th>
                    <th>Pesan Respon</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $no = 1;
                @endphp
                @foreach ($reports as $report)
                <tr>
                    {{--menambahkan angka 1 dari $no di php tiap baris nya--}}
                    <td>{{$no++}}</td>
                    <td>{{$report ['nik']}}</td>
                    <td>{{$report ['nama']}}</td>
                    @php 
                       $telp = substr_replace($report->no_telp, "62", 0, 1)
                       @endphp
                       @php
                       if ($report->response) {
                       $pesanWA = 'Hallo' . $report->nama . '! pengaduan anda di 
                        ' . $report->response['status'] . '.Berikut pesan untuk
                         anda :' . $report->response['pesan'];
                       }
                     else {
                       $pesanWA = 'Belum ada data response!';
                      }
                        @endphp
                       <td> <a href="https://wa.me/{{$telp}}?text={{$pesanWA}}"
                        target="_blank">{{$telp}} </a></td>
                    <td>{{$report ['pengaduan']}}</td>
                    <td>
                        <a href="../assets/image/{{$report->foto}}" target="_blank">
                        <img src="{{asset('assets/image/'.$report->foto)}}" width="120">
                           </a>
                    </td>

                    <td>
                        @if ($report->response)
                        {{ $report->response['status'] }}
                        @else
                        -
                        @endif
                    </td>
                    <td>
                     @if ($report->response)
                     {{ $report->response['pesan']}}
                     @else
                     -
                     @endif
                     </td>


                    <td style="display: flex">
                        <form action="{{ route('delete', $report->id) }}" method="POST">
                                @csrf
                                @method('DELETE')
                            
                                <button type="submit" class="btn btn-danger" >Delete</button>
                            </form>
                            <form action="{{ route('created.pdf', $report->id) }}" method="GET" style=margin-top: 20px>

                            <button type="submit">Print</button>
                     </td>
                </tr>
                @endforeach
            </body>
        </table>
    </div>
</body>

</html>