<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Criteria;

class CriteriaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $response['success'] = true;
        $response['message'] = "List of Criterias";
        $response['data']    = Criteria::all();
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
        $response['message'] = "Criteria dengan id";
        //$response['data']    = Criteria::where('id', $id)->get(); 
        $response['data']    = DB::select('SELECT * FROM criterias WHERE id = :id', ['id' => $id]);
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
        $validator = \Validator::make($request->all(),[
            'ketCriteria' => 'required',
            'bobot' => 'required|numeric',
        ]);
        
        if ($validator->fails()){
            $response['success'] = false;
            $response['message'] = $validator->messages();
            $response['data']    = null;
        }else {
            $criteria = Criteria::find($id);
            $criteria->update($request->all());

            $response['success'] = true;
            $response['message'] = "Criteria dengan id ".$id." updated";
            $response['data']    = $criteria;
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
        //
    }
}
