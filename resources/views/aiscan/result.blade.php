@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto mt-10">
    <h1 class="text-2xl font-bold mb-6">📊 AI Scan Results</h1>

    <div class="bg-gray-100 p-4 mt-6 rounded">
        <p class="text-sm text-gray-700"><strong>📁 File:</strong> {{ $fileName }}</p>
        <p class="text-sm text-gray-700 mt-2"><strong>📌 Citation:</strong> {{ $citation ?? 'Not found' }}</p>
        <p class="text-sm text-gray-700 mt-2"><strong>📝 Summary:</strong> {{ $summary ?? 'Not found' }}</p>

        @if (!empty($recommendations) && is_iterable($recommendations))
            <div class="mt-4">
                <strong>📚 Recommendations:</strong>
                <ul class="list-disc list-inside mt-2 text-sm text-gray-800">
                    @foreach ($recommendations as $rec)
                        <li>{{ $rec }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>
</div>
@endsection