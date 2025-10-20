@extends('layouts.app')

@section('content')
<div class="container py-4">
    {{-- CUSTOM STYLES for better UI/UX --}}
    <style>
        /* Umumiy stilizatsiya */
        .card { border-radius: 10px; }
        .table thead th { background-color: #f8f9fa; font-weight: 600; }
        .btn-warning { color: #000; }

        /* Modal stilizatsiyasi */
        .modal-content {
            border-radius: 10px;
            box-shadow: 0 0 25px rgba(0, 0, 0, 0.1);
        }
        .modal-header {
            border-bottom: 1px solid #eee;
            background-color: #f8f9fa;
        }
        .modal-footer {
            border-top: 1px solid #eee;
        }
        /* Fonni yengil xiralashtirish (optional, but modern) */
        .modal-backdrop.show {
            backdrop-filter: blur(2px);
            -webkit-backdrop-filter: blur(2px);
            background-color: rgba(0,0,0,0.5) !important;
        }
    </style>

    {{-- HEADER --}}
    <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-2">
        <h1 class="h3 mb-0">Tadbirlar boshqaruvi</h1>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createModal">
            <i class="bi bi-plus-circle"></i> Yangi Tadbir Qo'shish
        </button>
    </div>

    {{-- XATOLAR/MUVAFFAQIYAT XABARI --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- MAIN TABLE --}}
    <div class="card shadow-sm">
        <div class="card-body">
            @if($events->isEmpty())
                <p class="text-muted text-center py-3">Hozircha hech qanday tadbirlar mavjud emas.</p>
            @else
                <div class="table-responsive">
                    <table class="table table-striped table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Sarlavha</th>
                                <th>Kategoriya</th>
                                <th>Tavsif</th>
                                <th>Rasm</th>
                                <th>Sana</th>
                                <th>Joylashuv</th>
                                <th>Narx</th>
                                <th class="text-center">Amallar</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($events as $event)
                            <tr>
                                <td>{{ $event->id }}</td>
                                <td>{{ $event->title_uz }}</td>
                                <td>{{ $event->category->name ?? '-' }}</td>
                                <td>{{ Str::limit($event->description_uz, 40) }}</td>
                                <td>
                                    @if($event->image)
                                        <img src="{{ asset('storage/' . $event->image) }}" width="70" height="70" class="rounded border object-fit-cover">
                                    @else
                                        <span class="text-muted">Yo‘q</span>
                                    @endif
                                </td>
                                <td>{{ $event->event_date?->format('Y-m-d') ?? '—' }}</td>
                                <td>{{ $event->location ?? '—' }}</td>
                                <td>{{ $event->price ? '$' . number_format($event->price, 2) : 'Bepul' }}</td>
                                <td class="text-center">
                                    <button class="btn btn-sm btn-warning me-1"
                                        data-bs-toggle="modal"
                                        data-bs-target="#editModal{{ $event->id }}"
                                        title="Tahrirlash">
                                        <i class="bi bi-pencil-square"></i> Tahrir
                                    </button>

                                    <button class="btn btn-sm btn-danger"
                                        data-bs-toggle="modal"
                                        data-bs-target="#deleteModal{{ $event->id }}"
                                        title="O‘chirish">
                                        <i class="bi bi-trash3"></i> O‘chirish
                                    </button>
                                </td>
                            </tr>

                            {{-- 🟡 Tahrirlash Modali (EDIT MODAL) --}}
                            <div class="modal fade" id="editModal{{ $event->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
                                    <div class="modal-content">
                                        <form action="{{ route('admin.events.update', $event->id) }}" method="POST" enctype="multipart/form-data">
                                            @csrf
                                            @method('PUT')
                                            <div class="modal-header">
                                                <h5 class="modal-title">Tadbirni Tahrirlash: "{{ Str::limit($event->title, 30) }}"</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="row">
                                                    <div class="col-md-4">
                                                        <label class="form-label">Sarlavha (UZ) <span class="text-danger">*</span></label>
                                                        <input type="text" name="title_uz" class="form-control" value="{{ old('title_uz', $event->title_uz) }}" required>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-label">Sarlavha (RU) <span class="text-danger">*</span></label>
                                                        <input type="text" name="title_ru" class="form-control" value="{{ old('title_ru', $event->title_ru) }}" required>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-label">Sarlavha (EN) <span class="text-danger">*</span></label>
                                                        <input type="text" name="title_en" class="form-control" value="{{ old('title_en', $event->title_en) }}" required>
                                                    </div>
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

                                                <div class="row">
                                                    <div class="col-md-4 mb-3">
                                                        <label class="form-label">Tavsif (UZ)</label>
                                                        <textarea name="description_uz" class="form-control" rows="3">{{ old('description_uz', $event->description_uz) }}</textarea>
                                                    </div>
                                                    <div class="col-md-4 mb-3">
                                                        <label class="form-label">Tavsif (EN)</label>
                                                        <textarea name="description_en" class="form-control" rows="3">{{ old('description_en', $event->description_en) }}</textarea>
                                                    </div>
                                                    <div class="col-md-4 mb-3">
                                                        <label class="form-label">Tavsif (RU)</label>
                                                        <textarea name="description_ru" class="form-control" rows="3">{{ old('description_ru', $event->description_ru) }}</textarea>
                                                    </div>
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label">Rasm (Muqova)</label><br>
                                                    @if($event->image)
                                                        <img src="{{ asset('storage/' . $event->image) }}" width="100" class="rounded border mb-2 object-fit-cover">
                                                        <small class="d-block text-muted">Hozirgi rasm. Yangisini yuklamasangiz o'zgarmaydi.</small>
                                                    @endif
                                                    <input type="file" name="image" class="form-control mt-1">
                                                </div>

                                                <div class="row">
                                                    <div class="col-md-4 mb-3">
                                                        <label class="form-label">Tadbir sanasi</label>
                                                        <input type="date" name="event_date" class="form-control"
                                                            value="{{ old('event_date', $event->event_date?->format('Y-m-d')) }}">
                                                    </div>
                                                    <div class="col-md-4 mb-3">
                                                        <label class="form-label">Vaqt</label>
                                                        <input type="time" name="time" class="form-control" value="{{ old('time', $event->time) }}">
                                                    </div>
                                                    <div class="col-md-4 mb-3">
                                                        <label class="form-label">Narx ($)</label>
                                                        <input type="number" step="0.01" name="price" class="form-control" value="{{ old('price', $event->price) }}">
                                                    </div>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Joylashuv</label>
                                                    <input type="text" name="location" class="form-control" value="{{ old('location', $event->location) }}">
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Bekor qilish</button>
                                                <button type="submit" class="btn btn-success">Yangilash</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            {{-- 🔴 O'chirishni Tasdiqlash Modali (DELETE MODAL) --}}
                            <div class="modal fade" id="deleteModal{{ $event->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-sm modal-dialog-centered">
                                    <div class="modal-content">
                                        <form action="{{ route('admin.events.destroy', $event->id) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <div class="modal-header">
                                                <h5 class="modal-title"><i class="bi bi-exclamation-triangle-fill"></i> O‘chirish</h5>
                                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <p class="fw-bold">Haqiqatan ham <strong>{{ Str::limit($event->title, 25) }}</strong> tadbirini o‘chirmoqchimisiz?</p>
                                                <p class="text-danger small">Ushbu harakatni bekor qilib bo‘lmaydi.</p>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Bekor</button>
                                                <button type="submit" class="btn btn-danger"><i class="bi bi-trash3"></i> O‘chirish</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>

<div class="modal fade" id="createModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <form action="{{ route('admin.events.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Yangi Tadbir Qo‘shish</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label">Sarlavha (UZ) <span class="text-danger">*</span></label>
                            <input type="text" name="title_uz" class="form-control" value="{{ old('title_uz') }}" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Sarlavha (RU) <span class="text-danger">*</span></label>
                            <input type="text" name="title_ru" class="form-control" value="{{ old('title_ru') }}" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Sarlavha (EN) <span class="text-danger">*</span></label>
                            <input type="text" name="title_en" class="form-control" value="{{ old('title_en') }}" required>
                        </div>
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

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Tavsif (UZ)</label>
                            <textarea name="description_uz" class="form-control" rows="3">{{ old('description_uz') }}</textarea>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Tavsif (EN)</label>
                            <textarea name="description_en" class="form-control" rows="3">{{ old('description_en') }}</textarea>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Tavsif (RU)</label>
                            <textarea name="description_ru" class="form-control" rows="3">{{ old('description_ru') }}</textarea>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Rasm (Muqova) <span class="text-danger">*</span></label>
                        <input type="file" name="image" class="form-control" required>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Tadbir sanasi</label>
                            <input type="date" name="event_date" class="form-control" value="{{ old('event_date') }}">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Vaqt</label>
                            <input type="time" name="time" class="form-control" value="{{ old('time') }}">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Narx (UZS)</label>
                            <input type="number" step="0.01" name="price" class="form-control" value="{{ old('price') }}">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Joylashuv</label>
                        <input type="text" name="location" class="form-control" value="{{ old('location') }}">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Bekor qilish</button>
                    <button type="submit" class="btn btn-primary">Saqlash</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection