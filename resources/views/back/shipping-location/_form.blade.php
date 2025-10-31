@csrf
@include('alerts.alerts')

<div class="form-group">
    <label for="title">{{ __('Title') }} *</label>
    <input type="text" name="title" id="title" class="form-control" placeholder="{{ __('Enter Title') }}" value="{{ old('title', $shipping_location->title ?? '') }}" required>
</div>

<div class="form-row">
    <div class="form-group col-md-4">
        <label for="division_id">{{ __('Division') }}</label>
        <select name="division_id" id="division_id" class="form-control select2" data-placeholder="-- {{ __('Select Division') }} --">
            <option value="">-- {{ __('Select Division') }} --</option>
            @foreach($divisions as $division)
                <option value="{{ $division->id }}" {{ (int) old('division_id', $shipping_location->division_id ?? 0) === $division->id ? 'selected' : '' }}>{{ $division->name }}</option>
            @endforeach
        </select>
    </div>
    <div class="form-group col-md-4">
        <label for="district_id">{{ __('District') }}</label>
        <select name="district_id" id="district_id" class="form-control select2" data-placeholder="-- {{ __('Select District') }} --">
            <option value="">-- {{ __('Select District') }} --</option>
            @isset($districts)
                @foreach($districts as $district)
                    <option value="{{ $district->id }}" {{ (int) old('district_id', $shipping_location->district_id ?? 0) === $district->id ? 'selected' : '' }}>{{ $district->name }}</option>
                @endforeach
            @endisset
        </select>
    </div>
    <div class="form-group col-md-4">
        <label for="thana_id">{{ __('Thana') }}</label>
        <select name="thana_id" id="thana_id" class="form-control select2" data-placeholder="-- {{ __('Select Thana') }} --">
            <option value="">-- {{ __('Select Thana') }} --</option>
            @isset($thanas)
                @foreach($thanas as $thana)
                    <option value="{{ $thana->id }}" {{ (int) old('thana_id', $shipping_location->thana_id ?? 0) === $thana->id ? 'selected' : '' }}>{{ $thana->name }}</option>
                @endforeach
            @endisset
        </select>
    </div>
</div>

<div class="form-group">
    <label for="price">{{ __('Shipping Cost') }} *</label>
    <div class="input-group mb-3">
        <div class="input-group-prepend"><span class="input-group-text">{{ PriceHelper::adminCurrency() }}</span></div>
        <input type="number" step="0.01" min="0" name="price" id="price" class="form-control" placeholder="{{ __('Enter Price') }}" value="{{ old('price', isset($shipping_location) ? PriceHelper::setPrice($shipping_location->price) : '') }}" required>
    </div>
</div>

<div class="form-group">
    <label for="status">{{ __('Status') }}</label>
    <select name="status" id="status" class="form-control">
        <option value="1" {{ (string) old('status', $shipping_location->status ?? 1) === '1' ? 'selected' : '' }}>{{ __('Enabled') }}</option>
        <option value="0" {{ (string) old('status', $shipping_location->status ?? 1) === '0' ? 'selected' : '' }}>{{ __('Disabled') }}</option>
    </select>
    <small class="form-text text-muted">{{ __('Choose whether this shipping price is active.') }}</small>
</div>

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/back/css/select2.css') }}">
@endpush

@push('scripts')
<script src="{{ asset('assets/back/js/select2.js') }}"></script>
<script>
(function(){
    if (window.__shippingLocInit) return; // avoid double init
    window.__shippingLocInit = true;
    var $division = $('#division_id');
    var $district = $('#district_id');
    var $thana = $('#thana_id');

    function initSelect2($el){
        if ($.fn.select2) {
            $el.select2({
                width: '100%',
                placeholder: $el.data('placeholder') || '',
                allowClear: true,
                minimumResultsForSearch: 0
            });
        }
    }

    initSelect2($division);
    initSelect2($district);
    initSelect2($thana);

    function resetOptions($el, placeholder){
        $el.empty().append(new Option(placeholder, '', true, false)).trigger('change');
    }

    $division.on('change', function(){
        var id = $(this).val();
        resetOptions($district, '-- {{ __('Select District') }} --');
        resetOptions($thana, '-- {{ __('Select Thana') }} --');
        if(!id){ return; }
        $.get("{{ route('back.shipping-location.districts') }}", { division_id: id })
            .done(function(list){
                list.forEach(function(x){
                    var opt = new Option(x.name, x.id, false, false);
                    $district.append(opt);
                });
                // Refresh select2 if present, else trigger native change
                if ($district.data('select2')) { $district.trigger('change.select2'); } else { $district.trigger('change'); }
            })
            .fail(function(xhr){
                if (window.console) console.error('Failed to load districts', xhr.status, xhr.responseText);
            });
    });

    $district.on('change', function(){
        var id = $(this).val();
        resetOptions($thana, '-- {{ __('Select Thana') }} --');
        if(!id){ return; }
        $.get("{{ route('back.shipping-location.thanas') }}", { district_id: id })
            .done(function(list){
                list.forEach(function(x){
                    var opt = new Option(x.name, x.id, false, false);
                    $thana.append(opt);
                });
                if ($thana.data('select2')) { $thana.trigger('change.select2'); } else { $thana.trigger('change'); }
            })
            .fail(function(xhr){
                if (window.console) console.error('Failed to load thanas', xhr.status, xhr.responseText);
            });
    });
})();
</script>
@endpush
{{-- Removed --}}
