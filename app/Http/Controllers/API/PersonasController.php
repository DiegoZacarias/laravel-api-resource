<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Persona;
use Illuminate\Http\Request;

class PersonasController extends Controller
{
    public function __construct(Persona $personas)
    {
        $this->personas = $personas;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $personas = $this->personas->get();

        return response()->json($personas);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return false;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $persona = $this->personas->create($request->all());

        return response()->json($persona,201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return false;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        return false;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Persona $persona)
    {

        $persona->update($request->all());

        return response()->json($persona);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Persona $persona)
    {

        $persona->delete();

        return response()->json('Registro eliminado correctamente',204);
    }

    public function buscar(Request $request)
    {
        $personas = $this->personas->where('nombre', 'LIKE', '%' . $request->q . '%')->get();

        return response()->json($personas);
    }
}
