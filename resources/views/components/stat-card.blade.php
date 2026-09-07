@props(['label', 'value', 'icon' => 'chart-bar', 'color' => 'indigo'])

@php
    $colors = [
        'indigo' => 'bg-indigo-50 text-indigo-600 dark:bg-indigo-500/15 dark:text-indigo-300',
        'green' => 'bg-green-50 text-green-600 dark:bg-green-500/15 dark:text-green-300',
        'amber' => 'bg-amber-50 text-amber-600 dark:bg-amber-500/15 dark:text-amber-300',
        'rose' => 'bg-rose-50 text-rose-600 dark:bg-rose-500/15 dark:text-rose-300',
    ];
@endphp

<div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-gray-200 dark:border-slate-700 p-5">
    <div class="flex items-center justify-between mb-3">
        <span class="text-2xl font-bold text-gray-800 dark:text-slate-100">{{ $value }}</span>
        <span class="w-10 h-10 rounded-lg flex items-center justify-center {{ $colors[$color] ?? $colors['indigo'] }}">
            <x-dynamic-component :component="'heroicon.'.$icon" class="w-5 h-5"/>
        </span>
    </div>
    <div class="text-sm text-gray-500 dark:text-slate-400">{{ $label }}</div>
</div>