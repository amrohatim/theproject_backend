@extends('layouts.app-with-language')

@section('title', __('messages.language_demo'))

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <h1 class="card-title mb-0">
                    <i class="fas fa-language me-2"></i>
                    @lang('messages.language_demo')
                </h1>
            </div>
            <div class="card-body">
                <!-- Language Information -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="card border-info">
                            <div class="card-header bg-info text-white">
                                <h5 class="mb-0">@lang('messages.current_language_info')</h5>
                            </div>
                            <div class="card-body">
                                <ul class="list-unstyled mb-0">
                                    <li><strong>@lang('messages.locale'):</strong> {{ $currentLocale }}</li>
                                    <li><strong>@lang('messages.name'):</strong> {{ $currentLanguage['name'] }}</li>
                                    <li><strong>@lang('messages.native_name'):</strong> {{ $currentLanguage['native'] }}</li>
                                    <li><strong>@lang('messages.direction'):</strong> {{ $direction }}</li>
                                    <li><strong>@lang('messages.text_alignment'):</strong> {{ $textAlign }}</li>
                                    <li><strong>@lang('messages.is_rtl'):</strong> 
                                        @if($isRtl)
                                            <span class="badge bg-success">@lang('messages.yes')</span>
                                        @else
                                            <span class="badge bg-secondary">@lang('messages.no')</span>
                                        @endif
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="card border-success">
                            <div class="card-header bg-success text-white">
                                <h5 class="mb-0">@lang('messages.supported_languages')</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    @foreach($supportedLocales as $locale => $details)
                                        <div class="col-6 mb-2">
                                            <div class="d-flex align-items-center">
                                                <span class="me-2" style="font-size: 1.5em;">{{ $details['flag'] }}</span>
                                                <div>
                                                    <strong>{{ $details['native'] }}</strong><br>
                                                    <small class="text-muted">{{ $details['name'] }} ({{ $locale }})</small>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Sample Content -->
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="card border-warning">
                            <div class="card-header bg-warning text-dark">
                                <h5 class="mb-0">@lang('messages.sample_content')</h5>
                            </div>
                            <div class="card-body">
                                <h3>@lang('messages.welcome_message')</h3>
                                <p class="lead">@lang('messages.sample_paragraph')</p>
                                
                                <div class="row">
                                    <div class="col-md-4">
                                        <h5>@lang('messages.navigation')</h5>
                                        <ul class="list-group">
                                            <li class="list-group-item">@lang('messages.home')</li>
                                            <li class="list-group-item">@lang('messages.about')</li>
                                            <li class="list-group-item">@lang('messages.services')</li>
                                            <li class="list-group-item">@lang('messages.contact')</li>
                                        </ul>
                                    </div>
                                    
                                    <div class="col-md-4">
                                        <h5>@lang('messages.user_actions')</h5>
                                        <div class="d-grid gap-2">
                                            <button class="btn btn-primary">@lang('messages.login')</button>
                                            <button class="btn btn-success">@lang('messages.register')</button>
                                            <button class="btn btn-info">@lang('messages.profile')</button>
                                            <button class="btn btn-warning">@lang('messages.settings')</button>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-4">
                                        <h5>@lang('messages.form_elements')</h5>
                                        <form>
                                            <div class="mb-3">
                                                <label class="form-label">@lang('messages.name')</label>
                                                <input type="text" class="form-control" placeholder="@lang('messages.enter_name')">
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">@lang('messages.email')</label>
                                                <input type="email" class="form-control" placeholder="@lang('messages.enter_email')">
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">@lang('messages.message')</label>
                                                <textarea class="form-control" rows="3" placeholder="@lang('messages.enter_message')"></textarea>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Number and Date Formatting -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="card border-primary">
                            <div class="card-header bg-primary text-white">
                                <h5 class="mb-0">@lang('messages.number_formatting')</h5>
                            </div>
                            <div class="card-body">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>@lang('messages.original')</th>
                                            <th>@lang('messages.formatted')</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>1234567.89</td>
                                            <td>@localizedNumber(1234567.89)</td>
                                        </tr>
                                        <tr>
                                            <td>123456</td>
                                            <td>@arabicNumbers(123456)</td>
                                        </tr>
                                        <tr>
                                            <td>2024</td>
                                            <td>@arabicNumbers(2024)</td>
                                        </tr>
                                        <tr>
                                            <td>42</td>
                                            <td>@arabicNumbers(42)</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="card border-secondary">
                            <div class="card-header bg-secondary text-white">
                                <h5 class="mb-0">@lang('messages.date_formatting')</h5>
                            </div>
                            <div class="card-body">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>@lang('messages.format')</th>
                                            <th>@lang('messages.result')</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>Y-m-d</td>
                                            <td>@localizedDate(now(), 'Y-m-d')</td>
                                        </tr>
                                        <tr>
                                            <td>F j, Y</td>
                                            <td>@localizedDate(now(), 'F j, Y')</td>
                                        </tr>
                                        <tr>
                                            <td>l, F j, Y</td>
                                            <td>@localizedDate(now(), 'l, F j, Y')</td>
                                        </tr>
                                        <tr>
                                            <td>H:i:s</td>
                                            <td>@localizedDate(now(), 'H:i:s')</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- RTL/LTR Demonstration -->
                <div class="row">
                    <div class="col-12">
                        <div class="card border-dark">
                            <div class="card-header bg-dark text-white">
                                <h5 class="mb-0">@lang('messages.layout_demonstration')</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <h6>@lang('messages.text_alignment')</h6>
                                        <div class="border p-3 mb-3" style="text-align: {{ $textAlign }}">
                                            @lang('messages.aligned_text_sample')
                                        </div>
                                        
                                        <h6>@lang('messages.float_demonstration')</h6>
                                        <div class="border p-3 mb-3 clearfix">
                                            <div class="{{ $floatStart }} bg-primary text-white p-2 me-2" style="width: 100px;">
                                                @lang('messages.start')
                                            </div>
                                            <div class="{{ $floatEnd }} bg-success text-white p-2 ms-2" style="width: 100px;">
                                                @lang('messages.end')
                                            </div>
                                            <div class="pt-5">
                                                @lang('messages.content_flows_around')
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <h6>@lang('messages.directional_classes')</h6>
                                        <div class="border p-3">
                                            <p><strong>@lang('messages.body_classes'):</strong> <code>{{ $bodyClasses }}</code></p>
                                            <p><strong>@lang('messages.html_attributes'):</strong> <code>{!! $htmlAttributes !!}</code></p>
                                            <p><strong>@lang('messages.direction'):</strong> <code>{{ $direction }}</code></p>
                                            <p><strong>@lang('messages.text_align'):</strong> <code>{{ $textAlign }}</code></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Demo-specific JavaScript
    console.log('Language Demo Page Loaded');
    console.log('Current Locale:', '{{ $currentLocale }}');
    console.log('Is RTL:', {{ $isRtl ? 'true' : 'false' }});
    console.log('Direction:', '{{ $direction }}');
</script>
@endpush