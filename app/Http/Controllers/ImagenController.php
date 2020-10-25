<?php

namespace App\Http\Controllers;

use App\Establecimiento;
use App\Imagen;
use Illuminate\Support\Facades\File;
use Illuminate\Http\Request;
use Intervention\Image\Facades\Image;

class ImagenController extends Controller
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
        //leer la imagen
        $ruta_imagen = $request->file('file')->store('establecimientos', 'public'); 

        //resize a la imagen
        $imagen = Image::make(public_path("storage/{$ruta_imagen}"))->fit(800, 450);
        $imagen->save(); 

        //almacenar en el modelo 
        $imagenDB = new Imagen;
        $imagenDB->id_establecimiento = $request['uuid'];
        $imagenDB->ruta_imagen = $ruta_imagen;

        $imagenDB->save(); 

        //retornar respuesta
        $respuesta = [
            'archivo' => $ruta_imagen
        ]; 

        return response()->json($respuesta); 
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Imagen  $imagen
     * @return \Illuminate\Http\Response
     */
    public function show(Imagen $imagen)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Imagen  $imagen
     * @return \Illuminate\Http\Response
     */
    public function edit(Imagen $imagen)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Imagen  $imagen
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Imagen $imagen)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Imagen  $imagen
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {

        //validacion
        $uuid = $request->get('uuid');

        $establecimiento = Establecimiento::where('uuid', $uuid)->first(); 
        $this->authorize('delete', $establecimiento); 

        //imagen a eliminar
        $imagen = $request->get('imagen'); 

        if(File::exists('storage/' . $imagen)){
            //elimina imagen del servidor
            File::delete('storage/' . $imagen); 

            //elimina de la bd
            Imagen::where('ruta_imagen', $imagen)->delete();

            $respuesta = [
                'mensaje' => 'imagen eliminada', 
                'imagen' => $imagen
            ];
        }

        return response()->json($respuesta);
    }
}
