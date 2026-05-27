@extends('layouts.admin')
@section('title', 'Log Aktivitas')
@section('page-title', 'Log Aktivitas')
@section('content')
<div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
    <h2 class="font-display text-xl font-bold">Log Aktivitas</h2>
    <table class="mt-5 w-full text-left text-sm">
        <thead class="bg-slate-50"><tr><th class="px-4 py-3">Waktu</th><th class="px-4 py-3">User</th><th class="px-4 py-3">Aktivitas</th><th class="px-4 py-3">IP</th></tr></thead>
        <tbody>@forelse($logs as $log)<tr class="border-t"><td class="px-4 py-3">{{ $log->created_at?->format('d/m/Y H:i') }}</td><td class="px-4 py-3">{{ $log->user?->nama ?? '-' }}</td><td class="px-4 py-3">{{ $log->aktivitas }}</td><td class="px-4 py-3">{{ $log->ip_address }}</td></tr>@empty<tr><td colspan="4" class="px-4 py-8 text-center text-slate-500">Belum ada log.</td></tr>@endforelse</tbody>
    </table>
    <div class="mt-4">{{ $logs->links() }}</div>
</div>
@endsection
