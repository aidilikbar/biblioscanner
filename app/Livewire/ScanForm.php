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
        logger('🔥 Upload method triggered');

        $this->citation = "Beer, M., & Nohria, N. (2000). Cracking the Code of Change. Harvard Business Review, 78(3), 133–141.";
        $this->summary = "This paper contrasts Theory E (economic value) and Theory O (organizational capability) in managing change. It argues for an integrated approach combining both.";
        $this->recommendations = <<<TEXT
    📘 JOURNAL ARTICLES:
    1. Kotter, J. P. (1995). Leading Change: Why Transformation Efforts Fail. HBR.
    2. Nadler, D. A., & Tushman, M. L. (1997). Competing by Design.

    📚 BOOKS:
    1. Kotter, J. P. (2012). Leading Change.
    2. Collins, J., & Porras, J. (2004). Built to Last.
    TEXT;
    }
}