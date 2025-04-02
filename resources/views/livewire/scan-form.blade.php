<div>
    <form wire:submit.prevent="upload" class="space-y-4">
        <div>
            <label class="block font-semibold mb-1">Upload Academic PDF</label>
            <input type="file" wire:model="file" class="block w-full border rounded p-2">
            @error('file') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
        </div>

        <button type="submit"
            class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 disabled:opacity-50"
            wire:loading.attr="disabled"
            wire:target="upload">
            📤 Scan
        </button>

        <div wire:loading wire:target="upload" class="text-sm text-gray-500 mt-2">
            ⏳ Scanning document with AI...
        </div>
    </form>

    <hr class="my-6">

    @if ($scan)
        <div class="bg-white rounded shadow p-6 space-y-6">

            @if ($scan->file_url)
                <div>
                    <a href="{{ $scan->file_url }}" target="_blank" class="text-blue-600 underline">
                        📄 View Uploaded PDF
                    </a>
                </div>
            @endif

            <div>
                <h2 class="text-xl font-bold mb-2">📄 APA Citation</h2>
                <p class="text-gray-700">{{ $scan->citation }}</p>
            </div>

            <div>
                <h2 class="text-xl font-bold mb-2">📝 Summary</h2>
                <p class="text-gray-700">{{ $scan->summary }}</p>
            </div>

            <div>
                <h2 class="text-xl font-bold mb-2">📚 Recommendations</h2>
                <pre class="bg-gray-100 p-3 rounded text-sm whitespace-pre-wrap text-gray-800">
{{ $scan->recommendations }}
                </pre>
            </div>
        </div>
    @endif
</div>