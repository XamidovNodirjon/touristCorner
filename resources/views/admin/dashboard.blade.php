@extends('layouts.app')
@section('content') 
    <h4 class="mb-4 text-dark">Dashboard Bosh sahifasi</h4>
    
    <div class="alert alert-info border-0 rounded-0 border-start border-5 border-info" role="alert">
        <i class="bi bi-info-circle-fill me-2"></i> Xush kelibsiz, **{{ auth()->user()->name }}!** Bu yerda siz barcha ma'lumotlarni boshqarasiz.
    </div>
        
    <div class="row g-4 mb-5">
        <div class="col-lg-3 col-md-6">
            <a href="{{ route('admin.events') }}" class="stretched-link text-decoration-none">
                <div class="card shadow h-100 border-start border-success">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col me-2">
                                <div class="text-success fw-bold text-uppercase mb-1 small">Tadbirlar soni</div>
                                <div class="h5 mb-0 fw-bold text-gray-800">{{ $eventsCount }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="bi bi-calendar-check fs-1 text-success opacity-50"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-lg-3 col-md-6">
            <a href="{{route('admin.libraries')}}" class="stretched-link text-decoration-none">
                <div class="card shadow h-100 border-start border-warning">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col me-2">
                                <div class="text-warning fw-bold text-uppercase mb-1 small">Kutubxonadagi Hujjatlar</div>
                                <div class="h5 mb-0 fw-bold text-gray-800">{{ $librariesCount }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="bi bi-book-fill fs-1 text-warning opacity-50"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-lg-3 col-md-6">
            <a href="{{route('admin.maps')}}" class="stretched-link text-decoration-none">
                <div class="card shadow h-100 border-start border-primary">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col me-2">
                                <div class="text-primary fw-bold text-uppercase mb-1 small">Harita Ma'lumotlari</div>
                                <div class="h5 mb-0 fw-bold text-gray-800">{{ $mapsCount }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="bi bi-map-fill fs-1 text-primary opacity-50"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    </div>
@endsection