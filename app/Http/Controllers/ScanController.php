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
        $citation = "Beer, M., & Nohria, N. (2000). Cracking the code of change. Harvard Business Review, 78(3), 133–141. https://hbr.org/2000/05/cracking-the-code-of-change";
        $summary = "The paper explores the contrasting approaches of Theory E (focused on economic value and top-down change) and Theory O (centered on organizational capability and employee engagement) in managing change. It advocates for an integrated strategy that leverages the strengths of both models to achieve sustainable transformation.";
        $recommendations = <<<TEXT
📘 JOURNAL ARTICLES:
<strong>1. Kotter, J. P. (1995). Leading change: Why transformation efforts fail. Harvard Business Review, 73(2), 59–67.</strong>
DOI: [N/A - HBR articles typically don’t have DOIs]
Kotter outlines an eight-step model for successful organizational transformation. It supports Theory O’s emphasis on building internal capability and culture, while warning against short-term fixes often aligned with Theory E. This article is a staple for change leaders seeking sustainable outcomes.

<strong>2. Nadler, D. A., & Tushman, M. L. (1997). Competing by design: The power of organizational architecture. Oxford University Press.</strong>
ISBN: 9780195116489
This work offers a framework for aligning organizational components such as strategy, structure, and culture — directly complementing Beer & Nohria’s integration of Theory E and O. It’s particularly relevant for organizations aiming to balance performance and human capital.


📚 BOOKS:
<strong>1. Kotter, J. P. (2012). Leading change (Rev. ed.). Harvard Business Review Press.</strong>
ISBN: 9781422186435
An expanded version of the 1995 HBR article, this book dives deeper into each of Kotter’s eight steps. It aligns well with Theory O’s structured yet people-centered change processes and offers practical tools for change leadership in dynamic environments.

<strong>2. Collins, J., & Porras, J. I. (2004). Built to last: Successful habits of visionary companies (Rev. ed.). HarperBusiness.</strong>
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