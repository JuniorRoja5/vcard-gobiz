@php
    use App\Theme;

    // Where theme_id "588969111125" to "588969111146"
    $themes = Theme::whereBetween('theme_id', ['588969111125', '588969111146'])
        ->where('status', '1')
        ->get();
@endphp

<section class="pt-24 pb-12 lg:px-24 overflow-hidden">
    <div class="container mx-auto px-4">
        <div class="mx-auto">
            <p class="text-{{ $config[11]->config_value }}-600 font-bold text-center">
                {{ __('Themes') }}
            </p>
            <h2 class="mb-5 font-heading font-bold text-center text-6xl sm:text-7xl text-gray-900">
                {{ __('Find Your Perfect Theme') }}
            </h2>
            <p class="mb-20 font-heading text-xs text-gray-600 font-semibold text-center uppercase tracking-px">
                {{ __('Personalize Your Digital Identity with Our Stunning Collection') }}
            </p>
        </div>
        <div class="relative">
            <div class="swiper mySwiper">
                <div class="swiper-wrapper">
                    {{-- Themes --}}
                    @foreach ($themes as $theme)
                        <div class="swiper-slide group relative">
                            <div class="absolute inset-0 bg-white/40 backdrop-blur-2xl"></div>
                            <img src="{{ asset('img/vCards/' . $theme->theme_thumbnail) }}" alt="{{ $theme->theme_name }}"
                                class="w-full transition-transform duration-300 group-hover:scale-105 shadow-lg shadow-white relative z-10 rounded-2xl">
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>

<script>
    const swiper = new Swiper('.mySwiper', {
        slidesPerView: 5,
        spaceBetween: 20,
        slideToClickedSlide: true,
        centeredSlides: true,
        effect: "coverflow",
        grabCursor: true,
        coverflowEffect: {
            rotate: -5,
            stretch: 5,
            depth: 120,
            modifier: 1,
            slideShadows: true,
        },
        loop: true,
        autoplay: {
            delay: 2000,
        },
        keyboard: {
            enabled: true,
            onlyInViewport: true,
        },
        breakpoints: {
            1024: {
                slidesPerView: 4,
            },
            768: {
                slidesPerView: 2,
            },
            320: {
                slidesPerView: 1,
            },
        },
    });
</script>