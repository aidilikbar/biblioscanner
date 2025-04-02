<x-app-layout>
    <div class="max-w-4xl mx-auto py-10 px-4">
        <h1 class="text-3xl font-bold mb-6">📚 BiblioScanner</h1>

        <livewire:scan-form />

        @if (session('error'))
            <div class="mt-6 text-red-600 bg-red-100 p-4 rounded">
                {{ session('error') }}
            </div>
        @endif
    </div>
</x-app-layout>