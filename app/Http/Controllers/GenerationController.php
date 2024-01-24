<?php

namespace App\Http\Controllers;

use App\Models\Generation;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class GenerationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $links = Generation::all();



        //         Create a new Excel file
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set headers
        $sheet->setCellValue('A1', 'link');
        $sheet->setCellValue('B1', 'value');

        $cellRange1 = 'A'. 1 .':C'. 1;
        $sheet->getStyle($cellRange1)->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getStyle($cellRange1)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle($cellRange1)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        $sheet->getStyle($cellRange1)->getAlignment()->setWrapText(true);
        $sheet->getStyle($cellRange1)->getAlignment()->setIndent(1);
        $sheet->getStyle($cellRange1)->getAlignment()->setShrinkToFit(true);

        // Populate user data
        foreach ($links as $index => $link) {

            $row = $index + 2;
            $sheet->setCellValue('A'.$row, $link['link']);
            $sheet->setCellValue('B'.$row, $link['value']);

            // Set cell styling and spacing
            $cellRange = 'A'.$row.':C'.$row;
            $sheet->getStyle($cellRange)->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
            $sheet->getStyle($cellRange)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle($cellRange)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
            $sheet->getStyle($cellRange)->getAlignment()->setWrapText(true);
            $sheet->getStyle($cellRange)->getAlignment()->setIndent(1);
            $sheet->getStyle($cellRange)->getAlignment()->setShrinkToFit(true);
        }

        // Set the file name and save the Excel file
        $fileName = 'links.xlsx';
        $writer = new Xlsx($spreadsheet);
        $writer->save($fileName);

        // Return the file as a response
        return response()->download($fileName)->deleteFileAfterSend(true);

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $lastGeneration = Generation::MAX('id')??0;
        $links = [];

        // Perform the operations or code you want to measure

        for ($i = 0; $i < $request->number; $i++) {
            $links[$i] = [
               'link' => 'link/rate/'.$lastGeneration + 1 + $i,
            ];

        }
        Generation::insert($links);

        //         Create a new Excel file
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set headers
        $sheet->setCellValue('A1', 'link');

        $cellRange1 = 'A'. 1 .':C'. 1;
        $sheet->getStyle($cellRange1)->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getStyle($cellRange1)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle($cellRange1)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        $sheet->getStyle($cellRange1)->getAlignment()->setWrapText(true);
        $sheet->getStyle($cellRange1)->getAlignment()->setIndent(1);
        $sheet->getStyle($cellRange1)->getAlignment()->setShrinkToFit(true);

        // Populate user data
        foreach ($links as $index => $link) {

            $row = $index + 2;
            $sheet->setCellValue('A'.$row, $link['link']);

            // Set cell styling and spacing
            $cellRange = 'A'.$row.':C'.$row;
            $sheet->getStyle($cellRange)->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
            $sheet->getStyle($cellRange)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle($cellRange)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
            $sheet->getStyle($cellRange)->getAlignment()->setWrapText(true);
            $sheet->getStyle($cellRange)->getAlignment()->setIndent(1);
            $sheet->getStyle($cellRange)->getAlignment()->setShrinkToFit(true);
        }

        // Set the file name and save the Excel file
        $fileName = 'links.xlsx';
        $writer = new Xlsx($spreadsheet);
        $writer->save($fileName);

        // Return the file as a response
        return response()->download($fileName)->deleteFileAfterSend(true);

    }

    /**
     * Display the specified resource.
     */
    public function create_value( Request $request)
    {
      $generation = Generation::where('link',$request->link)->first();
        $generation->update(['value'=>$request->value]);
        return $generation;
    }

    public function show( Request $request)
    {
       return $generation = Generation::where('link',$request->link)->first();


    }



}
