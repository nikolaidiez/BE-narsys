<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Variabel;

class VariabelController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $response['success'] = true;
        $response['message'] = "List of variabels";
        $response['data']    = Variabel::orderby('kode_induk','asc')->get();
        return response()->json($response,200);
    }

    public function kuota($id)
    {
        $response['success'] = true;
        $response['message'] = "List of kuota variabel";
        $response['data']    = DB::select('SELECT khusus.kode, CONCAT(umum.keterangan,\':\',khusus.keterangan,\'-kuota : \',(khusus.batas - (SELECT COUNT(nim) FROM usulans WHERE SUBSTRING(nim,6,2) = :id AND khusus.kode = bidangIlmu))) AS ket FROM variabels umum, variabels khusus  WHERE umum.kode = khusus.kode_induk ORDER BY umum.keterangan ASC',['id' => $id]);
        return response()->json($response,200);
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
        $response['success'] = true;
        $response['message'] = "List of Spesific Variabels";
        $response['data']    = Variabel::where('kode_induk',$id)->get();
        return response()->json($response,200);
    }

    /**
     * Display the unselected resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function showUnselected($nip,$idCriteria,$kodeParent)
    {
        $response['success'] = true;
        $response['message'] = "List of Unselected Variabels";
        $response['data']    = DB::select('SELECT * FROM variabels WHERE kode NOT IN (SELECT idVariabel FROM scorings WHERE nip = :nip AND idCriteria = :idCriteria) AND kode_induk = :kodeInduk', ['nip' => $nip, 'idCriteria' => $idCriteria, 'kodeInduk' => $kodeParent]);
        return response()->json($response,200);
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
