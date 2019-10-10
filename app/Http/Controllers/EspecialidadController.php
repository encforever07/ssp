<?php

namespace App\Http\Controllers;

use App\models\CatPago;
use App\models\Especialidad;
use Illuminate\Http\Request;

class EspecialidadController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $especialidades = Especialidad::orderBy('nombre', 'ASC')->paginate(10);
        return [
            'especialidades' => $especialidades,
        ];
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function search($field, $value)
    {
        $especialidades = Especialidad::where($field, 'ilike', "%{$value}%")->paginate(10);
        return $especialidades;
    }
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
        $especialidad = new Especialidad();
        $especialidad->nombre = $request->get('nombre');
        $especialidad->sigla = $request->get('sigla');
        $especialidad->cat_pago_id = $request->get('cat_pago_id');
        $especialidad->save();
        return response()->json($especialidad);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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
        $especialidad = Especialidad::find($id);
        $especialidad->fill($request->all());
        $especialidad->save();
        return response()->json($especialidad);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $deleted = Especialidad::findOrFail($id);
        Especialidad::findOrFail($id)->delete();
    }
}
