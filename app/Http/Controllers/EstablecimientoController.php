<?php

namespace App\Http\Controllers;

use App\Categoria;
use App\Establecimiento;
use App\Imagen;
use Illuminate\Http\Request;
use Intervention\Image\Facades\Image;


class EstablecimientoController extends Controller
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
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //consultar categorias
        $categorias = Categoria::all(); 
        
        return view('establecimientos.create', compact('categorias'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //Validacion

        
        $data = $request->validate([
            'nombre' => 'required',
            'categoria_id' => 'required|exists:App\Categoria,id',
            'imagen_principal' => 'required|image|max:1000',
            'direccion' => 'required',
            'colonia' => 'required',
            'lat' => 'required',
            'lng' => 'required',
            'telefono' => 'required|numeric',
            'descripcion' => 'required|min:50',
            'apertura' => 'date_format:H:i',
            'cierre' => 'date_format:H:i|after:apertura',
            'uuid' => 'required|uuid'
        ]); 
        

        //guardar la imagen
        $ruta_imagen = $request['imagen_principal']->store('principales', 'public'); 

        //resize imagen
        $img = Image::make(public_path("storage/${ruta_imagen}"))->fit(800, 600);
        $img->save(); 

        //guardar en bd
        $establecimiento = new Establecimiento($data); 
        $establecimiento->imagen_principal = $ruta_imagen; 
        $establecimiento->user_id = auth()->user()->id; 
        $establecimiento->save(); 


        return back()->with('estado', 'Tu informacion se almaceno correctamente');
        
     
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Establecimiento  $establecimiento
     * @return \Illuminate\Http\Response
     */
    public function show(Establecimiento $establecimiento)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Establecimiento  $establecimiento
     * @return \Illuminate\Http\Response
     */
    public function edit(Establecimiento $establecimiento)
    {
        //
        $categorias = Categoria::all(); 

        //obtener el establecimiento
        $establecimiento = auth()->user()->establecimientos;
        $establecimiento->apertura = date('H:i', strtotime($establecimiento -> apertura));
        $establecimiento->cierre = date('H:i', strtotime($establecimiento -> cierre));

        //obtiene las imagenes del establecimiento
        $imagenes = Imagen::where('id_establecimiento', $establecimiento->uuid)->get(); 

        return view('establecimientos.edit', compact('categorias', 'establecimiento', 'imagenes'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Establecimiento  $establecimiento
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Establecimiento $establecimiento)
    {

        //ejecutar el policy
        $this->authorize('update', $establecimiento); 

        //
        $data = $request->validate([
            'nombre' => 'required',
            'categoria_id' => 'required|exists:App\Categoria,id',
            'imagen_principal' => 'image|max:1000',
            'direccion' => 'required',
            'colonia' => 'required',
            'lat' => 'required',
            'lng' => 'required',
            'telefono' => 'required|numeric',
            'descripcion' => 'required|min:50',
            'apertura' => 'date_format:H:i',
            'cierre' => 'date_format:H:i|after:apertura',
            'uuid' => 'required|uuid'
        ]); 

        $establecimiento->nombre = $data['nombre'];
        $establecimiento->categoria_id = $data['categoria_id'];
        $establecimiento->direccion = $data['direccion'];
        $establecimiento->lat = $data['lat'];
        $establecimiento->lng = $data['lng'];
        $establecimiento->telefono = $data['telefono'];
        $establecimiento->descripcion = $data['descripcion'];
        $establecimiento->apertura = $data['apertura'];
        $establecimiento->cierre = $data['cierre'];
        $establecimiento->uuid = $data['uuid'];

        //si el usuario sube una foto, actualizarla
        if($request['imagen_principal']){
            //guardar la imagen
            $ruta_imagen = $request['imagen_principal']->store('principales', 'public'); 

            //resize imagen
            $img = Image::make(public_path("storage/${ruta_imagen}"))->fit(800, 600);
            $img->save(); 

            $establecimiento->imagen_principal = $ruta_imagen;
        }

        $establecimiento->save(); 

        //mandar mensaje a usuario
        return back()->with('estado', 'Tu informacion se almaceno correctamente');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Establecimiento  $establecimiento
     * @return \Illuminate\Http\Response
     */
    public function destroy(Establecimiento $establecimiento)
    {
        //
    }
}
