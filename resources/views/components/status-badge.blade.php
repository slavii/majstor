@props(['status'])

@php
$colors = [
    'new' => 'bg-blue-100 text-blue-800',
    'scheduled' => 'bg-yellow-100 text-yellow-800',
    'in_progress' => 'bg-orange-100 text-orange-800',
    'completed' => 'bg-green-100 text-green-800',
    'cancelled' => 'bg-red-100 text-red-800',
];
$labels = \App\Models\Job::STATUSES;
@endphp

<span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium {{ $colors[$status] ?? 'bg-gray-100 text-gray-800' }}">
    {{ $labels[$status] ?? $status }}
</span>
