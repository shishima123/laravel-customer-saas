<div class="kt-grid__item kt-grid__item--fluid flex-grow-0" style="margin-top: 40px; margin-bottom: 40px">
    <div class="m-auto d-flex justify-content-center align-items-center flex-column px-4">
        <h1 class="fs-52 fw-500 mb-0 text-center">{{ $heading }}</h1>
        @isset($description)
            <p class="fs-18 fw-400 mb-0 mt-3 text-center">{{ $description }}</p>
        @endisset
    </div>
</div>