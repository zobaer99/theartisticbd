@extends('master.back')

@section('content')

<div class="container-fluid">

	<!-- Option Heading -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="d-sm-flex align-items-center justify-content-between">
                <h3 class="mb-0 bc-title"><b>{{ __('Edit Options') }}</b> </h3>
                <a class="btn btn-primary   btn-sm" href="{{route('back.option.index',$item->id)}}"><i class="fas fa-chevron-left"></i> {{ __('Back') }}</a>
                </div>
        </div>
    </div>

	<!-- Form -->
	<div class="row">

		<div class="col-xl-12 col-lg-12 col-md-12">

			<div class="card o-hidden border-0 shadow-lg">
				<div class="card-body ">
					<!-- Nested Row within Card Body -->
					<div class="row justify-content-center">
						<div class="col-lg-12">
								<form class="admin-form" action="{{ route('back.option.update',[$item->id,$option->id]) }}"
									method="POST" enctype="multipart/form-data">

                                    @csrf

                                    @method('PUT')

									@include('alerts.alerts')

									<div class="form-group">
                                        <label for="attribute_id">{{ __('Attribute') }} *</label>
                                        <select name="attribute_id" class="form-control" id="attribute_id" >
                                            <option value="">{{ __('Select Attribute') }}</option>
                                            @foreach($attributes as $attribute)
                                            <option value="{{ $attribute->id }}" 
                                                data-name="{{ strtolower($attribute->name) }}"
                                                {{ $attribute->id == $option->attribute_id ? 'selected' : '' }}>
                                                {{ $attribute->name }}
                                            </option>
                                            @endforeach
                                        </select>
									</div>

									<div class="form-group">
										<label for="attr_name">{{ __('Name') }} *</label>
										<input type="text" name="name" class="form-control" id="attr_name"
											placeholder="{{ __('Enter Name') }}" value="{{ $option->name }}" >
									</div>

                                    <div class="form-group">
										<label for="stock">{{ __('Stock') }} *</label>
										<input type="text" name="stock" class="form-control" id="stock"
											placeholder="{{ __('Enter Stock') }}" value="{{ $option->stock }}" >
                                            <label for="unlimited">
                                                <input type="checkbox" {{$option->stock == 'unlimited' ? 'checked' : ''}} class="my-2" id="unlimited">
                                            {{__('Unlimited Stock')}}
                                            </label>
									</div>

                                    <div class="form-group">
                                        <label for="price">{{ __('Price') }} *</label>
                                        <small>({{ __('Set 0 to make it free') }})</small>
                                        <div class="input-group mb-3">
                                            <div class="input-group-prepend">
                                                <span
                                                    class="input-group-text">{{ $curr->sign }}</span>
                                            </div>
                                            <input type="text" id="price"
                                                name="price" class="form-control"
                                                placeholder="{{ __('Enter Price') }}"

                                                value="{{ PriceHelper::setPrice($option->price) }}" >
                                        </div>
                                    </div>

                                    <input type="hidden" id="attr_keyword" name="keyword" value="{{ $option->keyword }}">
                                       <div class="form-group" id="colorDiv" style="display:none;">
                                       <div class="card">
                                        <div class="card-body">
                                            <div class="form-group pb-0  mb-0">
                                                <label class="d-block">{{ __('Color Image') }}</label>
                                                </div>
            
                                            <div class="form-group pb-0 pt-0 mt-0 mb-0">
                                            <img class="admin-img lg" src="{{ $option->color_image ? url('/storage/images/color_options/'.$option->color_image) : url('/storage/images/placeholder.png') }}" >
                                            </div>
                                            <div class="form-group position-relative ">
                                                <label class="file">
                                                    <input type="file"  accept="image/*"   class="upload-photo" name="color_image"
                                                        id="file"  aria-label="File browser example">
                                                    <span
                                                        class="file-custom text-left">{{ __('Upload Image...') }}</span>
                                                </label>
                                                <br>
                                                <span class="mt-1 text-info">{{ __('Image Size Should Be 800 x 800. or square size') }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    </div>
									<div class="form-group">
										<button type="submit" class="btn btn-secondary">{{ __('Submit') }}</button>
									</div>


									<div>
								</form>
						</div>
					</div>
				</div>
			</div>

		</div>

	</div>

</div>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const select = document.getElementById('attribute_id');
    const colorDiv = document.getElementById('colorDiv');

    function toggleColorDiv() {
        const selectedOption = select.options[select.selectedIndex];
        const attrName = selectedOption.getAttribute('data-name');
        
        if (attrName === 'color') {
            colorDiv.style.display = 'block';
        } else {
            colorDiv.style.display = 'none';
        }
    }

    // Run on change
    select.addEventListener('change', toggleColorDiv);
    // Run on page load (to handle old selected value)
    toggleColorDiv();
    
    // Show color div if current selected attribute is color
    @if($option->attribute && strtolower($option->attribute->name) === 'color')
        colorDiv.style.display = 'block';
    @endif
});
</script>
@endsection
