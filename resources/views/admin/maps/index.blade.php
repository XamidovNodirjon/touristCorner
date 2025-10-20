@extends('layouts.app')
@section('content')

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-2">
        <h2 class="mb-0">Harita Ma'lumotlarini Boshqarish</h2>
        
        <a href="{{ route('admin.maps.create') }}" class="btn btn-primary shadow-sm">
            <i class="bi bi-plus-circle-fill me-1"></i> Yangi Harita Yaratish
        </a>
    </div>
    @if($maps->isEmpty())
        <div class="alert alert-info text-center" role="alert">
            Hali hech qanday harita ma'lumotlari yaratilmagan.
        </div>
    @else
        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
            @foreach($maps as $map)
                <div class="col">
                    <div class="card shadow-sm h-100">
                        <img src="{{ $map->image ? asset('storage/' . $map->image) : asset('images/default_map.jpg') }}"
                            class="card-img-top"
                            alt="{{ $map->name }}"
                            style="height: 200px; object-fit: cover;">
                        
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title text-primary">{{ $map->title }}</h5>
                            <p class="card-text text-muted">{{ $map->latitude }}, {{ $map->longitude }}</p>
                            <p class="card-text text-muted flex-grow-1">{{ Str::limit($map->description, 100) }}</p>
                            
                            <div class="mt-3">
                                <form action="{{ route('admin.maps.destroy', $map->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Haqiqatan ham bu haritani oʻchirmoqchimisiz?');">
                                        <i class="bi bi-trash"></i> O'chirish
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection