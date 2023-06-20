@extends('layout')

@section('content')
    <div class="grid grid-cols-1">
        <div class="p-12">
            <div class="m-3">
                <div class="container mb-10 mt-8">
                    <div class="row col-12">
                        <div class="col-9">
                            <h3>Select lesson time</h3>
                        </div>
                    </div>
                    <table class="table bg-white table-hover">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>Category</th>
                            <th>Time (Hours)</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($categories as $index => $category)
                            <tr>
                                <td>{{$index + 1}}</td>
                                <td>{{$category->name}}</td>
                                <td>
                                    <div class="row col-12">
                                        <div class="col-1 mt-4">
                                            <span>{{$category->minimumRange()}}</span>
                                        </div>
                                        <div class="col-10">
                                            @php
                                                $defaultSelection = $category->minimumRange();
                                                if (config('app.default_to_minimum') == false) {
                                                    $defaultSelection = rand($category->minimumRange(), $category->maximumRange());
                                                }
                                            @endphp
                                            <span id="outer{{$index}}" class="text-center d-flex justify-content-center">{{$defaultSelection}}</span>
                                            <input
                                                type="range"
                                                name="category_id"
                                                data-category="{{$category->id}}"
                                                class="form-range input-range"
                                                min="{{$category->minimumRange()}}"
                                                max="{{$category->maximumRange()}}"
                                                id="customRange{{$index}}"
                                                value="{{$defaultSelection}}"
                                                oninput="slide({{$index}})"
                                                onchange="slide({{$index}}, true)"
                                                step="0.01"
                                            >
                                        </div>
                                        <div class="col-1 mt-4">
                                            <span>{{$category->maximumRange()}}</span>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                    <input type="hidden" id="is_auto_load_records" value="{{config('app.default_slider_output')}}"/>
                </div>
                @foreach($categories as $category)
                    <div class="mt-5 text-gray-600" id="category-list-{{$category->id}}"></div>
                @endforeach
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('js/main.js') }}"></script>
@endpush
