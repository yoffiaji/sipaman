@extends('layouts.admin')
@section('title', 'Audit Trail')
@section('page-title', 'Audit Trail')
@section('content')
<div class="space-y-6">
<div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm"><h2 class="font-display text-xl font-bold">Audit Trail Perubahan Data</h2><table class="mt-5 w-full text-left text-sm"><thead class="bg-slate-50"><tr><th class="px-4 py-3">Waktu</th><th class="px-4 py-3">User</th><th class="px-4 py-3">Aksi</th><th class="px-4 py-3">Tabel</th><th class="px-4 py-3">Record</th></tr></thead><tbody>@forelse($audits as $audit)<tr class="border-t"><td class="px-4 py-3">{{ $audit->created_at?->format('d/m/Y H:i') }}</td><td class="px-4 py-3">{{ $audit->user?->nama ?? '-' }}</td><td class="px-4 py-3">{{ $audit->aksi }}</td><td class="px-4 py-3">{{ $audit->tabel_terkait }}</td><td class="px-4 py-3">{{ $audit->record_id ?? '-' }}</td></tr>@empty<tr><td colspan="5" class="px-4 py-8 text-center text-slate-500">Belum ada audit trail.</td></tr>@endforelse</tbody></table><div class="mt-4">{{ $audits->links() }}</div></div>
<div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm"><h2 class="font-display text-xl font-bold">Aktivitas Terakhir</h2><div class="mt-4 space-y-2">@forelse($activities as $activity)<div class="rounded-lg border border-slate-100 p-3 text-sm"><span class="font-semibold">{{ $activity->user?->nama ?? '-' }}</span> — {{ $activity->aktivitas }} <span class="text-slate-500">{{ $activity->created_at?->format('d/m/Y H:i') }}</span></div>@empty<p class="text-sm text-slate-500">Belum ada aktivitas.</p>@endforelse</div></div>
</div>
@endsection
