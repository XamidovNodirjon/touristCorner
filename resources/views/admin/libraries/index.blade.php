@extends('layouts.app')

@section('content')
<div class="container py-4">
    <style>
        body {
            background-color: #ffffff;
        }
        h1, h5 {
            color: #1a1a1a;
            font-weight: 600;
        }
        .card {
            border: 1px solid #e0e0e0;
            background-color: #fff;
        }
        .table thead {
            background-color: #f8f9fa;
            color: #333;
            font-weight: 600;
        }
        .table tbody tr:hover {
            background-color: #f5f5f5;
        }
        .btn {
            border-radius: 6px;
        }
        .btn-primary {
            background-color: #004085;
            border-color: #004085;
        }
        .btn-primary:hover {
            background-color: #003166;
        }
        .btn-outline-success {
            border-color: #28a745;
            color: #28a745;
        }
        .btn-outline-success:hover {
            background-color: #28a745;
            color: #fff;
        }
        .btn-warning {
            background-color: #ffc107;
            border: none;
            color: #000;
        }
        .btn-warning:hover {
            background-color: #e0a800;
        }
        .btn-danger {
            background-color: #dc3545;
            border: none;
        }
        .btn-danger:hover {
            background-color: #b02a37;
        }
        .modal-content {
            border-radius: 10px;
            border: 1px solid #e0e0e0;
        }
    </style>

    {{-- HEADER --}}
    <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-2">
        <h1 class="mb-0">Kutubxona boshqaruvi</h1>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createModal">
            <i class="bi bi-plus-circle"></i> Yangi qo‘shish
        </button>
    </div>

    <p class="text-secondary mb-4">Kutubxonadagi barcha hujjatlar va materiallarni boshqarish.</p>

    {{-- MAIN TABLE --}}
    <div class="card shadow-sm">
        <div class="card-body">
            <table class="table table-striped table-hover align-middle">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Sarlavha</th>
                        <th>Tavsif</th>
                        <th>Kategoriya</th>
                        <th>Rasm</th>
                        <th>Yaratilgan sana</th>
                        <th class="text-center">Harakatlar</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($libraries as $library)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $library->title_uz }}</td>
                            <td>{{ Str::limit($library->description_uz, 20) }}</td>
                            <td>{{ $library->category->name ?? 'Noma\'lum' }}</td>
                            <td>
                                @if($library->image)
                                    <img src="{{ asset('storage/' . $library->image) }}" alt="Image"
                                         width="60" height="60" class="rounded border">
                                @else
                                    <span class="text-muted">Rasm yo‘q</span>
                                @endif
                            </td>
                            
                            <td>{{ $library->created_at->format('d.m.Y') }}</td>
                            <td class="text-center">
                                {{-- Edit button --}}
                                <button type="button" class="btn btn-sm btn-warning me-1"
                                        data-bs-toggle="modal" data-bs-target="#editModal{{ $library->id }}">
                                    <i class="bi bi-pencil-square"></i>
                                </button>

                                {{-- Delete button --}}
                                <button type="button" class="btn btn-sm btn-danger"
                                        data-bs-toggle="modal" data-bs-target="#deleteModal{{ $library->id }}">
                                    <i class="bi bi-trash3"></i>
                                </button>
                            </td>
                        </tr>

                        {{-- EDIT MODAL --}}
                        <div class="modal fade" id="editModal{{ $library->id }}" tabindex="-1" aria-labelledby="editModalLabel{{ $library->id }}" aria-hidden="true">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <form action="{{ route('admin.libraries.update', $library->id) }}" method="POST" enctype="multipart/form-data">
                                        @csrf
                                        @method('PUT')
                                        <div class="modal-header bg-light border-bottom">
                                            <h5 class="modal-title">Ma’lumotni tahrirlash</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Yopish"></button>
                                        </div>

                                        <div class="modal-body">
                                            <div class="row">
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label">Sarlavha Uz</label>
                                                    <input type="text" name="title_uz" value="{{ $library->title_uz }}" class="form-control" >
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label">Sarlavha Ru</label>
                                                    <input type="text" name="title_ru" value="{{ $library->title_ru }}" class="form-control" >
                                                </div>
                                            
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label">Sarlavha En</label>
                                                    <input type="text" name="title_en" value="{{ $library->title_en }}" class="form-control" >
                                                </div>

                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label">Tavsif (O'zbekcha)</label>
                                                    <textarea name="description_uz" class="form-control" rows="3">{{ $library->description_uz }}</textarea>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label">Tavsif (Inglizcha)</label>
                                                    <textarea name="description_en" class="form-control" rows="3">{{ $library->description_en }}</textarea>
                                                </div>

                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label">Tavsif (Ruscha)</label>
                                                    <textarea name="description_ru" class="form-control" rows="3">{{ $library->description_ru }}</textarea>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label">Category</label>
                                                    <select name="category_id" class="form-select">
                                                        <option value=""> Kategoriyani tanlang </option>
                                                        @foreach ($categories as $category)
                                                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>

                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label">Hozirgi rasm:</label><br>
                                                    @if($library->image)
                                                        <img src="{{ asset('storage/' . $library->image) }}" width="100" class="rounded border mb-2">
                                                    @else
                                                        <span class="text-muted">Rasm yo‘q</span>
                                                    @endif
                                                    <input type="file" name="image" class="form-control" accept="image/*">
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label">Hozirgi Url (EN):</label><br>
                                                    <input type="text" name="file_path_en" value="{{ $library->file_path_en }}" class="form-control" >
                                                
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label">Hozirgi Url (RU):</label><br>
                                                    <input type="text" name="file_path_ru" value="{{ $library->file_path_ru }}" class="form-control" >
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label">Hozirgi Url (UZ):</label><br>
                                                    <input type="text" name="file_path_uz" value="{{ $library->file_path_uz }}" class="form-control" >
                                                </div>
                                            </div>
                                        </div>

                                        <div class="modal-footer bg-light">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Bekor qilish</button>
                                            <button type="submit" class="btn btn-primary">Yangilash</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        {{-- DELETE MODAL --}}
                        <div class="modal fade" id="deleteModal{{ $library->id }}" tabindex="-1" aria-labelledby="deleteModalLabel{{ $library->id }}" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <form action="{{ route('admin.libraries.destroy', $library->id) }}" method="POST">
                                        @csrf
                                        @method('DELETE')

                                        <div class="modal-header text-white">
                                            <h5 class="modal-title" id="deleteModalLabel{{ $library->id }}">Ma’lumotni o‘chirish</h5>
                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Yopish"></button>
                                        </div>
                                        <div class="modal-body">
                                            <p class="fw-semibold">Siz <strong>{{ $library->title }}</strong> nomli yozuvni o‘chirmoqchisiz.</p>
                                            <p class="text-danger mb-0">Bu amalni qaytarib bo‘lmaydi!</p>
                                        </div>
                                        <div class="modal-footer bg-light">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Bekor qilish</button>
                                            <button type="submit" class="btn btn-danger">Ha, o‘chirilsin</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-3">Hozircha ma’lumot yo‘q</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- CREATE MODAL --}}
<div class="modal fade" id="createModal" tabindex="-1" aria-labelledby="createModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <form action="{{ route('admin.libraries.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        
        <div class="modal-header bg-light border-bottom">
          <h5 class="modal-title" id="createModalLabel">Yangi ma’lumot qo‘shish</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Yopish"></button>
        </div>
        
        <div class="modal-body">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Sarlavha Uz</label>
                    <input type="text" name="title_uz" class="form-control">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Sarlavha En</label>
                    <input type="text" name="title_en" class="form-control">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Sarlavha Ru</label>
                    <input type="text" name="title_ru" class="form-control">
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Tavsif (O'zbekcha)</label>
                    <textarea name="description_uz" class="form-control" rows="3"></textarea>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Tavsif (Inglizcha)</label>
                    <textarea name="description_en" class="form-control" rows="3"></textarea>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Tavsif (Ruscha)</label>
                    <textarea name="description_ru" class="form-control" rows="3"></textarea>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Category <span class="text-danger">*</span></label>
                    <select name="category_id" class="form-select" required>
                        <option value=""> Kategoriyani tanlang </option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Rasim <span class="text-danger">*</span></label>
                    <input type="file" name="image" class="form-control" accept="image/*" required>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">RU</label>
                    <input type="url" name="file_path_ru" class="form-control">
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">EN</label>
                    <input type="url" name="file_path_en" class="form-control">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">UZ</label>
                    <input type="url" name="file_path_uz" class="form-control">
                </div>
            </div>

        </div>
        <div class="modal-footer bg-light">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Bekor qilish</button>
          <button type="submit" class="btn btn-primary">Saqlash</button>
        </div>
      </form>
      </div>
  </div>
</div>
@endsection
