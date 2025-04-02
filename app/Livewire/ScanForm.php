<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use App\Models\Scan;
use App\Services\OpenAIService;

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
        logger('📥 upload() method triggered');

        $this->validate([
            'file' => 'required|mimes:pdf|max:5120',
        ]);

        try {
            // 1. Upload to DigitalOcean Spaces
            $fileName = time() . '_' . Str::slug($this->file->getClientOriginalName()) . '.pdf';
            $filePath = $this->file->storeAs('scans', $fileName, 'spaces');
            $fileUrl = Storage::disk('spaces')->url($filePath);

            logger('✅ Uploaded to Spaces', [
                'file_name' => $fileName,
                'file_path' => $filePath,
                'file_url' => $fileUrl,
            ]);

            // 2. Download from Spaces
            $fileContent = Storage::disk('spaces')->get($filePath);
            $tmpPath = storage_path("app/tmp/{$fileName}");
            Storage::disk('local')->put("tmp/{$fileName}", $fileContent);

            logger('📂 Saved temp file locally for OpenAI', ['tmp_path' => $tmpPath]);

            // 3. Upload to OpenAI
            $openaiFileId = $openai->uploadFile($tmpPath, $fileName);
            logger('🚀 Uploaded to OpenAI', ['openai_file_id' => $openaiFileId]);

            // 4. Cleanup local file
            Storage::disk('local')->delete("tmp/{$fileName}");

            // 5. Dummy excerpt (replace with PDF parsing later)
            $excerpt = "Cracking the Code of Change by Michael Beer and Nitin Nohria...";

            // 6. Metadata
            $meta = $openai->extractMetadata($excerpt);
            logger('📄 Metadata response', ['meta' => $meta]);

            $citation = $this->extractCitation($meta['metadata'] ?? '');
            $summary = $this->extractSummary($meta['metadata'] ?? '');

            // 7. Recommendations
            $recs = $openai->getRecommendations($summary ?? $excerpt);
            logger('📚 Recommendations response', ['recs' => $recs]);

            $recommendations = $recs['recommendations'] ?? null;

            // 8. Save to DB
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

            logger('✅ Scan saved to database', ['scan_id' => $this->scan->id]);
        } catch (\Throwable $e) {
            logger('❌ Error in upload()', ['message' => $e->getMessage()]);
            $this->addError('file', 'Something went wrong. Please try again.');
        }
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