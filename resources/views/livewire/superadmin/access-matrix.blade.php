<div>
    <x-page-title title="Rangkuman Modul Akses Menu" subtitle="Recap matrix hak akses tiap role. Penegakan dilakukan middleware CheckRole pada routes/web.php." />

    <div class="table-wrap">
        <table>
            <thead>
                <tr><th>Nama Menu</th>@foreach ($roles as $label)<th class="text-center">{{ $label }}</th>@endforeach</tr>
            </thead>
            <tbody>
            @foreach ($menus as $menu => $allowed)
                <tr>
                    <td class="font-medium">{{ $menu }}</td>
                    @foreach ($roles as $key => $label)
                        <td class="text-center">
                            @if (in_array($key, $allowed))<span class="text-emerald-500">✔</span>@else<span class="text-pink-300">✘</span>@endif
                        </td>
                    @endforeach
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    <p class="mt-4 text-xs text-gray-400">Untuk mengubah matriks, edit file config/rbac.php lalu sesuaikan middleware role pada route terkait.</p>
</div>
