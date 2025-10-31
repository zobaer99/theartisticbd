@extends('master.back')

@section('content')

<div class="container-fluid">

    <!-- Page Heading -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="d-sm-flex align-items-center justify-content-between">
                <h3 class="mb-0"><b>{{ __('Create Video') }}</b></h3>
                <a class="btn btn-primary btn-sm" href="{{ route('back.video-stories.index') }}">
                    <i class="fas fa-chevron-left"></i> {{ __('Back') }}
                </a>
            </div>
        </div>
    </div>

    <!-- Form -->
    <div class="row">
        <div class="col-xl-12">
            <div class="card o-hidden border-0 shadow-lg">
                <div class="card-body">
                    <div class="row justify-content-center">
                        <div class="col-lg-12">
                            <form class="admin-form" action="{{ route('back.video-stories.store') }}" method="POST">
                                @csrf

                                @include('alerts.alerts')

                                <div class="form-group">
                                    <label for="title">{{ __('Title') }}</label>
                                    <input type="text" name="title" class="form-control" id="title"
                                        placeholder="{{ __('Enter Title') }}" value="{{ old('title') }}">
                                </div>

                                <div class="form-group">
                                    <label for="youtube_url">{{ __('YouTube Link') }} *</label>
                                    <input type="url" name="youtube_url" class="form-control" id="youtube_url"
                                        placeholder="https://www.youtube.com/watch?v=XXXXXX" value="{{ old('youtube_url') }}" required>
                                </div>

                                <div class="form-group">
                                    <label for="details">{{ __('Details') }}</label>
                                    <textarea name="details" id="details" class="form-control" rows="4"
                                        placeholder="{{ __('Enter Details') }}">{{ old('details') }}</textarea>
                                </div>

                                <div class="form-group">
                                    <button type="submit" class="btn btn-secondary">{{ __('Submit') }}</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

@endsection
