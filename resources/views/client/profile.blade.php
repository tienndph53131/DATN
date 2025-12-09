@extends('layouts.partials.client')

@section('title','Thông tin cá nhân')

@section('content')
@php
    $address_id = $address->id ?? null;
    $province_id = $address->province_id ?? null;
    $district_id = $address->district_id ?? null;
    $ward_id = $address->ward_id ?? null;
    $address_detail = $address->address_detail ?? '';
@endphp

<div class="container py-5">
    <h2 class="mb-4">Thông tin cá nhân</h2>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form action="{{ route('profile.update') }}" method="POST">
        @csrf

        <!-- Thông tin cá nhân -->
        <div class="mb-3">
            <label>Họ tên</label>
            <input type="text" name="name" value="{{ $account->name }}" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Ngày sinh</label>
            <input type="date" name="birthday" value="{{ $account->birthday }}" class="form-control">
        </div>

        <div class="mb-3">
            <label>Email</label>
            <input type="email" name="email" value="{{ $account->email }}" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Điện thoại</label>
            <input type="number" name="phone" value="{{ $account->phone }}" class="form-control">
        </div>

        <div class="mb-3">
            <label>Giới tính</label>
            <select name="sex" class="form-control">
                <option value="male" {{ $account->sex=='male'?'selected':'' }}>Nam</option>
                <option value="female" {{ $account->sex=='female'?'selected':'' }}>Nữ</option>
                <option value="other" {{ $account->sex=='other'?'selected':'' }}>Khác</option>
            </select>
        </div>

        <!-- Địa chỉ -->
        <h4>Địa chỉ</h4>

        <div class="mb-3">
            <label>Tỉnh/Thành phố</label>
            <select id="province" name="province_id" class="form-control">
                <option value="">Chọn Tỉnh/Thành phố</option>
                @foreach($provinces as $p)
                    <option value="{{ $p['ProvinceID'] }}" {{ $province_id == $p['ProvinceID'] ? 'selected' : '' }}>
                        {{ $p['ProvinceName'] }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label>Quận/Huyện</label>
            <select id="district" name="district_id" class="form-control">
               <option value="{{ $district_id ?? '' }}">{{ $address->district_name ?? 'Chọn Quận/Huyện' }}</option>

            </select>
        </div>

        <div class="mb-3">
            <label>Xã/Phường</label>
            <select id="ward" name="ward_id" class="form-control">
                <option value="{{ $ward_id ?? '' }}">{{ $address->ward_name ?? 'Chọn Xã/Phường' }}</option>
            </select>
        </div>

        <div class="mb-3">
            <label>Địa chỉ chi tiết</label>
            <input type="text" name="address_detail" value="{{ $address_detail }}" class="form-control" placeholder="Số nhà, đường, khu vực">
        </div>

        <button type="submit" class="btn btn-primary">Cập nhật</button>
    </form>
</div>
@endsection

@section('scripts')
<script>
const provinceSelect = document.getElementById('province');
const districtSelect = document.getElementById('district');
const wardSelect = document.getElementById('ward');

const selectedDistrict = "{{ $district_id }}";
const selectedWard = "{{ $ward_id }}";

// Load districts khi chọn province
function loadDistricts(provinceId, selected = null){
    districtSelect.innerHTML = '<option value="">Chọn Quận/Huyện</option>';
    wardSelect.innerHTML = '<option value="">Chọn Xã/Phường</option>';
    if(!provinceId) return;

    fetch("{{ route('profile.districts') }}?province_id=" + provinceId)

    .then(res => res.json())
    .then(data => {
    const districts = Array.isArray(data) ? data : (data.data || []);
    if(districts.length > 0){
        districts.forEach(d => {
            let option = document.createElement('option');
            option.value = d.DistrictID.toString();
            option.text = d.DistrictName;
            if(selected && selected.toString() === d.DistrictID.toString()) option.selected = true;
            districtSelect.appendChild(option);
        });
        const currentDistrict = selected ? selected.toString() : districtSelect.value;
        if(currentDistrict) loadWards(currentDistrict, selectedWard);
    } else {
        console.warn("Không có quận nào trong dữ liệu:", data);
    }
})

.catch(err=>{
        console.error('District load failed:', err);
        // Fallback data
        let option = document.createElement('option');
        option.value = selectedDistrict;
        option.text = districtSelect.options[0].text || 'Chọn Quận/Huyện';
        option.selected = true;
        districtSelect.appendChild(option);
    });
}

// Load wards khi chọn district
function loadWards(districtId, selected = null){
    wardSelect.innerHTML = '<option value="">Chọn Xã/Phường</option>';
    if(!districtId) return;

    fetch("{{ route('profile.wards') }}?district_id=" + districtId)
    .then(res => res.json())
    .then(data => {
    const wards = Array.isArray(data) ? data : (data.data || []);
    if(wards.length > 0){
        wards.forEach(w => {
            let option = document.createElement('option');
            option.value = w.WardCode;
            option.text = w.WardName;
            if(selected && selected === w.WardCode) option.selected = true;
            wardSelect.appendChild(option);
        });
    } else {
        console.warn("Không có xã nào trong dữ liệu:", data);
    }
})
.catch(err=>{
        console.error('Ward load failed:', err);
        let option = document.createElement('option');
        option.value = selectedWard;
        option.text = wardSelect.options[0].text || 'Chọn Xã/Phường';
        option.selected = true;
        wardSelect.appendChild(option);
    });
}

// Event change
provinceSelect.addEventListener('change', e => loadDistricts(e.target.value));
districtSelect.addEventListener('change', e => loadWards(e.target.value));

// Load initial khi edit page
if(provinceSelect.value){
    loadDistricts(provinceSelect.value, selectedDistrict);
}
</script>

@endsection
