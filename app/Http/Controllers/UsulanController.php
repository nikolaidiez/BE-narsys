<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Usulan;
use App\Models\Helpan;
use App\Models\Helpab;

class UsulanController extends Controller
{
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $response['success'] = true;
        $response['message'] = "List of Title";
        $response['data']    = DB::select('SELECT usulans.id AS usulid, nim, nama, (SELECT nama FROM users WHERE id = nipPA) AS namaDosenPA, judul, abstraksi, status, rekomendasi, judulFinal, (SELECT keterangan FROM variabels WHERE kode = bidangIlmu) AS ketBidang, (SELECT keterangan FROM variabels WHERE kode = metPen) AS metPen FROM usulans LEFT JOIN users ON usulans.nim = users.id ORDER BY nim ASC');
        return response()->json($response, 200);
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
        $validator = \Validator::make($request->all(),[
            'nim' => 'required|numeric',
            'nipPA' => 'required|numeric',
            'judul' => 'required',
            'abstraksi' => 'required',
        ]);

        if ($validator->fails()) {
            $response['success'] = false;
            $response['message'] = "Seluruh item entrian harus diisi";
            $response['data']    = null;
        }else {
            $usulan = new Usulan();
            $usulan->nim = $request->nim;
            $usulan->nipPA = $request->nipPA;
            $usulan->tahun = date("Y");
            $usulan->judul = strtoupper($request->judul);
            $usulan->abstraksi = $request->abstraksi;
            $usulan->save();

            $response['success'] = true;
            $response['message'] = "Usulan is successfully created";
            $response['data']    = $usulan;
        }
        return response()->json($response, 200);
    }

    public function showHB($id)
    {
        $response['success'] = true;
        $response['message'] = "List of Title";
        $response['data']    = DB::select('SELECT usulans.id AS usulid, nim, nama, judulFinal, pemb01, pemb02, peng01, peng02 FROM usulans LEFT JOIN users ON usulans.nim = users.id WHERE SUBSTRING(nim,6,2) = :hb',['hb' => $id]);
        return response()->json($response, 200);
    }
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function showNIP($id)
    {
        $response['success'] = true;
        $response['message'] = "Usulan dengan nipPA";
        //$response['data']    = Usulan::where('nipPA', $id)->get();
        $response['data'] = DB::select('SELECT usulans.id, nim, nama, nipPA, judul, abstraksi, status, rekomendasi, judulFinal, bidangIlmu FROM usulans LEFT JOIN users ON usulans.nim = users.id WHERE nipPA = :id', ['id' => $id]);
        return response()->json($response, 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function showNIM($id)
    {
        $usulan = Usulan::where('nim', $id)->get();
        if ($usulan->isEmpty()) {
            $response['success'] = false;
            $response['message'] = "Belum ada usulan";
            $response['data']    = null;
        } else {
            $response['success'] = true;
            $response['message'] = "Usulan dengan NIM";
            $response['data']    = $usulan;
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
        $response['message'] = "Usulan dengan id";
        $response['data']    = Usulan::where('id', $id)->get();
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
            'status' => 'required',
            'rekomendasi' => 'required',
            'judulFinal' => 'required',
            'bidangIlmu' => 'required|numeric',
        ]);

        if ($validator->fails()){
            $response['success'] = false;
            $response['message'] = $validator->messages();
            $response['data'] = null;
        }else{
            $usulan = Usulan::find($id);
            $usulan->status = $request->status;
            $usulan->rekomendasi = $request->rekomendasi;
            $usulan->judulFinal = strtoupper($request->judulFinal);
            $hb = substr($usulan->nim,5,2);
            $bidangIlmu = $request->bidangIlmu;
            // $usulan->bidangIlmu = $request->bidangIlmu;

            // $kuotaCheck = DB::select('SELECT COUNT(nim) as kuota FROM usulans WHERE SUBSTRING(nim,6,2) = :hb AND bidangIlmu = :kode',['hb' => strval($hb), 'kode' => $bidangIlmu]);

            $query = DB::select('SELECT kode, batas, (SELECT COUNT(nim) FROM usulans WHERE bidangIlmu = kode AND SUBSTRING(nim,6,2) = :hb) as kuota FROM variabels WHERE kode = :kode',['hb' => strval($hb), 'kode' => $bidangIlmu]);
            $kuotaCheck = ($query[0]->batas) - ($query[0]->kuota);

            if ($kuotaCheck <= 0) {
                if ($usulan->bidangIlmu != $bidangIlmu) {
                   $response['success'] = false;
                   $response['message'] = "Kuota Bidang Ilmu Habis";
                   $response['data'] = null;
                } else {
                    $usulan->bidangIlmu = $request->bidangIlmu;
                    $usulan->update();
                    $response['success'] = true;
                    $response['message'] = "Usulan with id ".$id." BERHASIL DIENTRI/DIUBAH";
                    $response['data'] = $usulan;
                }
            } else {
               $usulan->bidangIlmu = $request->bidangIlmu;
               $usulan->update();
               $response['success'] = true;
               $response['message'] = "Usulan with id ".$id." BERHASIL DIENTRI/DIUBAH";
               $response['data'] = $usulan;
            }
        }
        return response()->json($response, 201);
    }

    public function updateMetPen(Request $request, $id)
    {
        $validator = \Validator::make($request->all(),[
            'metPen' => 'required|numeric'
        ]);

        if ($validator->fails()){
            $response['success'] = false;
            $response['message'] = $validator->messages();
            $response['data'] = null;
        }else{
            $usulan = Usulan::find($id);
            $usulan->metPen = $request->metPen;
            $usulan->update();

            $response['success'] = true;
            $response['message'] = "Metodologi Penelitian berhasil dientri/diubah";
            $response['data'] = $usulan;
        }
        return response()->json($response, 201);
    }

    public function simulan()
    {
        $id = '13';
        
        $query01 = DB::select('SELECT id, nim, bidangIlmu, metPen FROM usulans WHERE SUBSTRING(nim,6,2) = :hb',['hb' => strval($id)]);

        // $query01 = DB::select('SELECT id, nim, bidangIlmu, metPen FROM usulans WHERE SUBSTRING(nim,6,2) = :hb ORDER BY id ASC',['hb' => strval($id)]);

        $reset = DB::statement('TRUNCATE TABLE helpans');

        $active = DB::statement("SET SQL_MODE=''");

        for ($en = 0; $en < count($query01); $en++) {
            $helpan = new Helpan();
            $helpan->id = $query01[$en]->id;
            $helpan->save();
        }

        //p1
        for ($in = 0; $in < count($query01); $in++) {
            $que_score = DB::select('SELECT filtereduser.id AS nip, nama, SUM(score*bobot) AS totScore, an_p1, an_p2, an_p3, an_p4 FROM (SELECT * FROM users WHERE role = :role) AS filtereduser LEFT JOIN jatahs ON filtereduser.id = jatahs.nip LEFT JOIN scorings ON filtereduser.id = scorings.nip LEFT JOIN criterias ON scorings.idCriteria = criterias.id AND (idVariabel = :bidangIlmu OR idVariabel = :hb OR idVariabel = :metPen) GROUP BY filtereduser.id ORDER BY totScore DESC',['role'=>'dosen', 'bidangIlmu' => $query01[$in]->bidangIlmu, 'hb'=>strval($id),'metPen'=>$query01[$in]->metPen]);

            for ($i = 0; $i < count($que_score); $i++) {
                
                $adjustMetPen = DB::select('SELECT nip FROM scorings WHERE nip = :nip AND idVariabel = :metpen',['nip'=> $que_score[$i]->nip, 'metpen' => $query01[$in]->metPen]);
                $fitMetPen = count($adjustMetPen);

                $kuota = $que_score[$i]->an_p1;
                //$cekJumlah = count_array_values($groupOfp1an,$que_score[$i]->nip);
                $cekJumlah = DB::select('SELECT COUNT(an_p1) AS anp1cek FROM helpans WHERE an_p1 = :an_p1',['an_p1' => $que_score[$i]->nip]);

                $simu_anp1 = Helpan::find($query01[$in]->id);
                $simu_anp1->an_p1 = $que_score[$i]->nip;
                $simu_anp1->sc_p1 = $que_score[$i]->totScore;
                if ($cekJumlah[0]->anp1cek < $kuota && $fitMetPen > 0) {
                    $simu_anp1->update();
                    break;
                }
            }
        }

        $cekdis = DB::select('SELECT nip, jatahs.an_p1 AS kuota, COUNT(helpans.an_p1) AS entri FROM jatahs, helpans WHERE nip = helpans.an_p1 GROUP BY nip HAVING (kuota - entri) > 0');

        //$ceknull = DB::select('SELECT id, an_p1 FROM helpans WHERE an_p1 IS NULL');
        $ceknull = Helpan::select("*")->whereNull('an_p1')->get();
            if (count($ceknull) > 0) {
                for ($f = 0; $f < count($ceknull); $f++) {
                    $ceknull[$f]->an_p1 = $cekdis[0]->nip;
                    $ceknull[$f]->sc_p1 = '0.0';
                    $ceknull[$f]->update();
                }
            }    

        //p2    
        $query02 = DB::select('SELECT id, nim, bidangIlmu, metPen FROM usulans WHERE SUBSTRING(nim,6,2) = :hb',['hb' => strval($id)]);
       
        for ($in = 0; $in < count($query02); $in++) {
            $que_score = DB::select('SELECT filtereduser.id AS nip, nama, SUM(score*bobot) AS totScore, an_p1, an_p2, an_p3, an_p4 FROM (SELECT * FROM users WHERE role = :role) AS filtereduser LEFT JOIN jatahs ON filtereduser.id = jatahs.nip LEFT JOIN scorings ON filtereduser.id = scorings.nip LEFT JOIN criterias ON scorings.idCriteria = criterias.id AND (idVariabel = :bidangIlmu OR idVariabel = :hb OR idVariabel = :metPen) GROUP BY filtereduser.id ORDER BY totScore DESC',['role'=>'dosen', 'bidangIlmu' => $query02[$in]->bidangIlmu, 'hb'=>strval($id),'metPen'=>$query02[$in]->metPen]);

                for ($i = 0; $i < count($que_score); $i++) {

                    $adjustMetPen = DB::select('SELECT nip FROM scorings WHERE nip = :nip AND idVariabel = :metpen',['nip'=> $que_score[$i]->nip, 'metpen' => $query02[$in]->metPen]);
                    $fitMetPen = count($adjustMetPen);

                    $kuota = $que_score[$i]->an_p2;
                    //$cekJumlah = count_array_values($groupOfp1an,$que_score[$i]->nip);
                    $cekJumlah = DB::select('SELECT COUNT(an_p2) AS anp2cek FROM helpans WHERE an_p2 = :an_p2',['an_p2' => $que_score[$i]->nip]);

                    $simu_anp1 = Helpan::find($query02[$in]->id);
                    
                    if ($simu_anp1->an_p1 === $que_score[$i]->nip) {
                        continue;
                    } else {
                        if ($cekJumlah[0]->anp2cek < $kuota && $fitMetPen > 0) {
                            $simu_anp1->an_p2 = $que_score[$i]->nip;
                            $simu_anp1->sc_p2 = $que_score[$i]->totScore;
                            $simu_anp1->update();
                            break;
                        }   
                    }
                }

                $cekdis = DB::select('SELECT nip, jatahs.an_p2 AS kuota, COUNT(helpans.an_p2) AS entri FROM jatahs, helpans WHERE nip = helpans.an_p2 GROUP BY nip HAVING (kuota - entri) > 0');

                //$ceknull = DB::select('SELECT id, an_p1 FROM helpans WHERE an_p1 IS NULL');
                $ceknull = Helpan::select("*")->whereNull('an_p2')->get();
                    if (count($ceknull) > 0) {
                        for ($f = 0; $f < count($ceknull); $f++) {
                            $ceknull[$f]->an_p2 = $cekdis[0]->nip;
                            $ceknull[$f]->sc_p2 = '0.0';
                            $ceknull[$f]->update();
                        }
                    } 
        }

        //p3   
        $query03 = DB::select('SELECT id, nim, bidangIlmu, metPen FROM usulans WHERE SUBSTRING(nim,6,2) = :hb',['hb' => strval($id)]);

        for ($in = 0; $in < count($query03); $in++) {
            $que_score = DB::select('SELECT filtereduser.id AS nip, nama, SUM(score*bobot) AS totScore, an_p1, an_p2, an_p3, an_p4 FROM (SELECT * FROM users WHERE role = :role) AS filtereduser LEFT JOIN jatahs ON filtereduser.id = jatahs.nip LEFT JOIN scorings ON filtereduser.id = scorings.nip LEFT JOIN criterias ON scorings.idCriteria = criterias.id AND (idVariabel = :bidangIlmu OR idVariabel = :hb OR idVariabel = :metPen) GROUP BY filtereduser.id ORDER BY totScore DESC',['role'=>'dosen', 'bidangIlmu' => $query03[$in]->bidangIlmu, 'hb'=>strval($id),'metPen'=>$query03[$in]->metPen]);

                for ($i = 0; $i < count($que_score); $i++) {

                    $adjustMetPen = DB::select('SELECT nip FROM scorings WHERE nip = :nip AND idVariabel = :metpen',['nip'=> $que_score[$i]->nip, 'metpen' => $query03[$in]->metPen]);
                    $fitMetPen = count($adjustMetPen);

                    $kuota = $que_score[$i]->an_p3;
                    //$cekJumlah = count_array_values($groupOfp1an,$que_score[$i]->nip);
                    $cekJumlah = DB::select('SELECT COUNT(an_p3) AS anp3cek FROM helpans WHERE an_p3 = :an_p3',['an_p3' => $que_score[$i]->nip]);

                    $simu_anp1 = Helpan::find($query03[$in]->id);
                    
                    if ($simu_anp1->an_p1 === $que_score[$i]->nip || $simu_anp1->an_p2 === $que_score[$i]->nip) {
                        continue;
                    } else {
                        if ($cekJumlah[0]->anp3cek < $kuota && $fitMetPen > 0) {
                            $simu_anp1->an_p3 = $que_score[$i]->nip;
                            $simu_anp1->sc_p3 = $que_score[$i]->totScore;
                            $simu_anp1->update();
                            break;
                        }   
                    }
                }

                $cekdis = DB::select('SELECT nip, jatahs.an_p3 AS kuota, COUNT(helpans.an_p3) AS entri FROM jatahs, helpans WHERE nip = helpans.an_p3 GROUP BY nip HAVING (kuota - entri) > 0');

                //$ceknull = DB::select('SELECT id, an_p1 FROM helpans WHERE an_p1 IS NULL');
                $ceknull = Helpan::select("*")->whereNull('an_p3')->get();
                    if (count($ceknull) > 0) {
                        for ($f = 0; $f < count($ceknull); $f++) {
                            $ceknull[$f]->an_p3 = $cekdis[0]->nip;
                            $ceknull[$f]->sc_p3 = '0.0';
                            $ceknull[$f]->update();
                        }
                    } 
        }
        
        //p4   
        $query04 = DB::select('SELECT id, nim, bidangIlmu, metPen FROM usulans WHERE SUBSTRING(nim,6,2) = :hb',['hb' => strval($id)]);

        for ($in = 0; $in < count($query04); $in++) {
            $que_score = DB::select('SELECT filtereduser.id AS nip, nama, SUM(score*bobot) AS totScore, an_p1, an_p2, an_p3, an_p4 FROM (SELECT * FROM users WHERE role = :role) AS filtereduser LEFT JOIN jatahs ON filtereduser.id = jatahs.nip LEFT JOIN scorings ON filtereduser.id = scorings.nip LEFT JOIN criterias ON scorings.idCriteria = criterias.id AND (idVariabel = :bidangIlmu OR idVariabel = :hb OR idVariabel = :metPen) GROUP BY filtereduser.id ORDER BY totScore DESC',['role'=>'dosen', 'bidangIlmu' => $query04[$in]->bidangIlmu, 'hb'=>strval($id),'metPen'=>$query04[$in]->metPen]);

                for ($i = 0; $i < count($que_score); $i++) {

                    $adjustMetPen = DB::select('SELECT nip FROM scorings WHERE nip = :nip AND idVariabel = :metpen',['nip'=> $que_score[$i]->nip, 'metpen' => $query04[$in]->metPen]);
                    $fitMetPen = count($adjustMetPen);

                    $kuota = $que_score[$i]->an_p4;
                    //$cekJumlah = count_array_values($groupOfp1an,$que_score[$i]->nip);
                    $cekJumlah = DB::select('SELECT COUNT(an_p4) AS anp4cek FROM helpans WHERE an_p4 = :an_p4',['an_p4' => $que_score[$i]->nip]);

                    $simu_anp1 = Helpan::find($query04[$in]->id);
                    
                    if ($simu_anp1->an_p1 === $que_score[$i]->nip || $simu_anp1->an_p2 === $que_score[$i]->nip || $simu_anp1->an_p3 === $que_score[$i]->nip) {
                        continue;
                    } else {
                        if ($cekJumlah[0]->anp4cek < $kuota && $fitMetPen > 0) {
                            $simu_anp1->an_p4 = $que_score[$i]->nip;
                            $simu_anp1->sc_p4 = $que_score[$i]->totScore;
                            $simu_anp1->update();
                            break;
                        }   
                    }
                }

                $cekdis = DB::select('SELECT nip, jatahs.an_p4 AS kuota, COUNT(helpans.an_p4) AS entri FROM jatahs, helpans WHERE nip = helpans.an_p4 GROUP BY nip HAVING (kuota - entri) > 0');

                //$ceknull = DB::select('SELECT id, an_p1 FROM helpans WHERE an_p1 IS NULL');
                $ceknull = Helpan::select("*")->whereNull('an_p4')->get();
                    if (count($ceknull) > 0) {
                        for ($f = 0; $f < count($ceknull); $f++) {
                            $ceknull[$f]->an_p4 = $cekdis[0]->nip;
                            $ceknull[$f]->sc_p4 = '0.0';
                            $ceknull[$f]->update();
                        }
                    } 
        }
       $response['success'] = true;
       $response['message'] = "Usulan dengan id";
       $response['data'] = DB::select('SELECT id, an_p1, an_p2, an_p3, an_p4, (SELECT nama FROM users WHERE id = an_p1) AS nama_p1, (SELECT nama FROM users WHERE id = an_p2) AS nama_p2, (SELECT nama FROM users WHERE id = an_p3) AS nama_p3, (SELECT nama FROM users WHERE id = an_p4) AS nama_p4, sc_p1, sc_p2, sc_p3, sc_p4 FROM helpans');
       return response()->json($response, 200);
    }

    public function simulab()
    {
        $id = '04';
        
        $query01 = DB::select('SELECT id, nim, bidangIlmu, metPen FROM usulans WHERE SUBSTRING(nim,6,2) = :hb',['hb' => strval($id)]);

        $reset = DB::statement('TRUNCATE TABLE helpabs');

        $active = DB::statement("SET SQL_MODE=''");

        for ($en = 0; $en < count($query01); $en++) {
            $helpab = new Helpab();
            $helpab->id = $query01[$en]->id;
            $helpab->save();
        }

        //p1
        for ($in = 0; $in < count($query01); $in++) {
            $que_score = DB::select('SELECT filtereduser.id AS nip, nama, SUM(score*bobot) AS totScore, ab_p1, ab_p2, ab_p3 FROM (SELECT * FROM users WHERE role = :role) AS filtereduser LEFT JOIN jatahs ON filtereduser.id = jatahs.nip LEFT JOIN scorings ON filtereduser.id = scorings.nip LEFT JOIN criterias ON scorings.idCriteria = criterias.id AND (idVariabel = :bidangIlmu OR idVariabel = :hb OR idVariabel = :metPen) GROUP BY filtereduser.id ORDER BY totScore DESC',['role'=>'dosen', 'bidangIlmu' => $query01[$in]->bidangIlmu, 'hb'=>strval($id),'metPen'=>$query01[$in]->metPen]);

            for ($i = 0; $i < count($que_score); $i++) {
                
                $adjustMetPen = DB::select('SELECT nip FROM scorings WHERE nip = :nip AND idVariabel = :metpen',['nip'=> $que_score[$i]->nip, 'metpen' => $query01[$in]->metPen]);
                $fitMetPen = count($adjustMetPen);

                $kuota = $que_score[$i]->ab_p1;
                //$cekJumlah = count_array_values($groupOfp1an,$que_score[$i]->nip);
                $cekJumlah = DB::select('SELECT COUNT(ab_p1) AS abp1cek FROM helpabs WHERE ab_p1 = :ab_p1',['ab_p1' => $que_score[$i]->nip]);

                $simu_ab = Helpab::find($query01[$in]->id);
                $simu_ab->ab_p1 = $que_score[$i]->nip;
                $simu_ab->sco_p1 = $que_score[$i]->totScore;
                if ($cekJumlah[0]->abp1cek < $kuota && $fitMetPen > 0) {
                    $simu_ab->update();
                    break;
                }
            }
        }

        $cekdis = DB::select('SELECT nip, jatahs.ab_p1 AS kuota, COUNT(helpabs.ab_p1) AS entri FROM jatahs, helpabs WHERE nip = helpabs.ab_p1 GROUP BY nip HAVING (kuota - entri) > 0');

        //$ceknull = DB::select('SELECT id, an_p1 FROM helpans WHERE an_p1 IS NULL');
        $ceknull = Helpab::select("*")->whereNull('ab_p1')->get();
            if (count($ceknull) > 0) {
                for ($f = 0; $f < count($ceknull); $f++) {
                    $ceknull[$f]->ab_p1 = $cekdis[0]->nip;
                    $ceknull[$f]->sco_p1 = '0.0';
                    $ceknull[$f]->update();
                }
            }    

        //p2    
        $query02 = DB::select('SELECT id, nim, bidangIlmu, metPen FROM usulans WHERE SUBSTRING(nim,6,2) = :hb',['hb' => strval($id)]);
       
        for ($in = 0; $in < count($query02); $in++) {
            $que_score = DB::select('SELECT filtereduser.id AS nip, nama, SUM(score*bobot) AS totScore, ab_p1, ab_p2, ab_p3 FROM (SELECT * FROM users WHERE role = :role) AS filtereduser LEFT JOIN jatahs ON filtereduser.id = jatahs.nip LEFT JOIN scorings ON filtereduser.id = scorings.nip LEFT JOIN criterias ON scorings.idCriteria = criterias.id AND (idVariabel = :bidangIlmu OR idVariabel = :hb OR idVariabel = :metPen) GROUP BY filtereduser.id ORDER BY totScore DESC',['role'=>'dosen', 'bidangIlmu' => $query02[$in]->bidangIlmu, 'hb'=>strval($id),'metPen'=>$query02[$in]->metPen]);

                for ($i = 0; $i < count($que_score); $i++) {

                    $adjustMetPen = DB::select('SELECT nip FROM scorings WHERE nip = :nip AND idVariabel = :metpen',['nip'=> $que_score[$i]->nip, 'metpen' => $query02[$in]->metPen]);
                    $fitMetPen = count($adjustMetPen);

                    $kuota = $que_score[$i]->ab_p2;
                    //$cekJumlah = count_array_values($groupOfp1an,$que_score[$i]->nip);
                    $cekJumlah = DB::select('SELECT COUNT(ab_p2) AS abp2cek FROM helpabs WHERE ab_p2 = :ab_p2',['ab_p2' => $que_score[$i]->nip]);

                    $simu_ab = Helpab::find($query02[$in]->id);
                    
                    if ($simu_ab->ab_p1 === $que_score[$i]->nip) {
                        continue;
                    } else {
                        if ($cekJumlah[0]->abp2cek < $kuota && $fitMetPen > 0) {
                            $simu_ab->ab_p2 = $que_score[$i]->nip;
                            $simu_ab->sco_p2 = $que_score[$i]->totScore;
                            $simu_ab->update();
                            break;
                        }   
                    }
                }

                $cekdis = DB::select('SELECT nip, jatahs.ab_p2 AS kuota, COUNT(helpabs.ab_p2) AS entri FROM jatahs, helpabs WHERE nip = helpabs.ab_p2 GROUP BY nip HAVING (kuota - entri) > 0');

                //$ceknull = DB::select('SELECT id, an_p1 FROM helpans WHERE an_p1 IS NULL');
                $ceknull = Helpab::select("*")->whereNull('ab_p2')->get();
                    if (count($ceknull) > 0) {
                        for ($f = 0; $f < count($ceknull); $f++) {
                            $ceknull[$f]->ab_p2 = $cekdis[0]->nip;
                            $ceknull[$f]->sco_p2 = '0.0';
                            $ceknull[$f]->update();
                        }
                    } 
        }

        //p3   
        $query03 = DB::select('SELECT id, nim, bidangIlmu, metPen FROM usulans WHERE SUBSTRING(nim,6,2) = :hb',['hb' => strval($id)]);

        for ($in = 0; $in < count($query03); $in++) {
            $que_score = DB::select('SELECT filtereduser.id AS nip, nama, SUM(score*bobot) AS totScore, ab_p1, ab_p2, ab_p3 FROM (SELECT * FROM users WHERE role = :role) AS filtereduser LEFT JOIN jatahs ON filtereduser.id = jatahs.nip LEFT JOIN scorings ON filtereduser.id = scorings.nip LEFT JOIN criterias ON scorings.idCriteria = criterias.id AND (idVariabel = :bidangIlmu OR idVariabel = :hb OR idVariabel = :metPen) GROUP BY filtereduser.id ORDER BY totScore DESC',['role'=>'dosen', 'bidangIlmu' => $query03[$in]->bidangIlmu, 'hb'=>strval($id),'metPen'=>$query03[$in]->metPen]);

                for ($i = 0; $i < count($que_score); $i++) {

                    $adjustMetPen = DB::select('SELECT nip FROM scorings WHERE nip = :nip AND idVariabel = :metpen',['nip'=> $que_score[$i]->nip, 'metpen' => $query03[$in]->metPen]);
                    $fitMetPen = count($adjustMetPen);

                    $kuota = $que_score[$i]->ab_p3;
                    //$cekJumlah = count_array_values($groupOfp1an,$que_score[$i]->nip);
                    $cekJumlah = DB::select('SELECT COUNT(ab_p3) AS abp3cek FROM helpabs WHERE ab_p3 = :ab_p3',['ab_p3' => $que_score[$i]->nip]);

                    $simu_ab = Helpab::find($query03[$in]->id);
                    
                    if ($simu_ab->ab_p1 === $que_score[$i]->nip || $simu_ab->ab_p2 === $que_score[$i]->nip) {
                        continue;
                    } else {
                        if ($cekJumlah[0]->abp3cek < $kuota && $fitMetPen > 0) {
                            $simu_ab->ab_p3 = $que_score[$i]->nip;
                            $simu_ab->sco_p3 = $que_score[$i]->totScore;
                            $simu_ab->update();
                            break;
                        }   
                    }
                }

                $cekdis = DB::select('SELECT nip, jatahs.ab_p3 AS kuota, COUNT(helpabs.ab_p3) AS entri FROM jatahs, helpabs WHERE nip = helpabs.ab_p3 GROUP BY nip HAVING (kuota - entri) > 0');

                //$ceknull = DB::select('SELECT id, an_p1 FROM helpans WHERE an_p1 IS NULL');
                $ceknull = Helpab::select("*")->whereNull('ab_p3')->get();
                    if (count($ceknull) > 0) {
                        for ($f = 0; $f < count($ceknull); $f++) {
                            $ceknull[$f]->ab_p3 = $cekdis[0]->nip;
                            $ceknull[$f]->sco_p3 = '0.0';
                            $ceknull[$f]->update();
                        }
                    } 
        }
        $response['success'] = true;
        $response['message'] = "Usulan dengan id";
        $response['data'] = DB::select('SELECT id, ab_p1, ab_p2, ab_p3, (SELECT nama FROM users WHERE id = ab_p1) AS nama_p1, (SELECT nama FROM users WHERE id = ab_p2) AS nama_p2, (SELECT nama FROM users WHERE id = ab_p3) AS nama_p3, sco_p1, sco_p2, sco_p3 FROM helpabs');
        return response()->json($response, 200);
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
