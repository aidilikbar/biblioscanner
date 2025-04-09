@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto mt-10">
    <h1 class="text-2xl font-bold mb-6">📚 AI Scanner</h1>

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
</div>
@endsection