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
1. Kotter, J. P. (1995). Leading change: Why transformation efforts fail. Harvard Business Review, 73(2), 59–67.
DOI: [N/A - HBR articles typically don’t have DOIs]
Kotter outlines an eight-step model for successful organizational transformation. It supports Theory O’s emphasis on building internal capability and culture, while warning against short-term fixes often aligned with Theory E. This article is a staple for change leaders seeking sustainable outcomes.

2. Nadler, D. A., & Tushman, M. L. (1997). Competing by design: The power of organizational architecture. Oxford University Press.
ISBN: 9780195116489
This work offers a framework for aligning organizational components such as strategy, structure, and culture — directly complementing Beer & Nohria’s integration of Theory E and O. It’s particularly relevant for organizations aiming to balance performance and human capital.


📚 BOOKS:
1. Kotter, J. P. (2012). Leading change (Rev. ed.). Harvard Business Review Press.
ISBN: 9781422186435
An expanded version of the 1995 HBR article, this book dives deeper into each of Kotter’s eight steps. It aligns well with Theory O’s structured yet people-centered change processes and offers practical tools for change leadership in dynamic environments.

2. Collins, J., & Porras, J. I. (2004). Built to last: Successful habits of visionary companies (Rev. ed.). HarperBusiness.
ISBN: 9780060516406
While not exclusively focused on change, this book studies enduring companies that embraced cultural strength (Theory O) while maintaining high performance (Theory E). The findings emphasize the long-term benefit of integrated strategies for sustainable success.

TEXT;

        return redirect()->route('scan.index')->with([
            'fileName' => $fileName,
            'citation' => $citation,
            'summary' => $summary,
            'recommendations' => $recommendations,
        ]);
    }
}