<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Scoring;

class ScoringController extends Controller
{
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $op = ', ';
        $response['success'] = true;
        $response['message'] = "List of Scoring";
        $response['data'] = DB::select('SELECT DISTINCT nip, (SELECT nama FROM users WHERE id = nip) AS nama, idCriteria, (SELECT ketCriteria FROM criterias WHERE id = idCriteria) AS kategori, GROUP_CONCAT(keterangan SEPARATOR \', \') AS detail FROM scorings LEFT JOIN variabels ON scorings.idVariabel = variabels.kode GROUP BY nip, nama, idCriteria, kategori');
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
        $validator = \Validator::make($request->all(),[
            'nip' => 'required|numeric',
            'idCriteria' => 'required|numeric',
            'idVariabel' => 'required|numeric',
            'score' => 'required|numeric'
        ]);

        if ($validator->fails()) {
            $response['success'] = false;
            $response['message'] = $validator->messages();
            $response['data']    = null;
        }else {
            // $scoring = Scoring::create($request->all());            
            $scoring = new Scoring();
            $scoring->nip = $request->nip;
            $scoring->idCriteria = $request->idCriteria;
            $scoring->idVariabel = $request->idVariabel;
            $scoreFinal = ($request->idCriteria == 3) ? $request->score : 100;
            $scoring->score = $scoreFinal;
            $scoring->save();

            $response['success'] = true;
            $response['message'] = "Scoring is successfully created";
            $response['data']    = $scoring;
            
        }
        return response()->json($response, 200);
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
        $response['message'] = "Scoring dengan nip";
        //$response['data']    = Scoring::where('nip', $id)->get();
        $response['data'] = DB::select('SELECT id, idCriteria, keterangan, score FROM scorings LEFT JOIN variabels ON scorings.idVariabel = variabels.kode WHERE nip = :nip', ['nip' => $id]);
        return response()->json($response, 200);
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
        $validator = \Validator::make($request->all(),[
            'nip' => 'required|numeric',
            'idCriteria' => 'required|numeric',
            'idVariabel' => 'required|numeric',
            'score' => 'required|numeric',
        ]);

        if ($validator->fails()){
            $response['success'] = false;
            $response['message'] = $validator->messages();
            $response['data'] = null;
        }else{
            $scoring = Scoring::find($id);
            $scoring->update($request->all());

            $response['success'] = true;
            $response['message'] = "Scoring with id ".$id." updated";
            $response['data'] = $scoring;
        }
        return response()->json($response, 201);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $scoring = Scoring::find($id);
        if ($scoring){
            $response['success'] = true;
            $response['message'] = "Scoring with id ".$id." deleted";
            $responseCode = 200;
            $scoring->delete();
        }else {
            $response['success'] = false;
            $response['message'] = "Undefined Scoring with id ".$id;
            $responseCode = 404;
        }
        return response()->json($response,  $responseCode);
    }
}
