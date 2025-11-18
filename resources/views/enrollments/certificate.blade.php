@extends('layouts.app')

@section('title', 'Certificate of Completion')

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="mb-6">
        <a href="{{ route('enrollments.index') }}" class="text-indigo-600 hover:text-indigo-700 text-sm font-medium">
            ← Back to My Enrollments
        </a>
    </div>

    <!-- Certificate -->
    <div id="certificate" class="bg-white rounded-lg shadow-2xl p-12 border-8 border-double border-indigo-600 text-center">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-5xl font-bold text-indigo-600 mb-2">Certificate of Completion</h1>
            <div class="w-32 h-1 bg-indigo-600 mx-auto"></div>
        </div>

        <!-- Body -->
        <div class="mb-8">
            <p class="text-lg text-gray-600 mb-6">This is to certify that</p>
            <h2 class="text-4xl font-bold text-gray-900 mb-6">{{ $enrollment->user->name }}</h2>
            <p class="text-lg text-gray-600 mb-4">has successfully completed the course</p>
            <h3 class="text-3xl font-semibold text-indigo-600 mb-8">{{ $enrollment->course->title }}</h3>
            
            <div class="grid grid-cols-2 gap-8 max-w-2xl mx-auto mb-8">
                <div>
                    <p class="text-sm text-gray-500 mb-1">Completion Date</p>
                    <p class="text-lg font-semibold text-gray-900">{{ $enrollment->completed_at->format('F d, Y') }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 mb-1">Instructor</p>
                    <p class="text-lg font-semibold text-gray-900">{{ $enrollment->course->teacher->name }}</p>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="border-t-2 border-gray-200 pt-8">
            <div class="grid grid-cols-2 gap-8 max-w-2xl mx-auto">
                <div>
                    <div class="border-t-2 border-gray-900 pt-2 mx-8">
                        <p class="text-sm font-semibold text-gray-900">{{ $enrollment->course->teacher->name }}</p>
                        <p class="text-xs text-gray-500">Course Instructor</p>
                    </div>
                </div>
                <div>
                    <div class="border-t-2 border-gray-900 pt-2 mx-8">
                        <p class="text-sm font-semibold text-gray-900">{{ config('app.name') }}</p>
                        <p class="text-xs text-gray-500">Platform Administrator</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Certificate ID -->
        <div class="mt-8 pt-8 border-t-2 border-gray-200">
            <p class="text-xs text-gray-500">Certificate ID: {{ strtoupper(Str::random(12)) }}-{{ $enrollment->id }}</p>
        </div>
    </div>

    <!-- Actions -->
    <div class="flex justify-center space-x-4 mt-8">
        <button onclick="window.print()" class="px-6 py-3 bg-indigo-600 text-white font-medium rounded-lg hover:bg-indigo-700 flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
            </svg>
            Print Certificate
        </button>
        <button onclick="downloadPDF()" class="px-6 py-3 bg-green-600 text-white font-medium rounded-lg hover:bg-green-700 flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            Download PDF
        </button>
    </div>
</div>

@push('scripts')
<script>
function downloadPDF() {
    // This is a placeholder - you would integrate a PDF generation library
    // like jsPDF or server-side PDF generation
    alert('PDF download functionality would be implemented here with a library like jsPDF or DomPDF on the backend.');
}

// Print styles
const style = document.createElement('style');
style.innerHTML = `
    @media print {
        body * {
            visibility: hidden;
        }
        #certificate, #certificate * {
            visibility: visible;
        }
        #certificate {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
        }
        .no-print {
            display: none !important;
        }
    }
`;
document.head.appendChild(style);
</script>
@endpush
@endsection