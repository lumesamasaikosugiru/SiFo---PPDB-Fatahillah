@php
    $file = $record->file_path;
    $url = Storage::url($file);
    $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
@endphp

<div class="w-full">

    @if (in_array($ext, ['jpg', 'jpeg', 'png', 'webp']))
        <img src="{{ $url }}" class="w-full rounded-lg">

    @elseif ($ext === 'pdf')
        <iframe src="{{ $url }}" class="w-full h-1/2 rounded-lg"></iframe>
    @endif

</div>