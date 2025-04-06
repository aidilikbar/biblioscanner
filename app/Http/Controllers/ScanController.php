<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ScanController extends Controller
{
    public function index()
    {
        return view('scan', [
            'fileName' => session('fileName'),
            'citation' => session('citation'),
            'summary' => session('summary'),
            'recommendations' => session('recommendations'),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:pdf|max:5120',
        ]);

        // Simulate file processing
        $file = $request->file('file');
        $fileName = $file->getClientOriginalName();

        // Simulated outputs
        $citation = "Beer, M., & Nohria, N. (2000). Cracking the Code of Change. Harvard Business Review.";
        $summary = "The paper contrasts Theory E and Theory O in change management, advocating for an integrated approach.";
        $recommendations = <<<TEXT
📘 JOURNAL ARTICLES:
1. Kotter, J. P. (1995). Leading Change: Why Transformation Efforts Fail.
2. Nadler, D. A., & Tushman, M. L. (1997). Competing by Design.

📚 BOOKS:
1. Kotter, J. P. (2012). Leading Change.
2. Collins, J., & Porras, J. (2004). Built to Last.
TEXT;

        return redirect()->route('scan.index')->with([
            'fileName' => $fileName,
            'citation' => $citation,
            'summary' => $summary,
            'recommendations' => $recommendations,
        ]);
    }
}