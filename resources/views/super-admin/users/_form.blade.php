@csrf
@if (($method ?? 'POST') !== 'POST') @method($method) @endif
<div class="grid gap-4 md:grid-cols-2">
    <div><label class="text-sm font-semibold">Nama</label><input name="nama" value="{{ old('nama', $user->nama ?? '') }}" required class="mt-1 w-full rounded-lg border-slate-300"></div>
    <div><label class="text-sm font-semibold">Email</label><input type="email" name="email" value="{{ old('email', $user->email ?? '') }}" required class="mt-1 w-full rounded-lg border-slate-300"></div>
    <div><label class="text-sm font-semibold">Password {{ isset($user) ? '(kosongkan jika tidak diganti)' : '' }}</label><input type="password" name="password" {{ isset($user) ? '' : 'required' }} class="mt-1 w-full rounded-lg border-slate-300"></div>
    <div><label class="text-sm font-semibold">Role</label><select name="role" required class="mt-1 w-full rounded-lg border-slate-300">@foreach($roles as $role)<option value="{{ $role->nama_role }}" @selected(old('role', $user->role->nama_role ?? '') === $role->nama_role)>{{ $role->nama_role }}</option>@endforeach</select></div>
    <div><label class="text-sm font-semibold">Status Akun</label><select name="status_akun" class="mt-1 w-full rounded-lg border-slate-300">@foreach(['aktif','nonaktif','kunci'] as $status)<option value="{{ $status }}" @selected(old('status_akun', $user->status_akun ?? 'aktif') === $status)>{{ ucfirst($status) }}</option>@endforeach</select></div>
</div>
<div class="mt-6 flex gap-3"><button class="rounded-lg bg-slate-900 px-5 py-2.5 font-semibold text-white">Simpan</button><a href="{{ route('super-admin.users.index') }}" class="rounded-lg border border-slate-300 px-5 py-2.5 font-semibold">Batal</a></div>
