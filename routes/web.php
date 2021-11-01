<?php

/** @var \Laravel\Lumen\Routing\Router $router */

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('/', function () use ($router) {
    return $router->app->version();
});

//bagian Criteria
$router->group(['middleware' => 'cors'], function () use ($router){
    $router->get('criteria','CriteriaController@index');
    $router->get('criteria/{id}','CriteriaController@show');
    $router->put('criteria/{id}','CriteriaController@update');
});   

//bagian Variabel
$router->group(['middleware' => 'cors'], function () use ($router){
    $router->get('variabel','VariabelController@index');
    $router->get('variabel/{id}','VariabelController@show');
    $router->get('variabel/{nip}/criteria/{idCriteria}/kodeInduk/{kodeParent}','VariabelController@showUnselected');
    $router->put('variabel/{id}','VariabelController@update');
    $router->get('varkuota/{id}','VariabelController@kuota');
});

//bagian Scoring
$router->group(['middleware' => 'cors'], function () use ($router){
    $router->get('scoring','ScoringController@index');
    $router->get('scoring/{id}','ScoringController@show');
    $router->put('scoring/{id}','ScoringController@update');
    $router->post('scoring','ScoringController@store');
    $router->get('scoringdel/{id}','ScoringController@destroy');    
});

//bagian User
$router->group(['middleware' => 'cors'], function () use ($router){
    $router->post('login','UserController@login');
    $router->get('logout/{id}','UserController@logout');
    $router->post('user/{id}','UserController@update');
    $router->get('user/{id}','UserController@show');
});

//bagian Usulan
$router->group(['middleware' => 'cors'], function () use ($router){
    $router->get('usulan','UsulanController@index');
    $router->get('usulan/{id}','UsulanController@show');
    $router->get('ras_usulan/{id}','UsulanController@showNIP');
    $router->get('lihat_usulan/{id}','UsulanController@showNIM');
    $router->get('judge_usulan/{id}','UsulanController@showHB');
    $router->post('usulan/{id}','UsulanController@update');
    $router->post('usulanm/{id}','UsulanController@updateMetPen');
    $router->post('usulan','UsulanController@store');
    $router->get('sim_usulan','UsulanController@simulan');
    $router->get('sim_usulab','UsulanController@simulab');
});

//bagian Rekap
$router->group(['middleware' => 'cors'], function () use ($router){
    $router->get('rekap','RekapController@index');
    $router->get('rekap/{id}','RekapController@show');
});