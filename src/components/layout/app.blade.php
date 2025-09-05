@props(['title' => ''])

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ ($title ? $title . ' | ' : '') . config('app.name') }}</title>
    <link rel="icon" href="/favicon.ico" sizes="any">
    <link rel="icon" href="/favicon.svg" type="image/svg+xml">
    <link rel="apple-touch-icon" href="/apple-touch-icon.png">
    @php
        viteInput(['src/css/app.css', 'src/js/app.ts']);   
    @endphp
</head> 
<body class="flex flex-col items-center justify-between min-h-dvh w-full font-bangla bg-slate-50">
    <x-progress-bar />
    <header class="w-full bg-white shadow-xs shadow-rose-100 flex flex-col items-center justify-between">
        <div class="w-full border-b pb-0.5 border-red-950 border-double lg:max-w-[94%] 2xl:max-w-[1440px] space-y-2 sm:space-y-0">
            <div class="w-full grid grid-cols-1 sm:grid-cols-3 items-end px-4 lg:px-0 mt-2 sm:mt-4 gap-2 sm:gap-0">
                <div v-scope class="flex flex-row sm:flex-col text-xs justify-center">
                    <p v-text="`${getBanglaWeekDay()}, ${getGragorianToBongabdoDate()} বঙ্গাব্দ`" class="sm:text-sm"></p>
                    <p v-html="`<span class='sm:hidden ml-1'>(</span>${getEnglishDateInBangla().join()}<span class='sm:hidden'>)</span>`"></p>
                    <p class="hidden sm:block">আজকের সংবাদপত্র</p>
                </div>
                <div class="w-full flex h-full items-center justify-center">
                    <x-ui.logo />
                </div>
                <x-ui.social-icons class="justify-self-end hidden sm:flex" />
            </div> 
            <div v-scope="{ isScrolled: false, init(el) {
                        window.addEventListener('scroll', () => {
                        this.isScrolled = window.scrollY > el.offsetTop
                    })
                } }" 
                :class="{ '!fixed top-0 left-0 bg-white z-50 shadow shadow-rose-100 !mt-0 py-2.5 lg:px-4': isScrolled, 'border-b border-red-950': !isScrolled }" 
                @@vue:mounted="init($el)" 
                class="sm:mt-4 gap-4 py-2 w-full px-4 lg:px-0 items-center relative flex justify-between"
            >
                <div class="flex gap-4 flex-row-reverse items-center w-full justify-between md:col-span-8 sm:col-span-6 sm:grid md:grid-cols-8 sm:grid-cols-6">
                    <x-ui.search/>
                    <x-nav class="sm:col-span-5 md:col-span-7"/> 
                </div>
                <div class="flex justify-end space-x-4">
                    <x-ui.link href="{{ useUrl('epaper') }}" varient="secondery" class="pt-2">
                        <span class="text-nowrap">ই-পেপার</span>
                    </x-ui.link>
                    <x-ui.link href="{{ route('login') }}" varient="primary" target="_blank">
                        Login
                    </x-ui.link>
                </div>
            </div>
        </div>
    </header>
    <main id="app" {{ $attributes->merge(['class' => 'w-full flex flex-col transition-opacity duration-300 opacity-100 py-8 lg:max-w-[94%] 2xl:max-w-[1440px]']) }}>
        {{ $slot }} 
    </main>
    <footer class="bg-white w-full inset-shadow-2xs inset-shadow-rose-100"> 
        <div class="mx-auto lg:max-w-[94%] 2xl:max-w-[1440px] w-full border-t pt-0.5 border-red-950">
            <!-- Top section: Columns -->
            <div class="border-t border-red-950 pt-10 pb-8 flex flex-col items-center sm:items-start space-y-10 w-full px-4 lg:px-0">
            <!-- About -->
                <x-ui.logo varient="icon"/>
                <div class="w-full flex flex-col sm:flex-row items-center justify-between sm:gap-20 gap-8">
                    <div class="sm:w-1/2 space-y-4">
                        <div>
                            <p class="text-lg"><b>প্রধান সম্পাদক:</b> মোঃ হাবিবুর রহমান</p>
                            <p class="text-lg"><b>প্রকাশক:</b> রিনা বেগম</p>
                            <p class="mt-4 text-slate-800 text-sm text-justify">প্রককাশক কর্তৃক ৫১,৫১১/এ  ঢাকা-১০০০ পুরান পল্টন থেকে প্রকাশিত ও সোনালী  প্রিন্টিং ২/১/এ ইডেন ভবন ১৬৭ ইনার সার্কুলার রোড মতিঝিল, ঢাকা-১০০০ থেকে মুদ্রিত।</p>
                        </div>
                        <x-ui.social-icons class="flex" />
                    </div>
                    <div class="w-full space-y-2">
                        <h3 class="text-lg font-medium">সরাসরি লিঙ্ক</h3>
                        <x-nav :fixed="false" class="text-xl font-light"/> 
                    </div>
                </div>
            </div>
 
            <!-- Bottom section: Socials and copyright -->
            <p class="border-t border-red-950 p-2 lg:px-0 text-center w-full text-red-950 text-sm">
                <span>২০২৫ &copy; ভোরের সময় কর্তৃক সর্বস্বত্ব স্বত্বাধিকার সংরক্ষিত</span>
            </p>
        </div>
    </footer>
</body>
</html>