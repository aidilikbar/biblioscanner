@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto mt-10">
    <h1 class="text-2xl font-bold mb-6"><i class="fas fa-book-open text-indigo-600"></i> AI Powered BiblioScanner</h1>

    <form action="{{ route('aiscan.scan') }}" method="POST" enctype="multipart/form-data" class="bg-white p-6 rounded shadow">
        @csrf
        <div class="mb-4">
            <label class="block font-semibold mb-1">Upload Academic PDF</label>
            <input type="file" name="file" accept="application/pdf" class="w-full border rounded p-2">
        </div>
        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
            📤 Scan with AI
        </button>
    </form>

    <div class="bg-gray-100 p-4 mt-6 rounded">
        <p class="text-sm text-gray-700"><strong><i class="fas fa-file text-yellow-600"></i>  File:</strong> {{ $fileName }}</p>
        <p class="text-sm text-gray-700 mt-2"><strong><i class="fas fa-thumbtack text-red-500"></i> Citation:</strong> {{ $citation ?? 'Not found' }}</p>
        <p class="text-sm text-gray-700 mt-2"><strong><i class="fas fa-align-left text-green-600"></i> Summary:</strong> {{ $summary ?? 'Not found' }}</p>

        @if (!empty($recommendations) && is_iterable($recommendations))
            <div class="mt-4">
                <strong><i class="fas fa-book-open text-indigo-600 mr-2"></i> Recommendations:</strong>
                <div class="bg-white text-sm mt-2 p-4 rounded border whitespace-pre-wrap overflow-x-auto break-words"> @foreach ($recommendations as $rec)
                    {{ $rec }}
                @endforeach</div>
            </div>
        @endif
    </div>
</div>
@endsection