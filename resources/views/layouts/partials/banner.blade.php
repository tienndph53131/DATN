<div class="container-fluid p-0 mb-5">
    <div id="header-carousel" class="carousel slide" data-ride="carousel">
        <div class="carousel-inner">
            <div class="carousel-item active">
                <img class="w-100"
                     src="{{ asset('assets/img/banner/banner1.jpg') }}"
                     alt="Banner 1"
                     style="height:450px; object-fit:cover;">
                <div class="carousel-caption d-flex flex-column align-items-center justify-content-center">
                    <h1 class="display-4 text-white mb-3">Thời trang nam cao cấp</h1>
                    <a href="{{ url('/shop') }}" class="btn btn-primary px-4">Mua ngay</a>
                </div>
            </div>

            <div class="carousel-item">
                <img class="w-100"
                     src="{{ asset('assets/img/banner/banner2.jpg') }}"
                     alt="Banner 2"
                     style="height:450px; object-fit:cover;">
                <div class="carousel-caption d-flex flex-column align-items-center justify-content-center">
                    <h1 class="display-4 text-white mb-3">Bộ sưu tập mới nhất</h1>
                    <a href="{{ url('/shop') }}" class="btn btn-primary px-4">Khám phá</a>
                </div>
            </div>
        </div>

        <a class="carousel-control-prev" href="#header-carousel" role="button" data-slide="prev">
            <span class="carousel-control-prev-icon"></span>
        </a>
        <a class="carousel-control-next" href="#header-carousel" role="button" data-slide="next">
            <span class="carousel-control-next-icon"></span>
        </a>
    </div>
</div>
