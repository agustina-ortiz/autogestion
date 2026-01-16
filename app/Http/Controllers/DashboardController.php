<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Laravel\Facades\Image;

class DashboardController extends Controller
{
   public function verNoticia($id)
    {
        $noticia = DB::table('in_noticia')
            ->where('ID', $id)
            ->where('FECHAVTO', '>=', now())
            ->first();

        if (!$noticia) {
            abort(404);
        }

        return view('noticia-detalle', compact('noticia'));
    }
}