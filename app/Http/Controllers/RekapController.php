<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Rekap;

class RekapController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
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
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $judul = preg_replace('/[\s]+[\.]?/', '%20', $id);
        $judulbersih = str_replace("%20",',',$judul);
        //$response['data'] = $judulbersih;
        $judulCheck    = DB::select('SELECT nama, judul, prodi, (MATCH(judul) AGAINST((:judul1) IN NATURAL LANGUAGE MODE)) AS score  FROM rekap WHERE MATCH(judul) AGAINST((:judul2) IN NATURAL LANGUAGE MODE) LIMIT 10 ',['judul1' => strval($judulbersih), 'judul2' => strval($judulbersih)]);
        if ($judulCheck) {
            $response['success'] = true;
            $response['message'] = "List dari Pencarian";
            $response['entri'] = $judulbersih;
            $response['data'] = $judulCheck;
        } else {
            $response['success'] = false;
            $response['message'] = "Tidak ada data";
            $response['data'] = null;
        }
        return response()->json($response, 201);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
