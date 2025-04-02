<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use App\Models\Scan;
use App\Services\OpenAIService;

#[WithFileUploads(disk: 'livewire-tmp')]
class ScanForm extends Component
{
    use WithFileUploads;

    public $file;
    public $scan;

    public function render()
    {
        return view('livewire.scan-form');
    }

    public function upload(OpenAIService $openai)
    {
        $this->validate([
            'file' => 'required|mimes:pdf|max:5120',
        ]);

        $fileName = time() . '_' . Str::slug($this->file->getClientOriginalName()) . '.pdf';
        $filePath = $this->file->storeAs('scans', $fileName, 'spaces');
        $fileUrl = Storage::disk('spaces')->url($filePath); 

        $openaiFileId = $openai->uploadFile(storage_path("app/public/{$filePath}"), $fileName);

        // NOTE: You can integrate real PDF parser here
        $excerpt = "Cracking the Code of Change by Michael Beer and Nitin Nohria...";

        $meta = $openai->extractMetadata($excerpt);
        $citation = $this->extractCitation($meta['metadata'] ?? '');
        $summary = $this->extractSummary($meta['metadata'] ?? '');

        $recs = $openai->getRecommendations($summary ?? $excerpt);
        $recommendations = $recs['recommendations'] ?? null;

        $this->scan = Scan::create([
            'user_id' => auth()->id(),
            'file_name' => $fileName,
            'file_path' => $filePath,
            'file_url' => $fileUrl,
            'openai_file_id' => $openaiFileId,
            'citation' => $citation,
            'summary' => $summary,
            'recommendations' => $recommendations,
        ]);
    }

    private function extractCitation(string $text): ?string
    {
        if (preg_match('/(?<citation>.+?\(\d{4}\).+?\.)/', $text, $match)) {
            return $match['citation'];
        }

        return null;
    }

    private function extractSummary(string $text): ?string
    {
        $lines = explode("\n", trim($text));
        $filtered = array_filter($lines, fn($line) => !str_contains($line, 'Citation'));
        return implode(" ", array_slice($filtered, 1));
    }
}