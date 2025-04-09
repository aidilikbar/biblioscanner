<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Smalot\PdfParser\Parser;
use App\Services\OpenAIService;

class AIScanController extends Controller
{
    protected $openai;

    public function __construct(OpenAIService $openai)
    {
        $this->openai = $openai;
    }

    public function index()
    {
        return view('aiscan.index');
    }

    public function scan(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:pdf|max:5120',
        ]);

        try {
            // Get the uploaded temp file path
            $tempPath = $request->file('file')->getRealPath();

            // Extract text using PDF parser
            $parser = new Parser();
            $pdf = $parser->parseFile($tempPath);
            $excerpt = substr($pdf->getText(), 0, 2000); // Limit for safety

        } catch (\Exception $e) {
            // If parsing fails, fallback to a dummy string for demo
            $excerpt = "Cracking the Code of Change by Michael Beer and Nitin Nohria...";
        }

        // Call OpenAI for citation/summary/recommendation
        $metadata = $this->openai->analyze($excerpt);

        // Normalize recommendations into array
        $recommendationsRaw = $metadata['recommendations'] ?? null;
        $recommendations = [];

        if ($recommendationsRaw) {
            $recommendations = preg_split('/\r\n|\r|\n|\• /', trim($recommendationsRaw));
            $recommendations = array_filter(array_map('trim', $recommendations));
        }

        return view('aiscan.result', [
            'fileName' => $request->file('file')->getClientOriginalName(),
            'citation' => $metadata['citation'] ?? null,
            'summary' => $metadata['summary'] ?? null,
            'recommendations' => $recommendations,
        ]);
    }
}