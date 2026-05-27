@props([
    'id' => 'modal-delete',
    'title' => 'Hapus data?',
    'description' => 'Data yang dihapus tidak dapat dikembalikan.',
    'action' => '#',
])

<dialog id="{{ $id }}" class="w-full max-w-md rounded-2xl p-0 shadow-2xl backdrop:bg-ink/55">
    <form method="POST" action="{{ $action }}" class="space-y-5 p-6">
        @csrf
        @method('DELETE')

        <div class="flex items-start gap-3">
            <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-red-50 text-red-600">
                <span class="material-symbols-outlined">delete</span>
            </span>
            <div>
                <h2 class="font-display text-xl font-600 text-primary">{{ $title }}</h2>
                <p class="mt-1 text-sm leading-6 text-on-surface-variant">{{ $description }}</p>
            </div>
        </div>

        <div class="flex justify-end gap-3">
            <button type="button" class="rounded-full border border-outline-variant px-4 py-2 font-600 text-primary transition-colors hover:bg-surface-container" onclick="document.getElementById('{{ $id }}').close()">
                Batal
            </button>
            <button type="submit" class="inline-flex items-center gap-1.5 rounded-full bg-red-600 px-4 py-2 font-600 text-white transition-colors hover:bg-red-700">
                <span class="material-symbols-outlined text-[18px]">delete</span>
                Hapus
            </button>
        </div>
    </form>
</dialog>
