@extends('admin.layouts.app')

@section('title', "Меню $menu->ru_title")

@section('css')
    <link rel="stylesheet" href="{{ asset('assets/js/plugins/select2/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/js/plugins/select2/select2-bootstrap.min.css') }}">
@endsection

@section('content')
    @include('admin.components.breadcrumb', [
        'list' => [
            [
                'url' => route('admin.needs.index'),
                'title' => 'Потребности'
            ],
            [
                'url' => route('admin.needs.menu', $menu->need_id),
                'title' => $menu->needType->ru_title
            ]
        ],
        'lastTitle' => $menu->ru_title
    ])
    <form action="{{ route('admin.menu.update', $menu->id) }}" method="post" enctype="multipart/form-data">
        @csrf
        @method('put')
        <input type="hidden" name="need_id" value="{{ $menu->need_id }}">
        <div class="block">
            <div class="block-header block-header-default">
                <h3 class="block-title">{{ $menu->ru_title }} <small>{{ $menu->needType->ru_title }}</small></h3>
                <div class="block-options">
                    <button class="btn btn-sm btn-alt-success" type="submit"><i class="fa fa-check"></i> Сохранить</button>
                    <button class="btn btn-sm btn-alt-success" type="submit" name="saveQuit"><i class="fa fa-check"></i> Сохранить и выйти</button>
                </div>
            </div>
            <div class="block-content">
                <div class="wizard-block">
                    <ul class="nav nav-tabs nav-tabs-block nav-fill" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" href="#wizard-simple-step1" data-toggle="tab">1. Русский</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#wizard-simple-step2" data-toggle="tab">2. Английский</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#wizard-simple-step3" data-toggle="tab">3. Узбекский</a>
                        </li>
                    </ul>
                    <!-- END Step Tabs -->

                    <!-- Steps Content -->
                    <div class="block-content block-content-full tab-content">
                        <!-- Step 1 -->
                        <div class="tab-pane active" id="wizard-simple-step1" role="tabpanel">
                            <div class="form-group @error('ru_title') is-invalid @enderror">
                                <div class="form-material floating">
                                    <label for="ru_title" @error('ru_title') class="col-form-label" @enderror>
                                    Заголовок
                                    @error('ru_title') <span class="text-danger">*</span> @enderror
                                    </label>
                                    <input class="form-control" type="text" id="ru_title" name="ru_title" value="{{ $menu->ru_title }}">
                                </div>
                                @error('ru_title') <div id="val-username-error" class="invalid-feedback animated fadeInDown">{{ $message }}</div> @enderror
                            </div>
                        </div>
                        <!-- END Step 1 -->

                        <!-- Step 2 -->
                        <div class="tab-pane" id="wizard-simple-step2" role="tabpanel">
                            <div class="form-group @error('en_title') is-invalid @enderror">
                                <div class="form-material floating">
                                    <label for="uz_title" @error('en_title') class="col-form-label" @enderror>
                                    Заголовок
                                    @error('en_title') <span class="text-danger">*</span> @enderror
                                    </label>
                                    <input class="form-control" type="text" id="en_title" name="en_title" value="{{ $menu->en_title }}">
                                </div>
                                @error('en_title') <div id="val-username-error" class="invalid-feedback animated fadeInDown">{{ $message }}</div> @enderror
                            </div>
                        </div>
                        <!-- END Step 2 -->

                        <!-- Step 3 -->
                        <div class="tab-pane" id="wizard-simple-step3" role="tabpanel">
                            <div class="form-group @error('uz_title') is-invalid @enderror">
                                <div class="form-material floating">
                                    <label for="uz_title" @error('uz_title') class="col-form-label" @enderror>
                                    Заголовок
                                    @error('uz_title') <span class="text-danger">*</span> @enderror
                                    </label>
                                    <input class="form-control" type="text" id="uz_title" name="uz_title" value="{{ $menu->uz_title }}">
                                </div>
                                @error('uz_title') <div id="val-username-error" class="invalid-feedback animated fadeInDown">{{ $message }}</div> @enderror
                            </div>
                        </div>
                        <!-- END Step 3 -->
                    </div>
                    <!-- END Steps Content -->
                </div>
                <div class="form-group">
                    <label for="image">Изображение</label>
                    @if($menu->image != null)
                        <br>
                        <img src="{{ $menu->getImage() }}" style="width: 200px;" alt="{{ $menu->ru_title }}">
                        <br>
                        <a href="{{ route('admin.menu.remove.image', $menu->id) }}" class="btn btn-danger">Удалить</a>
                        <br>
                    @endif
                    <input type="file" name="image" class="form-control">
                </div>
                <div class="form-group">
                    <div class="form-material floating">
                        <select name="categories" id="categories" class="form-control js-select2" multiple="multiple">
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" @if(in_array($category->id, categoriesIdsArray)) selected @endif>{{ $category->getTitle() }}</option>
                            @endforeach
                        </select>
                        <label for="categories">Категории</label>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection
@section('js')
    <script src="{{ asset('assets/js/plugins/select2/select2.full.min.js') }}"></script>
    <script>
        jQuery(function() {
            Codebase.helper('select2');
        });
    </script>
@endsection
