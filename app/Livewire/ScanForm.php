<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;

class ScanForm extends Component
{
    use WithFileUploads;

    public $file;
    public $citation;
    public $summary;
    public $recommendations;

    public function render()
    {
        return view('livewire.scan-form');
    }

    public function upload()
    {
        $this->validate([
            'file' => 'required|mimes:pdf|max:5120',
        ]);

        // ✅ MVP: Hardcoded responses
        $this->citation = "Beer, M., & Nohria, N. (2000). Cracking the code of change. *Harvard Business Review*, 78(3), 133–141.";

        $this->summary = "This article introduces two contrasting theories of change: Theory E (focused on economic value and shareholder returns) and Theory O (focused on building organizational culture and human capability). The authors argue that successful transformation requires integrating both approaches to avoid failure and build sustainable advantage.";

        $this->recommendations = <<<TEXT
JOURNAL ARTICLES

1. Kotter, J. P. (1995). Leading change: Why transformation efforts fail. *Harvard Business Review*, 73(2), 59–67.

2. Beer, M., & Nohria, N. (2000). Breaking the code of change. *Harvard Business Review*, 78(3), 133–141.

BOOKS

1. Kotter, J. P. (2012). *Leading Change*. Harvard Business Review Press.

2. Collins, J., & Porras, J. I. (2004). *Built to Last*. HarperBusiness.
TEXT;
    }
}