@extends('admin.adminhome')

@section('content')
{{-- 1. WELCOME BANNER --}}
<div class="row mb-4" data-aos="fade-down">
    <div class="col-12">
        <div class="card border-0 shadow-sm" style="background: linear-gradient(135deg, #5b1a1a 0%, #3f0e0e 100%); border-radius: 12px; overflow: hidden;">
            <div class="card-body p-4 text-left"> {{-- ✅ Aligned Left --}}
                <div class="row align-items-center">
                    {{-- LEFT: Greetings --}}
                    <div class="col-lg-7">
                        <h2 class="text-white font-weight-bold mb-1" style="font-family: 'Amiri', serif; line-height: 1.4;">
                            السلام عليكم ورحمة الله وبركاته <span style="font-size: 1.5rem;">👋</span>
                        </h2>
                        <p class="text-white-50 mb-0" style="font-size: 1.1rem;">
                            Welcome back, {{ Auth::user()->name }}!
                        </p>
                    </div>

                    {{-- RIGHT: Clock & Hijri Date --}}
                    <div class="col-lg-5 text-lg-right mt-3 mt-lg-0">
                        <div class="d-inline-flex align-items-center p-3 rounded" style="background: rgba(255,255,255,0.1); backdrop-filter: blur(5px);">
                            {{-- Hijri Date --}}
                            <div class="mr-4 text-right border-right pr-4" style="border-color: rgba(255,255,255,0.2) !important;">
                                <div class="text-warning small text-uppercase font-weight-bold mb-0">Hijri Date</div>
                                <div class="text-white h5 font-weight-bold mb-0" id="hijri-date-banner" style="font-family: 'Amiri', serif;">
                                    <i class="fas fa-spinner fa-spin"></i>
                                </div>
                            </div>
                            
                            {{-- Digital Clock --}}
                            <div class="text-right">
                                <div class="text-white-50 small mb-0" id="current-date">Loading...</div>
                                <div class="text-white h3 font-weight-bold mb-0" id="current-time" style="font-family: 'Inter', sans-serif;">00:00:00</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- 2. STATISTICS --}}
<div class="row mb-4 text-left">
    <div class="col-md-4" data-aos="fade-up" data-aos-delay="100">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center">
                <div class="rounded-circle d-flex align-items-center justify-content-center mr-3" style="width: 50px; height: 50px; background-color: #fef3c7; color: #b45309;">
                    <i class="fas fa-user-graduate fa-lg"></i>
                </div>
                <div>
                    <h5 class="font-weight-bold mb-0 text-dark">Total Students</h5>
                    <small class="text-muted">Manage your classes</small>
                </div>
                <div class="ml-auto">
                    <span class="h4 font-weight-bold text-dark">{{ \App\Models\User::where('role', 'student')->count() }}</span>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4" data-aos="fade-up" data-aos-delay="200">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center">
                <div class="rounded-circle d-flex align-items-center justify-content-center mr-3" style="width: 50px; height: 50px; background-color: #dcfce7; color: #15803d;">
                    <i class="fas fa-layer-group fa-lg"></i>
                </div>
                <div>
                    <h5 class="font-weight-bold mb-0 text-dark">Active Groups</h5>
                    <small class="text-muted">Form 4 Classes</small>
                </div>
                <div class="ml-auto">
                    <span class="h4 font-weight-bold text-dark">{{ \App\Models\Group::count() }}</span>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4" data-aos="fade-up" data-aos-delay="300">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center">
                <div class="rounded-circle d-flex align-items-center justify-content-center mr-3" style="width: 50px; height: 50px; background-color: #e0f2fe; color: #0369a1;">
                    <i class="fas fa-book fa-lg"></i>
                </div>
                <div>
                    <h5 class="font-weight-bold mb-0 text-dark">Resources</h5>
                    <small class="text-muted">Uploaded materials</small>
                </div>
                <div class="ml-auto">
                    <span class="h4 font-weight-bold text-dark">{{ \App\Models\Resources::count() }}</span>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- 3. PRAYER WIDGET --}}
<div class="row mb-5">
    <div class="col-12" data-aos="fade-up" data-aos-delay="400">
        <div class="card border shadow-sm">
            <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center flex-wrap">
                <div class="d-flex align-items-center">
                    <h4 class="card-title mb-0 mr-3" style="font-family: 'Amiri', serif; color: #5b1a1a;">
                        <i class="fas fa-mosque mr-2 text-warning"></i> Waktu Solat
                    </h4>
                    
                    <select id="location-select" class="form-control form-control-sm border-0 bg-light" style="width: auto; font-weight: 600; color: #5b1a1a; cursor: pointer;">
                        <optgroup label="Zon Melaka 1 (Jasin & Merlimau)">
                            <option value="2.3133,102.4309" selected>📍 Jasin (Bandar)</option>
                            <option value="2.1460,102.4250">📍 Merlimau</option>
                            <option value="2.2700,102.3800">📍 Bemban</option>
                            <option value="2.2230,102.4540">📍 Nyalas</option>
                        </optgroup>
                        <optgroup label="Zon Melaka 2 (Melaka Tengah)">
                            <option value="2.1896,102.2501">📍 Bandar Melaka</option>
                            <option value="2.2775,102.1466">📍 Sungai Udang (MRSM Terendak)</option>
                            <option value="2.2738,102.2858">📍 Ayer Keroh</option>
                            <option value="2.2470,102.2870">📍 Batu Berendam</option>
                        </optgroup>
                        <optgroup label="Zon Melaka 3 (Alor Gajah)">
                            <option value="2.3804,102.2089">📍 Alor Gajah (Bandar)</option>
                            <option value="2.3500,102.1100">📍 Masjid Tanah</option>
                        </optgroup>
                    </select>
                </div>
                <div class="small text-muted font-weight-bold">JAKIM Malaysia Calibration</div>
            </div>
            
            <div class="card-body">
                <div class="text-left py-4" id="prayer-loading">
                    <div class="spinner-border text-secondary" role="status"></div>
                    <p class="mt-2 text-muted">Loading accurate JAKIM times...</p>
                </div>
                {{-- Row for cards, left aligned --}}
                <div class="row text-left d-none" id="prayer-cards"></div>
            </div>
        </div>
    </div>
</div>

{{-- 4. CALENDAR --}}
<div class="row mb-5">
    <div class="col-12" data-aos="fade-up" data-aos-delay="500">
        <div class="card border shadow-sm h-100">
            <div class="card-header bg-white border-bottom text-left">
                <h4 class="card-title mb-0" style="font-family: 'Amiri', serif; color: #5b1a1a;">
                    <i class="far fa-calendar-alt mr-2 text-primary"></i> School Calendar
                </h4>
            </div>
            <div class="card-body p-0">
                <div class="embed-responsive embed-responsive-16by9" style="height: 600px;">
                    <iframe src="https://calendar.google.com/calendar/embed?height=600&wkst=1&bgcolor=%23ffffff&ctz=Asia%2FKuala_Lumpur&src=ZW4ubWFsYXlzaWEjaG9saWRheUBncm91cC52LmNhbGVuZGFyLmdvb2dsZS5jb20&color=%230B8043&showTitle=0&showNav=1&showDate=1&showPrint=0&showTabs=1&showCalendars=0" 
                        style="border-width:0" width="100%" height="600" frameborder="0" scrolling="no">
                    </iframe>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .prayer-card { background-color: #f8f9fa; transition: all 0.3s ease; text-align: left; }
    .prayer-card:hover { transform: translateY(-5px); background-color: #fff; box-shadow: 0 5px 15px rgba(0,0,0,0.1); border-color: #fbbf24 !important; }
    #location-select:focus { box-shadow: none; background-color: #e9ecef; }
</style>

<script>
    // 1. LIVE CLOCK
    function updateTime() {
        const now = new Date();
        document.getElementById('current-date').textContent = now.toLocaleDateString('en-US', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });
        document.getElementById('current-time').textContent = now.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
    }
    setInterval(updateTime, 1000); updateTime();

    // 2. PRAYER API FETCH (MALAYSIA JAKIM METHOD)
    function fetchPrayerTimes(lat, long) {
        document.getElementById('prayer-loading').classList.remove('d-none');
        document.getElementById('prayer-cards').classList.add('d-none');

        // ✅ URL parameters updated: method=3 (JAKIM), fajrAngle=20, ishaAngle=18, tune (3 min buffer for Subuh)
        fetch(`https://api.aladhan.com/v1/timings?latitude=${lat}&longitude=${long}&method=3&fajrAngle=20&ishaAngle=18&tune=0,3,0,0,0,0,0,0,0`)
            .then(res => res.json())
            .then(data => {
                const t = data.data.timings;
                const h = data.data.date.hijri;
                
                // Update Hijri Banner
                const hijriBanner = document.getElementById('hijri-date-banner');
                if(hijriBanner) {
                    hijriBanner.innerHTML = `<i class="fas fa-moon text-warning mr-1"></i> ${h.day} ${h.month.en} ${h.year}`;
                }

                const pList = [
                    {k:'Fajr', l:'Subuh', i:'cloud-sun'}, {k:'Dhuhr', l:'Zohor', i:'sun'}, 
                    {k:'Asr', l:'Asar', i:'sun'}, {k:'Maghrib', l:'Maghrib', i:'moon'}, 
                    {k:'Isha', l:'Isyak', i:'moon'}
                ];

                let html = '';
                pList.forEach(p => {
                    html += `
                    <div class="col-6 col-md-2"> {{-- ✅ Using col-md-2 for left-side stacking --}}
                        <div class="p-3 rounded border mb-3 prayer-card">
                            <i class="fas fa-${p.i} text-warning mb-2 fa-lg"></i>
                            <div class="small font-weight-bold text-muted text-uppercase">${p.l}</div>
                            <div class="h4 font-weight-bold text-dark mb-0">${t[p.k]}</div>
                        </div>
                    </div>`;
                });

                document.getElementById('prayer-cards').innerHTML = html;
                document.getElementById('prayer-loading').classList.add('d-none');
                document.getElementById('prayer-cards').classList.remove('d-none');
            })
            .catch(error => console.error('Error:', error));
    }

    // 3. INITIALIZE
    document.addEventListener('DOMContentLoaded', function() {
        const locSelect = document.getElementById('location-select');
        let lat = '2.3133';
        let long = '102.4309';

        if(localStorage.getItem('prayerLoc')) {
            const saved = localStorage.getItem('prayerLoc');
            locSelect.value = saved;
            const coords = saved.split(',');
            lat = coords[0];
            long = coords[1];
        }

        fetchPrayerTimes(lat, long);

        locSelect.addEventListener('change', function() {
            localStorage.setItem('prayerLoc', this.value);
            const c = this.value.split(',');
            fetchPrayerTimes(c[0], c[1]);
        });
    });
</script>
@endsection