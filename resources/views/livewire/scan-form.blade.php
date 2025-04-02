<div>
    <form wire:submit.prevent="upload" class="space-y-4">
        <div>
            <label class="block font-semibold mb-1">Upload Academic PDF</label>
            <input type="file" wire:model="file" class="block w-full border rounded p-2">
            @error('file') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
        </div>

        <button type="submit"
            class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
            Scan
        </button>

        @if ($file)
            <p class="text-sm text-gray-500 mt-2">Selected: {{ $file->getClientOriginalName() }}</p>
        @endif
    </form>

    <hr class="my-6">

    @if ($scan)
        <div class="bg-white rounded shadow p-4">
            <h2 class="text-xl font-bold mb-2">📄 Citation</h2>
            <p class="mb-4 text-gray-700">{{ $scan->citation }}</p>

            <h2 class="text-xl font-bold mb-2">📝 Summary</h2>
            <p class="mb-4 text-gray-700">{{ $scan->summary }}</p>

            <h2 class="text-xl font-bold mb-2">📚 Recommendations</h2>
            <pre class="bg-gray-100 p-3 rounded text-sm whitespace-pre-wrap">{{ $scan->recommendations }}</pre>
        </div>
    @endif
</div>