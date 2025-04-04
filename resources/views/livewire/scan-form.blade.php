<div class="space-y-6">

    {{-- Upload Form --}}
    <form wire:submit.prevent="upload" class="space-y-4 bg-white shadow p-6 rounded">
        <h2 class="text-xl font-semibold">📄 Upload Academic PDF</h2>

        <div>
            <label class="block font-semibold mb-1">Select a PDF File</label>
            <input type="file" wire:model="file" class="block w-full border rounded p-2" accept="application/pdf">
            @error('file') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
        </div>

        <div class="flex items-center space-x-3">
            <button type="submit"
                class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 disabled:opacity-50"
                wire:loading.attr="disabled"
                wire:target="upload">
                📤 Scan
            </button>

            <span wire:loading wire:target="upload" class="text-gray-600 text-sm">
                ⏳ Processing with AI...
            </span>
        </div>
    </form>

    {{-- Results --}}
    @if ($scan)
        <div class="bg-white rounded shadow p-6 space-y-6 border border-gray-100">

            @if ($scan->file_url)
                <div>
                    <a href="{{ $scan->file_url }}" target="_blank" class="text-blue-600 underline font-medium">
                        📎 View Uploaded PDF
                    </a>
                </div>
            @endif

            <div>
                <h2 class="text-xl font-bold mb-2">📄 APA Citation</h2>
                <p class="text-gray-800">{{ $scan->citation }}</p>
            </div>

            <div>
                <h2 class="text-xl font-bold mb-2">📝 Summary</h2>
                <p class="text-gray-800">{{ $scan->summary }}</p>
            </div>

            <div>
                <h2 class="text-xl font-bold mb-2">📚 Recommendations</h2>
                <div class="bg-gray-50 p-4 rounded text-sm text-gray-900 whitespace-pre-wrap">
                    {{ $scan->recommendations }}
                </div>
            </div>
        </div>
    @endif

</div>