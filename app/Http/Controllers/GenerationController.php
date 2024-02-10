<?php

namespace App\Http\Controllers;

use App\Models\Generation;
use Illuminate\Http\Request;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class GenerationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $links = Generation::all();

        return view('pdf_template', ['links' => $links]);

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $lastGeneration = Generation::MAX('id') ?? 0;
        $links = [];

        // Perform the operations or code you want to measure

        for ($i = 0; $i < $request->number; $i++) {
            $link = 'link/rate/'.$lastGeneration + 1 + $i;

            $qrCode = QrCode::format('svg')
                ->size(500)
                ->generate($link);
            $fileName = 'QR/link_rate'.$lastGeneration + 1 + $i.'.svg';
            $path = public_path($fileName);
            if (! file_exists(dirname($path))) {
                mkdir(dirname($path), 0755, true);
            }
            file_put_contents($path, $qrCode);
            $links[$i] = [
                'link' => $link,
                'QR' => $fileName,
            ];

        }
        Generation::insert($links);

    }

    public function show(Request $request)
    {
        return $generation = Generation::where('link', $request->link)->first();

    }

    public function create_value(Request $request)
    {
        $generation = Generation::where('link', $request->link)->first();
        $generation->update(['value' => $request->value]);

        return $generation;
    }
}
