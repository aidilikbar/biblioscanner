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

        // Simulate upload success — no actual saving needed for MVP
        $this->citation = "Beer, M., & Nohria, N. (2000). Cracking the code of change. *Harvard Business Review*, 78(3), 133–141.";

        $this->summary = "This article introduces two contrasting theories of change: Theory E (focused on economic value and shareholder returns) and Theory O (focused on building organizational culture and human capability). The authors argue that successful transformation requires integrating both approaches to avoid failure and build sustainable advantage. Case examples from Scott Paper, Champion International, and ASDA are used to illustrate the dynamics of each strategy.";

        $this->recommendations = <<<TEXT
JOURNAL ARTICLES

1. Kotter, J. P. (1995). Leading change: Why transformation efforts fail. *Harvard Business Review*, 73(2), 59–67.  
   - Describes an 8-step process for successful organizational change and why many efforts fail.  
   - [Link](https://hbr.org/1995/05/leading-change-why-transformation-efforts-fail)

2. Beer, M., & Nohria, N. (2000). Breaking the code of change. *Harvard Business Review*, 78(3), 133–141.  
   - A companion piece diving deeper into integrating Theory E and Theory O.  

BOOKS

1. Kotter, J. P. (2012). *Leading Change*. Harvard Business Review Press.  
   - A foundational book on how to manage change effectively through structure and culture.

2. Collins, J., & Porras, J. I. (2004). *Built to Last: Successful Habits of Visionary Companies*. HarperBusiness.  
   - Discusses the importance of values and culture in building enduring organizations.
TEXT;
    }
}